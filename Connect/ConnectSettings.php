<?php

namespace PagBank_Split_Payment\Connect;

use PagBank_WooCommerce\Presentation\Connect;

defined( 'ABSPATH' ) || exit;

class ConnectSettings
{
    private static $instance = null;

    protected static string $env_key = 'pagbank_connect_environment';
    public static string $connect_key = 'pagbank_connect_data';

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('personal_options', array($this, 'render_select_environment'));
        add_action('personal_options_update', array($this, 'save_select_environment'));
        add_action('personal_options', array($this, 'render_connect_button'));
    }

    public function render_connect_button($user) {
        echo pagbank_split_payment()->get_template('connect-button', [
            'environment' => get_user_meta($user->ID, self::$env_key, true),
            'applications' => Connect::get_connect_applications(),
            'nonce' => wp_create_nonce( 'pagbank_woocommerce_oauth' ),
            'env_key' => self::$env_key,
            'field_key' => self::$connect_key,
        ]);
    }

    public static function get_connect_key(string $environment): string {
        return $environment . '_' . self::$connect_key;
    }

    public function save_select_environment($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }

        if (!empty($_POST[self::$env_key])) {
            update_user_meta(
                $user_id,
                self::$env_key,
                sanitize_text_field($_POST[self::$env_key])
            );
        }
    }

    public function render_select_environment($user) {
        echo pagbank_split_payment()->get_template('select-environment', [
            'field_key' => self::$env_key,
            'value' => get_user_meta($user->ID, self::$env_key, true),
        ]);
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

        wp_register_style(
			'pagbank-for-woocommerce-admin-settings',
			plugins_url( 'styles/admin-fields.css', PAGBANK_WOOCOMMERCE_FILE_PATH ),
			array(),
			PAGBANK_WOOCOMMERCE_VERSION,
			'all'
		);
		wp_enqueue_style( 'pagbank-for-woocommerce-admin-settings' );
    }
}

ConnectSettings::get_instance();
