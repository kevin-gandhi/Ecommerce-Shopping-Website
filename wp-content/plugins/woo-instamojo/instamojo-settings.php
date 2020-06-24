<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
return array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'woocommerce' ),
				'type' => 'checkbox',
				'label' => __( 'Enable Instamojo', 'instamojo' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title*', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default' => __( 'Instamojo', 'woocommerce' ),
				'desc_tip'      => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Pay via Instamojo', 'instamojo' )
			),
			'api_details' => array(
				'title'       => __( 'API Credentials', 'instamojo' ),
				'type'        => 'title',
				'description' => '',
			),
			'client_id' => array(
				'title' => __( 'Client ID*', 'instamojo' ),
				'type' => 'text',
				'description' => __( 'Your Instamojo Client ID available on your instamojo account', 'instamojo' ),
				'desc_tip'      => true,
			),
			'client_secret' => array(
				'title' => __( 'Client Secret*', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Your Instamojo Client Secret available on your instamojo account', 'woocommerce' ),
				'desc_tip'      => true,
			),
			'testmode' => array(
				'title'       => __( 'Test Mode', 'instamojo' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Test Mode', 'instamojo' ),
				'default'     => 'no',
				'description' => sprintf( __( 'To use Test mode you have to create account at %s', 'woocommerce' ), 'https://test.instamojo.com/' ),
			),		
		);