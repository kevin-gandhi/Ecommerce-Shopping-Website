<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shipment Tracking Main Class.
 */
if( ! class_exists('Shiprocket_Woocommerce_Api') ) {
	class Shiprocket_Woocommerce_Api{

		/**
		 * Constructor of Shiprocket_Woocommerce_Api class.
		 */
		public function __construct() {
			add_action('rest_api_init', array( $this, 'user_list_api' ), 100);
		}

		/**
		 * Initialize rest api for tracking.
		 */
		public function user_list_api() {
			if( ! class_exists('Shiprocket_User_List_API') ) {
				require_once 'class-shiprocket-user-list-api.php';
			}
			$obj = new Shiprocket_User_List_API();
			$obj->register_routes();
		}
	}
}