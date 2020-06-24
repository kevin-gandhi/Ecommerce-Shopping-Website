=== Instamojo for WooCommerce ===
Contributors: instamojo, lubus
Donate link: https://www.instamojo.com/
Tags: commerce, e-commerce, ecommerce, online store, sell digital downloads, sell online, shop, store, wordpress ecommerce, WordPress shopping cart, sell event tickets, sell subscriptions, sell memberships, sell physical goods, payments, easy payments, payments button, widget
Requires at least: 3.3
Tested up to: 4.8.0
Stable tag: 1.0.7
License: MIT
License URI: http://opensource.org/licenses/MIT

Sell & collect payments instantly for almost anything -- directly from your WordPress website.

== Description ==

India's emerging C2C payments & e-commerce platform.

We make digital commerce universally accessible to create newer opportunities & sustainable livelihoods.

We believe every business idea deserves to be on the Internet to grow. But very few can. We make it possible with technology, data, design and little bit of Mojo (​noun | mo·jo | meaning magic​).

== Installation ==

This section describes how to install the plugin and get it working.

1. Make sure you've installed the [WooCommerce plugin](https://wordpress.org/plugins/woocommerce/) and it is activated. Without WooCommerce this plugin won't work.
2. Search for "Instamojo Payment Gateway for WooCommerce" on the WordPress Plugin directory or [download it](https://downloads.wordpress.org/plugin/woo-instamojo.zip).
3. Install the plugin.
4. Activate the plugin through the 'Plugins' menu in WordPress.

Once the plugin is installed and activated, you will be able to access a new menu under settings called "Instamojo Payment Gateway for WooCommerce".

*Configuration*

Now go to WooCommerce's settings tab (left sidebar on your WordPress dashboard) --> Settings --> Checkout --> Instamojo.

1. **Enable/Disable** - Check this to enable this plugin.
2. **Title** - The plugin name that buyer sees during checkout.
3. **Description** - Additional description related to this checkout method, for example: "Pay using CC/DB/NB and wallets".
4. **Client ID** and **Client Secret** - Client Secret And Client ID can be generated on the [Integrations page](https://www.instamojo.com/integrations/). Related support article: [How Do I Get My Client ID And Client Secret?](https://support.instamojo.com/hc/en-us/articles/212214265-How-do-I-get-my-Client-ID-and-Client-Secret-)
5. **Test Mode** - If enabled you can use our [Sandbox environment](https://support.instamojo.com/hc/en-us/articles/208485675-Test-or-Sandbox-Account) to test payments. Note that in this case you should use Client Secret and Client ID from the test account not production.

== Support ==

For any issue send us an email to support@instamojo.com and share the log file. Its location is `wp-content/uploads/wc-logs/`.
Inside `wc-logs` there’s going to be file whose name starts with `instamojo`.

== Screenshots ==

1. Activate WooCommerce and Instamojo Payment Gateway for WooCommerce.
2. Configure Instamojo Payment Gateway for WooCommerce.
3. Settings page after configuration.
4. Checkout screen when using Instamojo Payment Gateway for WooCommerce.

== Changelog ==

= 1.0.7 =

* Updated supported WordPress version to 4.8.0
* Made log files more secure 
* Fixed compatibility issues with PHP versions below 5.4.0

= 1.0.6 =

* Updated supported WordPress version to 4.7.2
* Fixed issue related to checking WooCommerce and cURL before plugin activation.

= 1.0.5 =

* Updated supported WordPress version to 4.7.1
* Added checks related to WooCommerce and cURL during plugin installation.

= 1.0.4 =

* Updated supported WordPress version to 4.7.

= 1.0.3 =

* Better error messages(with links) and logging. 

= 1.0.2 =

* Replaced `__dir__` with `dirname(__FILE__)` to support PHP versions older than 5.3.0.

= 1.0.1 =

* Fixed issue related to getting payment status.

= 1.0.0 =

This version(1.x) is a major change from 0.1.3.
* We have now switched from product link based approach to Integrations API.
* Better error handling.
* Easier configuration.

= 0.1.3 =

* Fetch the total from order instead of cart to handle order based payments as well.

= 0.1.2 =

* Now handling emails with "+" properly.

= 0.1.1 =

* Fixed the error message related to session_start().
* Updated supported WordPress version to 4.6.1.

= 0.1.0 =

* Fixed the issue with cart clearing on successful payment.
* Updated supported WordPress version to 4.6.
* Updated field title to use "Product Link" instead of "Payment Link".

= 0.0.9 =

* Updated README to use the term Product instead of Payment link.
* Updated supported WordPress version to 4.5.3.

= 0.0.8 =

* Now removing spaces from the full name to fix the signature error some users are getting.
* Updated supported WordPress version to 4.5.2.

= 0.0.7 =

* Fixed XSS vulnerability in checkout/order-received/ page. Discovered by Ajin Abraham(https://opsecx.com/).
* Updated supported WordPress version to 4.4.2.

= 0.0.6 =

* Added support for failed payments.
* Updated supported WordPress version to 4.3.
* Small update to the Installation docs.

= 0.0.5 =

* Handle phone numbers with "+" properly.
* Added '/' at the end of the payment detail URL to avoid an extra redirection.

= 0.0.4 =
* Fixed $_SESSION issue faced by users with non-persistent $_SESSION.

= 0.0.3 =
* Fixed sorting issue for older PHP versions.
* Added an option to provide custom "Thank You Message" to user in case of successful payment.
* Plugin is much more smaller now.

= 0.0.2 =
* Various bug fixes related to name, email length.
* Some changes related to backward compatibility.

= 0.0.1 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =

The version 1.x is a major change from 0.1.3 and will affect your payments if the migration is not done properly. The advantages of the new plugin are:

* Easier configuration
* Better error handling
* No sensitive data in the URL
* Faster checkouts
* Test mode

Please check the detailed migration guide here: [Migrating from version 0.1.3 to 1.0.x](https://github.com/Instamojo/Woo-Instamojo/blob/master/MIGRATION.md)
