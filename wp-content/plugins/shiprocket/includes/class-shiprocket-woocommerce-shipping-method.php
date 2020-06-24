    <?php

    if (! defined('ABSPATH')) {
        exit; // Exit if accessed directly.
    }

/**
 * Shipping Method Class. Responsible for handling rates.
 */
    if (! class_exists("Shiprocket_Woocommerce_Shipping_Method")) {
        class Shiprocket_Woocommerce_Shipping_Method extends WC_Shipping_Method
        {
        
            /**
             * Weight Unit.
             */
            public static $weight_unit;
            /**
             * Dimension Unit.
             */
            public static $dimension_unit;
            /**
             * Currency code.
             */
            public static $currency_code;
            /**
             * Integration Id.
             */
            public static $integration_id;

            /**
             * boolean true if debug mode is enabled.
             */
            public static $debug;
            /**
             * Shiprocket transaction id returned by Shiprocket Server.
             */
            public static $shiprocketTransactionId;
            /**
             * Fall back rate.
             */
            public static $fallback_rate;
            /**
             * Tax Calculation for Shipping rates.
             */
            public static $tax_calculation_mode;
        
            public static $cod;


            /**
             * Constructor.
             */
            public function __construct()
            {
                $plugin_configuration               = Shiprocket_Woocommerce_Shipping::shiprocket_plugin_configuration();
                $this->id                           = $plugin_configuration['id'];
                $this->method_title                 = $plugin_configuration['method_title'];
                $this->method_description           = $plugin_configuration['method_description'];
                $this->init();
        
                add_action('woocommerce_cart_calculate_fees', array($this, 'shipping_method_discount'));
                add_action('woocommerce_review_order_before_payment', array( $this, 'shiprocket_update_shipping_charges'));
                // Save settings in admin
                add_action('woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ));
            }

            /**
             * Initialize the settings.
             */
            private function init()
            {
                // Load the settings.
                $this->init_form_fields();
                $this->init_settings();

                $this->title                    = $this->method_title;
                $this->enabled                  = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'no';
                self::$integration_id           = $this->settings['integration_id'] ? $this->settings['integration_id'] : null;
                self::$debug                    = false; // ( isset($this->settings['debug']) && $this->settings['debug']=='yes' ) ? true : false;
                self::$fallback_rate            = ! empty($this->settings['fallback_rate']) ? $this->settings['fallback_rate'] : null;
                $this->shipping_title           = ! empty($this->settings['shipping_title']) ? $this->settings['shipping_title'] : 'Shipping Rate';
                self::$tax_calculation_mode     = ! empty($this->settings['tax_calculation_mode']) ? $this->settings['tax_calculation_mode'] : false;
            }
        
            /**
             * Settings Form fileds.
             */
            public function init_form_fields()
            {
                $this->form_fields  = include('data-shiprocket-settings.php');
            }

            /**
             * Calculate shipping.
             */
            public function calculate_shipping($package = array())
            {

                if (empty(self::$integration_id)) {
                    self::debug(__('Shiprocket Integration Id Missing.', 'shiprocket-woocommerce-shipping-calculator'));
                    return;
                }

                $this->found_rates = array();

                if (empty(self::$weight_unit)) {
                    self::$weight_unit      = get_option('woocommerce_weight_unit');
                }
                if (empty(self::$dimension_unit)) {
                    self::$dimension_unit   = get_option('woocommerce_dimension_unit');
                }
                if (empty(self::$currency_code)) {
                    self::$currency_code    = get_woocommerce_currency();
                }

                $formatted_package  = self::get_formatted_data($package);
            
                // Required to get the debug info from api
                if (self::$debug) {
                    $data['isDebug'] = true;
                }

                $response = $this->get_rates_from_server($formatted_package);

                if ($response !== false) {
                    $this->process_result($response);
                }
                // Handle Fallback rates if no rates returned
                if (empty($this->found_rates) && ! empty(self::$fallback_rate)) {
                    $shipping_method_detail = new stdClass();
                    $shipping_method_detail->ruleName       = $this->shipping_title;
                    $shipping_method_detail->displayName    = $this->shipping_title;
                    $shipping_method_detail->rate           = self::$fallback_rate;
                    $shipping_method_detail->ruleName       = $this->shipping_title;
                    $shipping_method_detail->ruleId         = null;
                    $shipping_method_detail->serviceId      = null;
                    $shipping_method_detail->etd            = '';
                    $shipping_method_detail->carrierId      = 'fallback_rate';
                    $this->prepare_rate($shipping_method_detail);
                }
        
                $this->add_found_rates();
            }

            /**
             * Get formatted data from woocommerce cart package.
             * @param $package array Package.
             * @return array Formatted package.
             */
            public static function get_formatted_data($package)
            {

                $l = $b = $h = $w = 0;

                foreach ($package['contents'] as $key => $line_item) {
                    $quantity = $line_item['quantity'];
                    $w += $line_item['data']->get_weight() * $quantity;
                    $temp = array($line_item['data']->get_length(), $line_item['data']->get_width(), $line_item['data']->get_height());
                    sort($temp);
                    $h += $temp[0];
                    $l = max($l, $temp[1]);
                    $b = max($b, $temp[2]);
                }

                // Convert weight into Kgs
                if (!empty(self::$weight_unit) && self::$weight_unit == 'grams') {
                    $weight /= 1000;
                }

                // Convert dimensions into cm
                if (!empty(self::$dimension_unit) && self::$dimension_unit == 'inches') {
                    $l *= 2.54;
                    $b *= 2.54;
                    $h *= 2.54;
                }
            
                $data_to_send = array('length' => $l, 'width' => $b, 'height' => $h, 'weight' => $w, 'declared_value' => $package['cart_subtotal']);
        
                $chosen_payment_method = WC()->session->get('chosen_payment_method');
        
                $data_to_send['cod']    = ($chosen_payment_method != "cod") ? '0' : '1';
                $data_to_send['currency']       = self::$currency_code;
                $data_to_send['declared_value']     = $package['cart_subtotal'];
                $data_to_send['delivery_postcode']  = $package['destination']['postcode'];
                $data_to_send['reference_id']   = uniqid();
                $data_to_send['merchant_id'] = self::$integration_id;
            
                WC()->session->set('ph_shiprocket_rates_unique_id', $data_to_send['reference_id']);
                return $data_to_send;
            }

            /**
             * Get the rates from Shiprocket Server.
             * @param $data string Encrypted data
             * @return
             */
            public function get_rates_from_server($data)
            {

                // Get the response from server.
                $response = wp_remote_get(
                    SHIPROCKET_WC_RATE_URL . '?' . http_build_query($data),
                    array(
                    'headers'   =>  array(
                        'authorization' =>  "ACCESS_TOKEN:" . SHIPROCKET_ACCESS_TOKEN
                    ),
                    'timeout'   =>  20
                    )
                );

                // WP_error while getting the response
                if (is_wp_error($response)) {
                    $error_string = $response->get_error_message();
                    self::debug('Wordpreess Error: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' .__('WP Error : ').print_r($error_string, true). '</pre>');
                    return false;
                }

                // Successful response
                if ($response['response']['code'] == '200') {
                    $body = $response['body'];
                    $body = json_decode($body);
                    return $body;
                } else {
                    self::debug('Shiprocket Error: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' .__('Error Code : ').print_r($response['response']['code'], true).'<br/>' .__('Error Message : ') . print_r($response['response']['message'], true) .'</pre>');
                    return false;
                }
            }

            /**
             * Add debug info to the Front end.
             */
            public static function debug($message, $type = 'notice')
            {
                if (self::$debug) {
                    wc_add_notice($message, $type);
                }
            }

            /**
             * Process the Response body received from server.
             */
            public function process_result($body)
            {
                if ($body->status == '200' && ! empty($body->data)) {
                    $json_decoded_data =  $body->data;
                
                    $available_courier_companies = $json_decoded_data->available_courier_companies;
                    if (is_array($available_courier_companies)) {
                        $limit = 5;
                        foreach ($available_courier_companies as $couriers) {
                            if ($limit == 0) {
                                break;
                            }
                            self::prepare_rate($couriers);
                            $limit--;
                        }
                    }
                }
            }

            /**
             * Prepare the rates.
             * @param $shipping_method_detail object Rate returned from API.
             */
            public function prepare_rate($shipping_method_detail)
            {
                $rate_name = $shipping_method_detail->courier_name;

                if ($shipping_method_detail->etd != '') {
                    $rate_name .= ' ( Delivery By ' . $shipping_method_detail->etd . ')';
                }

                if ($shipping_method_detail->cod) {
                    $rate_id    = $this->id.'_cod:'.$shipping_method_detail->courier_company_id;
                } else {
                    $rate_id    = $this->id.'_prepaid:'.$shipping_method_detail->courier_company_id;
                }

                $rate_cost  = $shipping_method_detail->rate;

                $this->found_rates[$rate_id] = array(
                'id'            => $rate_id,
                'label'         => $rate_name,
                'cost'          => $rate_cost,
                'taxes'         =>  ! empty(self::$tax_calculation_mode) ? '' : false,
                'calc_tax'      =>  self::$tax_calculation_mode,
                'meta_data'     => array(
                    'ph_shiprocket_shipping_rates'  =>  array(
                        'courier_company_id'    =>  $shipping_method_detail->courier_company_id,
                        'uniqueId'  =>  WC()->session->get('ph_shiprocket_rates_unique_id'),
                        'serviceId' =>  $shipping_method_detail->courier_name,
                        'carrierId' =>  $shipping_method_detail->courier_company_id,
                        'shiprocketTransactionId'   =>  self::$shiprocketTransactionId,
                    ),
                ),
                );
            }

            /**
             * Add found rates to woocommerce shipping rate.
             */
            public function add_found_rates()
            {
                foreach ($this->found_rates as $key => $rate) {
                    $this->add_rate($rate);
                }
            }
        
            public function shipping_method_discount($cart_object)
            {

                if (is_admin() && ! defined('DOING_AJAX')) {
                    return;
                }
            }
        
            public function shiprocket_update_shipping_charges()
            {
                // jQuery code
                ?>
            <script type="text/javascript">
                (function($){
                    $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                        $('body').trigger('update_checkout');
                    });
                })(jQuery);
            </script>
                <?php
            }
        }
    }