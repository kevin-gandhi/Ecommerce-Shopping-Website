<?php
class WbsVendors_DgmWpPluginBootstrapGuard
{
    /**
     * @param string $pluginName
     * @param string $phpVersion
     * @param string $wpVersion
     * @param string|null $wcVersion
     * @param string $bootstrapScript
     * @return void
     */
    static public function checkPrerequisitesAndBootstrap($pluginName,
                                                          $phpVersion,
                                                          $wpVersion,
                                                          $wcVersion,
                                                          $bootstrapScript)
    {
        $instance = new self($pluginName, $phpVersion, $wpVersion, $wcVersion, $bootstrapScript);
        $instance->checkPrerequisitesAndBootstrap_();
    }

    /**
     * @internal
     * @return void
     */
    public function _showNotices()
    {
        $this->showNotices($this->errors, 'error');
        $this->showNotices($this->warnings, 'warning');
    }

    /**
     * @internal
     * @return void
     */
    public function _checkWoocommerceVersionAndBootstrap()
    {
        $wcVersion = defined('WC_VERSION') ? WC_VERSION : null;

        if (!isset($wcVersion) || version_compare($wcVersion, $this->wcVersion, '<')) {
            $this->errors[] =
                "You are running an outdated WooCommerce version".(isset($wcVersion) ? " ".$wcVersion : null).".
                 {pluginName} requires WooCommerce {wcVersion}+.
                 Consider updating to a modern WooCommerce version.";
            return;
        }

        /** @noinspection PhpIncludeInspection */
        include($this->bootstrapScript);
    }

    /**
     * @param string $pluginName
     * @param string $phpVersion
     * @param string $wpVersion
     * @param string|null $wcVersion
     * @param string $bootstrapScript
     * @return void
     */
    private function __construct($pluginName, $phpVersion, $wpVersion, $wcVersion, $bootstrapScript)
    {
        $this->pluginName = $pluginName;
        $this->phpVersion = $phpVersion;
        $this->wpVersion = $wpVersion;
        $this->wcVersion = $wcVersion;
        $this->bootstrapScript = $bootstrapScript;

        // Hook admin_notices always since errors can be added lately
        add_action('admin_notices', array($this, '_showNotices'));
    }

    /**
     * @return void
     */
    private function checkPrerequisitesAndBootstrap_()
    {
        $this->errors = array();
        $this->warnings = array();

        if (version_compare($phpv = PHP_VERSION, $this->phpVersion, '<')) {
            $this->errors[] =
                "You are running an outdated PHP version {$phpv}. 
                 {pluginName} requires PHP {phpVersion}+. 
                 Contact your hosting support to switch to a newer PHP version.";

        }
        
        global $wp_version;
        if (isset($wp_version) && version_compare($wp_version, $this->wpVersion, '<')) {
            $this->errors[] =
                "You are running an outdated WordPress version {$wp_version}.
                 {pluginName} is tested with WordPress {wpVersion}+.
                 Consider updating to a modern WordPress version.";
        }

        if (isset($this->wcVersion)) {
            if (!self::isWoocommerceActive()) {
                $this->errors[] =
                    "WooCommerce is not active. 
                     {pluginName} requires WooCommerce to be installed and activated.";
            } else {
                if (defined('WC_VERSION') || did_action('woocommerce_loaded')) {
                    $this->_checkWoocommerceVersionAndBootstrap();
                } else {
                    add_action('woocommerce_loaded', array($this, '_checkWoocommerceVersionAndBootstrap'));
                }
            }
        }

        if ($this->errors) {
            return;
        }

        if (!class_exists('WbsVendors_DgmWpDismissibleNotices')) {
            require_once(__DIR__.'/DgmWpDismissibleNotices.php');
        }

        if (!WbsVendors_DgmWpDismissibleNotices::isNoticeDismissed($noticeId = 'dgm-zend-guard-loader')) {
            if (version_compare($phpv = PHP_VERSION, $minphpv = '5.4', '<') && self::isZendGuardLoaderActive()) {
                $this->warnings[$noticeId] =
                    "You are running PHP version {$phpv} with Zend Guard Loader extension active.
                    This server configuration might not be compatible with {pluginName}.
                    If you are getting 500 Internal Server Error or 503 Service Unavailable 
                    errors on Cart or Checkout pages when the plugin is active, disable
                    Zend Guard Loader or update your PHP version to {$minphpv}+.";
            }
        }

        if ($this->warnings) {
            WbsVendors_DgmWpDismissibleNotices::init();
        }

        return;
    }

    private function showNotices($notices, $kind)
    {
        if ($notices) {
            ?>
                <?php foreach ($notices as $dismissId => $notice): ?>
                    <?php
                        $dismissClass = null;
                        $dismissAttr = null;
                        if (is_string($dismissId) && !empty($dismissId)) {
                            $dismissClass = "is-dismissible";
                            $dismissAttr = "data-dismissible=".esc_html($dismissId);
                        }
                    ?>
                    <div class="notice notice-<?php echo esc_html($kind) ?> <?php echo $dismissClass ?>"
                        <?php echo $dismissAttr ?>
                    >
                        <?php
                            $notice = strtr($notice, array(
                                '{pluginName}' => $this->pluginName,
                                '{phpVersion}' => $this->phpVersion,
                                '{wpVersion}' => $this->wpVersion,
                                '{wcVersion}' => $this->wcVersion,
                            ));
                        ?>
                        <p><?php echo esc_html($notice) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php
        }
    }

    static private function isWoocommerceActive()
    {
        static $active_plugins;

        if (!isset($active_plugins)) {
            $active_plugins = (array)get_option('active_plugins', array());
            if (is_multisite()) {
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
        }

        return
            in_array('woocommerce/woocommerce.php', $active_plugins) ||
            array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }

    static private function getPhpIniBool($name, $default = null)
    {
        $value = ini_get($name);

        if ($value === false) {
            return $default;
        }

        if ((int)$value > 0) {

            $value = true;

        } else {

            $lowered = strtolower($value);

            if (in_array($lowered, array('true', 'on', 'yes'), true)) {
                $value = true;
            } else {
                $value = false;
            }
        }

        return $value;
    }

    static private function isZendGuardLoaderActive()
    {
        return
            in_array('Zend Guard Loader', get_loaded_extensions(), true) &&
            self::getPhpIniBool('zend_loader.enable', true);
    }


    private $pluginName;
    private $phpVersion;
    private $wpVersion;
    private $wcVersion;
    private $bootstrapScript;

    private $errors;
    private $warnings;
}