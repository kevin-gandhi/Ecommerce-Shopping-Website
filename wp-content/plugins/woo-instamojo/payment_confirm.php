<?php
if (!defined('ABSPATH')) {
	exit;
}

insta_log("Callback Called with payment ID: $payment_id and payment req id: $payment_request_id");

if(!isset($payment_id) or !isset($payment_request_id))
{
	insta_log("Callback Called without  payment ID or payment req id exittng..");
	wp_redirect(get_site_url());
}

$stored_payment_req_id = WC()->session->get( 'payment_request_id');
if($stored_payment_req_id!= $payment_request_id)
{
	insta_log("Given Payment request id not matched with  stored payment request id: $stored_payment_req_id ");
	wp_redirect(get_site_url());
}
include_once "lib/Instamojo.php";
try{
	$pgInstamojo = new WP_Gateway_Instamojo();
	$testmode       = 'yes' === $pgInstamojo->get_option('testmode', 'no');
	$client_id      = $pgInstamojo->get_option('client_id');
	$client_secret  = $pgInstamojo->get_option('client_secret');
	$xclient_id      = truncate_secret($client_id);
	$xclient_secret  = truncate_secret($client_secret);
	insta_log("Client ID: $xclient_id | Client Secret: $xclient_secret | Testmode: $testmode ");
	
	$api = new Instamojo($client_id, $client_secret, $testmode);
	$response = $api->getOrderById($payment_request_id);
	insta_log("Response from server: ".print_r($response,true));
	
	$payment_status = $api->getPaymentStatus($payment_id, $response->payments);

	insta_log("Payment status for $payment_id is $payment_status");

	if($payment_status === "successful" OR  $payment_status =="failed" )
	{
		
		$order_id = $response->transaction_id;
		$order_id = explode("-",$order_id);
		$order_id = $order_id[1];
		insta_log("Extracted order id from trasaction_id: ".$order_id);
		$order = new WC_Order( $order_id );
			
		if($order)
		{
			if($payment_status == "successful"){
			  insta_log("Payment for $payment_id was credited.");
			  $order->payment_complete($payment_id);
			  wp_safe_redirect($pgInstamojo->get_return_url($order));
			}
			else if($payment_status == "failed"){
			  insta_log("Payment for $payment_id failed.");
			  $order->cancel_order( __( 'Unpaid order cancelled - Instamojo Returned Failed Status for payment Id '.$payment_id, 'woocommerce' ));
			  global $woocommerce;
			  wp_safe_redirect($woocommerce->cart->get_cart_url()); 
			}
				
		}else
			insta_log("Order not found with order id $order_id");
	}else
		insta_log("Unknown Payment Status");	
	
}catch(CurlException $e){
		insta_log((String)$e);
}catch(ValidationException $e){
		insta_log("Validation Exception Occured with response ".print_r($e->getResponse(),true));
}catch(Exception $e){
	insta_log( $e->getMessage());	
}		
