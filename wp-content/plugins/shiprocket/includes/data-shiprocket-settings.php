<?php

if (! defined('ABSPATH')) {
    exit;   // exit if directly accessed
}

if (is_admin() && ! empty($_GET['section']) && $_GET['section'] == 'shiprocket_woocommerce_shipping') {
    $user_meta = Shiprocket_Shipping_Rates_Common::get_current_user_meta();
    ?>
    <script>
        window.addEventListener('load', function () {

            // To create button to get the account details
            let integrationId = jQuery('#woocommerce_shiprocket_woocommerce_shipping_integration_id').val();
            
            if( integrationId != undefined && integrationId.length == 0 ) {
                jQuery('.space_before_shiprocket_get_account_details_button').remove(); //  To avoid multiple times
                jQuery('#shiprocket_get_account_details').remove(); //  To avoid multiple times
                
                let createShiprocketAccount = jQuery('<button class="button button-primary" type="button" id="shiprocket_get_account_details" >Signup Shiprocket.in & Get API Keys</button>');

                jQuery("#woocommerce_shiprocket_woocommerce_shipping_phone").after(createShiprocketAccount);            // Add button
                jQuery("#shiprocket_get_account_details").before('<space class="space_before_shiprocket_get_account_details_button">&nbsp;</space>');   // Add space
            }

            // Call to Shiprocket Server to get the response
            jQuery( "#shiprocket_get_account_details" ).click(function() {
                var email=jQuery('#woocommerce_shiprocket_woocommerce_shipping_email_id').val();
                if(email=='')
                {
                    alert("Enter your registered mail id with shiprocket");
                    return false;
                }
                var phone=jQuery('#woocommerce_shiprocket_woocommerce_shipping_phone').val();
                if(phone=='')
                {
                    alert("Enter your phone");
                    return false;
                }
                var first_name=jQuery('#woocommerce_shiprocket_woocommerce_shipping_first_name').val();
                if(first_name=='')
                {
                    alert("Enter your first name");
                    return false;
                }
                var last_name=jQuery('#woocommerce_shiprocket_woocommerce_shipping_last_name').val();
                if(last_name=='')
                {
                    alert("Enter your last name");
                    return false;
                }
                var company_name=jQuery('#woocommerce_shiprocket_woocommerce_shipping_company_name').val();
                if(company_name=='')
                {
                    alert("Enter your Company name");
                    return false;
                }

                jQuery(this).prop("disabled",true);
                
                var token = 'ACCESS_TOKEN:' + '<?php echo SHIPROCKET_ACCESS_TOKEN; ?>';
                
                let data = {};
                
                data.email              = jQuery('#woocommerce_shiprocket_woocommerce_shipping_email_id').val();        
                data.phone              = jQuery('#woocommerce_shiprocket_woocommerce_shipping_phone').val();   
                data.first_name         = jQuery('#woocommerce_shiprocket_woocommerce_shipping_first_name').val();  
                data.last_name          = jQuery('#woocommerce_shiprocket_woocommerce_shipping_last_name').val();   
                data.company_name       = jQuery('#woocommerce_shiprocket_woocommerce_shipping_company_name').val();
                data.store_url          = "<?php echo get_site_url(); ?>";                                          
                data.storeName          = "<?php echo bloginfo(); ?>";                                              
                data.billing_address    = "<?php echo $user_meta['billing_address_1'][0] ?>";
                data.billing_address_2  = "<?php echo $user_meta['billing_address_2'][0] ?>";
                data.billing_city       = "<?php echo $user_meta['billing_city'][0] ?>";
                data.billing_state      = "<?php echo $user_meta['billing_state'][0] ?>";
                data.billing_country    = "<?php echo $user_meta['billing_country'][0] ?>";
                data.billing_pin_code   = "<?php echo $user_meta['billing_postcode'][0] ?>";
                data.billing_phone      = "<?php echo $user_meta['billing_phone'][0] ?>";
                data.shipping_address   = "<?php echo $user_meta['shipping_address_1'][0] ?>";
                data.shipping_address_2 = "<?php echo $user_meta['shipping_address_2'][0] ?>";
                data.shipping_city      = "<?php echo $user_meta['shipping_city'][0] ?>";
                data.shipping_state     = "<?php echo $user_meta['shipping_state'][0] ?>";
                data.shipping_country   = "<?php echo $user_meta['shipping_country'][0] ?>";
                data.shipping_pin_code  = "<?php echo $user_meta['shipping_postcode'][0] ?>";
                data.shipping_phone     = "<?php echo $user_meta['shipping_phone'][0] ?>";
                data.utm_source         = "Woocommerce-Marketplace";
                data.utm_medium         = "Referal";
                data.utm_campaign       = "Woocommerce-App-Download";
                data.utm_content        = "";
                data.utm_term           = "";

                jQuery.ajax({
                    url: "<?php echo SHIPROCKET_WC_ACCOUNT_REGISTER_ENDPOINT ?>", 
                    data: JSON.stringify(data), 
                    method: 'POST',
                    contentType: 'application/json', 
                    beforeSend: function( xhr ) {
                        xhr.setRequestHeader( "authorization",  token );
                      }
                })
                .done( function(response) {
                    if( response.status != 1 ) {
                        alert(response.message);
                    }
                    else if( response.status == 1 ) {
                        jQuery("#woocommerce_shiprocket_woocommerce_shipping_integration_id").val(response.merchant_id);

                        alert("Shiprocket Account linked successfully!");
                    }
                    jQuery('#shiprocket_get_account_details').prop("disabled",false);
                } )
                .fail( function(result){
                    alert('No response from Shiprocket Server. Something Went wrong');
                    jQuery('#shiprocket_get_account_details').prop("disabled",false);
                });
                
            });
        });
    </script>

    <?php
}

$logged_in_user_email_id = null;
if (is_admin() && ! empty($_GET['section']) && $_GET['section'] == 'shiprocket_woocommerce_shipping') {
    $logged_in_user_email_id = Shiprocket_Shipping_Rates_Common::get_current_user_email_id();
}

// Settings
return array(
    'enabled'           => array(
        'title'         => __('Realtime Rates', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'checkbox',
        'label'         => __('Enable', 'shiprocket-woocommerce-shipping-calculator'),
        'default'       => 'no',
    ),
    'email_id'  => array(
        'title'         => __('Email Id', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'default'       => $logged_in_user_email_id,
        'description'   => __("Required for Shiprocket Account . ", 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),
    'company_name'  => array(
        'title'         => __('Company Name', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'description'   => __("Required for Shiprocket Account . ", 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),
    'first_name'    => array(
        'title'         => __('First Name', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'description'   => __("Required for Shiprocket Account . ", 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),
    'last_name' => array(
        'title'         => __('Last Name', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'description'   => __("Required for Shiprocket Account . ", 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),
    'phone'         => array(
        'title'         => __('Phone', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'placeholder'   => __('Required for Shiprocket Account ', 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),
    'integration_id'    => array(
        'title'         => __('Integration Id', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'description'   => __("Required for the Plugin to Authenticate Shiprocket Account. If your store is not already integrated with Shiprocket, ", 'shiprocket-woocommerce-shipping-calculator'),
        'placeholder'         => __("It will be autofilled upon clicking the Signup button.", 'shiprocket-woocommerce-shipping-calculator'),
        'css'           => 'pointer-events: none;'
    ),
    'shipping_title'    => array(
        'title'         => __('Method Title', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'default'       =>  'Shipping Rate',
        'description'   => __("Shipping Method Title. It will be used when no response is returned by Shiprocket", 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),
    'fallback_rate'     => array(
        'title'         => __('Fallback Rate', 'shiprocket-woocommerce-shipping-calculator'),
        'type'          => 'text',
        'default'       => '40',
        'description'   => __("If no rate returned by Shiprocket account then this fallback rate will be displayed. Shipping Method Title will be used as Service name.", 'shiprocket-woocommerce-shipping-calculator'),
        'desc_tip'      => true,
    ),

);