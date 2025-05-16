<?php

namespace PagBank_Split_Payment\Metaboxes;

defined('ABSPATH') || exit;

use \Carbon_Fields\Container\Container;
use \Carbon_Fields\Field;
use PagBank_Split_Payment\Fields\ProductRecipient as ProductRecipientFields;

class ProductRecipient {
    private static $instance = null;

    private function __construct() {
        $this->load_modules();
        $this->add_actions();
    }

    protected function load_modules() {
        require_once __DIR__ . '/../Fields/Fields.php';
        require_once __DIR__ . '/../Fields/ProductRecipient.php';
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function add_actions() {
        add_action(
            'carbon_fields_register_fields',
            array($this, 'create_product_recipient_metabox')
        );

        add_action('after_setup_theme', array($this, 'register_carbon_fields'));
    }

    public function register_carbon_fields() {
            require_once __DIR__ . '/../../vendor/autoload.php';
            \Carbon_Fields\Carbon_Fields::boot();
        }

    public function create_product_recipient_metabox() {
        Container::make( 'post_meta', 'Recebedores' )
            ->where( 'post_type', '=', 'product' )
            ->set_priority( 'high' )
            ->add_fields([
                Field::make( 'radio', 'comission_type', 'Tipo de Comissão' )
                    ->set_options([
                        'flat'       => 'Fixo',
                        'percentage' => 'Porcentagem',
                    ]),
                Field::make( 'complex', 'recipients', '' )
                    ->add_fields(
                        (new ProductRecipientFields)->carbon_fields()
                    )->set_layout( 'tabbed-horizontal' )
            ]);
    }
}

ProductRecipient::get_instance();
