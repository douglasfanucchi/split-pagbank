<?php
/**
 * Plugin Name:     PagBank Split Payment
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     pagbank-split-payment
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         PagBank_Split_Payment
 */

defined( 'ABSPATH' ) || exit;

function pagbank_split_payment() {
    return PagBank_Split_Payment::get_instance();
}

class PagBank_Split_Payment {
    public string $file;
    
    private function __construct() {
        $this->load_modules();

        $this->file = __FILE__;
    }

    protected function load_modules() {
        require_once __DIR__ . '/Connect/ConnectSettings.php';
    }

    public static function get_instance() {
        static $instance = null;

        if ( null === $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    public function get_template($template, $data = []) {
        extract($data);
        $template_path = __DIR__ . '/templates/' . $template . '.php';
        $template_path = apply_filters('pagbank_split_payment_template_path', $template_path);

        if ( file_exists( $template_path ) ) {
            ob_start();
            include $template_path;
            return ob_get_clean();
        }

        return '';
    }
}

pagbank_split_payment();
