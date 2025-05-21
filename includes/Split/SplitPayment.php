<?php

namespace PagBank_Split_Payment\Split;

use PagBank_Split_Payment\Connect\ConnectSettings;
use PagBank_WooCommerce\Presentation\Connect;

class SplitPayment {
    private static $instance = null;

    private function __construct() {
        $this->add_filters();
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function add_filters() {
        add_filter('pagbank_credit_card_payment_data', array($this, 'add_split_payment_data'), 10, 3);
        add_filter('pagbank_pix_payment_data', array($this, 'add_split_payment_data'), 10, 3);
        add_filter('pagbank_boleto_payment_data', array($this, 'add_split_payment_data'), 10, 3);
    }

    public function add_split_payment_data($data, $order, $gateway) {
        $receivers = $this->get_receivers($order, $gateway->get_option('environment'));
        $receiversRequest = $this->receivers_to_request($receivers, $order->get_total());
        $receiversRequest[] = $this->get_main_receiver($receiversRequest, $gateway->get_option('environment'));

        $data['charges'][0]['splits'] = [
            'method' => 'PERCENTAGE',
            'receivers' => $receiversRequest,
        ];

        return $data;
    }

    protected function get_receivers($order, $environment) {
        $receivers = [];
        $items = $order->get_items();

        foreach($items as $item) {
            $product_id = $item->get_product_id();
            $receiversData = carbon_get_post_meta($product_id, 'recipients');
            $comission_type = carbon_get_post_meta($product_id, 'comission_type');

            foreach($receiversData as $receiverData) {
                $user_id = $receiverData['user'][0]['id'];

                if (empty($receivers[$user_id])) {
                    $pagbank_id = get_user_meta(
                        $user_id,
                        ConnectSettings::get_connect_key($environment),
                        true
                    )['account_id'];

                    $receivers[$user_id] = [
                        'pagbank_id' => $pagbank_id,
                        'amount' => 0,
                    ];
                }

                if ($comission_type === 'percentage') {
                    $receivers[$user_id]['amount'] += $this->str_to_float($receiverData['amount']) * $item->get_total() / 100;
                } else {
                    $receivers[$user_id]['amount'] += $this->str_to_float($receiverData['amount']) * $item->get_quantity();
                }
            }
        }

        return $receivers;
    }

    protected function receivers_to_request($receivers, $order_total) {
        return array_map(
            function ($index) use ($receivers, $order_total) {
                $user = $receivers[$index];
                return [
                    'account' => [
                        'id' => $user['pagbank_id']
                    ],
                    'amount' => [
                        'value' => $user['amount'] / $order_total * 100,
                    ],
                ];
            },
            array_keys($receivers)
        );
    }

    protected function get_main_receiver($receiversRequest, $environment) {
        $connect = new Connect($environment);

        $totalReceivers = array_reduce(
            $receiversRequest,
            function ($store, $receiver) {
                $store += $receiver['amount']['value'];
                return $store;
            },
            0
        );
        
        return [
            'account' => [
                'id' => $connect->get_data()['account_id'],
            ],
            'amount' => [
                'value' => 100 - $totalReceivers,
            ],
        ];
    }

    protected function str_to_float(string $value) {
        $value = str_replace(',', '.', $value);
        return (float)$value;
    }
}

SplitPayment::get_instance();
