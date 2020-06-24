<?php

/**
 * Handles requests to the /wc/shiprocket/v1/.
 *
 * @author   Shiprocket
 *
 * @category API
 */
if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Shiprocket_user_List_API')) {
    /**
     * Tracking REST API controller class.
     *
     * @package WooCommerce/API
     *
     * @extends WC_REST_Controller
     */
    class Shiprocket_user_List_API extends WC_REST_Controller
    {

        /**
         * Endpoint namespace.
         *
         * @var string
         */
        protected $namespace = 'wc/shiprocket/v1';

        /**
         * Route base.
         *
         * @var string
         */
        protected $rest_base = '/user_list';

        /**
         * Post type.
         *
         * @var string
         */
        protected $post_type = 'users';

        /**
         * Register the routes for order notes.
         */
        public function register_routes()
        {
            register_rest_route($this->namespace, '/' . $this->rest_base, array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_customers' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(),
                ),
                'schema' => array($this, 'get_public_item_schema'),
            ));
        }
        /**
         * Check if a given request has write access.
         *
         * @param  WP_REST_Request $request Full details about the request.
         *
         * @return bool|WP_Error
         */
        public function get_items_permissions_check($request)
        {
            if (! wc_rest_check_user_permissions('read')) {
                return new WP_Error('woocommerce_rest_cannot_view', __('Sorry, you cannot list resources.', 'woocommerce'), array( 'status' => rest_authorization_required_code() ));
            }

            return true;
        }
        
        public function get_customers($fields = null, $filter = array(), $page = 1)
        {

            $filter['page'] = $page;

            $query = $this->query_customers($filter);

            $customers = array();

            foreach ($query->get_results() as $user_id) {
                $customers[] = current($this->get_customer($user_id, $fields));
            }
            return array( 'customers' => $customers );
        }

        public function get_customer($id, $fields = null)
        {
            
            global $wpdb;

            $customer      = new WC_Customer($id);
            $last_order    = $customer->get_last_order();
            $customer_data = array(
                'id'               => $customer->get_id(),
                'created_at'       => $this->format_datetime($customer->get_date_created() ? $customer->get_date_created()->getTimestamp() : 0), // API gives UTC times.
                'last_update'      => $this->format_datetime($customer->get_date_modified() ? $customer->get_date_modified()->getTimestamp() : 0), // API gives UTC times.
                'email'            => $customer->get_email(),
                'first_name'       => $customer->get_first_name(),
                'last_name'        => $customer->get_last_name(),
                'username'         => $customer->get_username(),
                'role'             => $customer->get_role(),
                'last_order_id'    => is_object($last_order) ? $last_order->get_id() : null,
                'last_order_date'  => is_object($last_order) ? $this->format_datetime($last_order->get_date_created() ? $last_order->get_date_created()->getTimestamp() : 0) : null, // API gives UTC times.
                'orders_count'     => $customer->get_order_count(),
                'total_spent'      => wc_format_decimal($customer->get_total_spent(), 2),
                'avatar_url'       => $customer->get_avatar_url(),
                'billing_address'  => array(
                    'first_name' => $customer->get_billing_first_name(),
                    'last_name'  => $customer->get_billing_last_name(),
                    'company'    => $customer->get_billing_company(),
                    'address_1'  => $customer->get_billing_address_1(),
                    'address_2'  => $customer->get_billing_address_2(),
                    'city'       => $customer->get_billing_city(),
                    'state'      => $customer->get_billing_state(),
                    'postcode'   => $customer->get_billing_postcode(),
                    'country'    => $customer->get_billing_country(),
                    'email'      => $customer->get_billing_email(),
                    'phone'      => $customer->get_billing_phone(),
                ),
                'shipping_address' => array(
                    'first_name' => $customer->get_shipping_first_name(),
                    'last_name'  => $customer->get_shipping_last_name(),
                    'company'    => $customer->get_shipping_company(),
                    'address_1'  => $customer->get_shipping_address_1(),
                    'address_2'  => $customer->get_shipping_address_2(),
                    'city'       => $customer->get_shipping_city(),
                    'state'      => $customer->get_shipping_state(),
                    'postcode'   => $customer->get_shipping_postcode(),
                    'country'    => $customer->get_shipping_country(),
                ),
            );

            return array( 'customer' => apply_filters('woocommerce_api_customer_response', $customer_data, $customer, $fields) );
        }

        public function format_datetime($timestamp, $convert_to_utc = false, $convert_to_gmt = false)
        {
            if ($convert_to_gmt) {
                if (is_numeric($timestamp)) {
                    $timestamp = date('Y-m-d H:i:s', $timestamp);
                }

                $timestamp = get_gmt_from_date($timestamp);
            }

            if ($convert_to_utc) {
                $timezone = new DateTimeZone(wc_timezone_string());
            } else {
                $timezone = new DateTimeZone('UTC');
            }

            try {
                if (is_numeric($timestamp)) {
                    $date = new DateTime("@{$timestamp}");
                } else {
                    $date = new DateTime($timestamp, $timezone);
                }

                // convert to UTC by adjusting the time based on the offset of the site's timezone
                if ($convert_to_utc) {
                    $date->modify(-1 * $date->getOffset() . ' seconds');
                }
            } catch (Exception $e) {
                $date = new DateTime('@0');
            }

            return $date->format('Y-m-d\TH:i:s\Z');
        }


        public function query_customers($args = array())
        {

            // default users per page
            $users_per_page = get_option('posts_per_page');

            // Set base query arguments
            $query_args = array(
                'fields'  => 'ID',
                'role'    => 'customer',
                'orderby' => 'registered',
                'number'  => $users_per_page,
            );

            $query = new WP_User_Query($query_args);

            // Helper members for pagination headers
            $query->total_pages = ( -1 == $args['limit'] ) ? 1 : ceil($query->get_total() / $users_per_page);
            $query->page = $args['page'];

            return $query;
        }
    }


}
