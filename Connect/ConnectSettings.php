<?php

namespace PagBank_Split_Payment\Connect;

defined( 'ABSPATH' ) || exit;

class ConnectSettings
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        global $pagenow;

        if ( $pagenow !== 'profile.php' ) {
            return;
        }

        wp_register_script(
            'pagbank-split-payment',
            plugins_url( 'assets/dist/admin-settings.js', pagbank_split_payment()->file ),
            array( 'jquery' ),
            '1.0.0',
            true
        );

        wp_scripts()->add_data( 'pagbank-split-payment', 'pagbank_script', true );

        wp_enqueue_script( 'pagbank-split-payment' );
    }
}

ConnectSettings::get_instance();
