<?php

/**
 * Exit if accessed directly.
 * 
 * @author Deepak Sharma <depakshrma7@gmail.com>
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Common Functions class.
 */
if (!class_exists("Shiprocket_Shipping_Rates_Common") ) {
    /**
     * Holds Common Methods.
     */
    class Shiprocket_Shipping_Rates_Common {

        /**
         * Array of active plugins.
         */
        private static $active_plugins;
        /**
         * Current user details WP_User object.
         */
        private static $current_user_details;
        /**
         * Current User email id.
         */
        private static $current_user_email_id;

        /**
         * Current User meta.
         */
        private static $current_user_meta;

        /**
         * Initialize the active plugins.
         * 
         * @return null
         */
        public static function init() {

            self::$active_plugins = (array)get_option('active_plugins', array());

            if (is_multisite() )
                self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
        }

        /**
         * Check whether woocommerce is active or not.
         * 
         * @return boolean True if woocommerce is active else false.
         */
        public static function woocommerce_active_check() {

            if (!self::$active_plugins ) self::init();

            return in_array('woocommerce/woocommerce.php', self::$active_plugins) || array_key_exists('woocommerce/woocommerce.php', self::$active_plugins);
        }

        /**
         * Get current user details.
         * 
         * @return object WP_User Object
         */
        public static function get_current_user_details() {
            if (empty(self::$current_user_details) ) {
                self::$current_user_details = wp_get_current_user();
            }
            return self::$current_user_details;
        }

        /**
         * Get current user email.
         * 
         * @return string Current user email id.
         */
        public static function get_current_user_email_id() {
            if (empty(self::$current_user_email_id) ) {
                $current_user_details = self::get_current_user_details();
                self::$current_user_email_id = $current_user_details->__get('user_email');
            }
            return self::$current_user_email_id;
        }
        
        /**
         * Get User meta.
         * 
         * @return array Current user meta
         */
        public static function get_current_user_meta()
        {
            if (empty(self::$current_user_meta) ) {
                self::$current_user_meta = get_user_meta(get_current_user_id());
            }
            return self::$current_user_meta;
        }

    }
}