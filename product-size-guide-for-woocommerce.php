<?php

/**
 * Plugin Name:       Product Size Guide For WooCommerce
 * Plugin URI:        https://github.com/faridmia/product-size-guide-for-woocommerce
 * Description:       Easily add size charts to your WooCommerce products using default templates or fully customizable options..
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: faridmia
 * Author URI: https://profiles.wordpress.org/faridmia/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins: woocommerce
 * Text Domain: product-size-guide-for-woocommerce
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('SIZEPSGF_VERSION', '1.0.0');
define('SIZEPSGF_CORE_URL', plugin_dir_url(__FILE__));
define('SIZEPSGF_PLUGIN_ROOT', __FILE__);
define('SIZEPSGF_PLUGIN_PATH', plugin_dir_path(SIZEPSGF_PLUGIN_ROOT));
define('SIZEPSGF_PLUGIN_TITLE', 'Product Size Guide For WooCommerce');

add_action('init', 'sizepsgf_load_textdomain');
if (!version_compare(PHP_VERSION, '7.4', '>=')) {
    add_action('admin_notices', 'sizepsgf_fail_php_version');
} elseif (!version_compare(get_bloginfo('version'), '6.4', '>=')) {
    add_action('admin_notices', 'sizepsgf_fail_wp_version');
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Display an admin notice if the PHP version is not sufficient for the plugin.
 *
 * This function checks the current PHP version and displays a warning notice in the WordPress admin
 * if the current PHP version is less than the required version.
 *
 * @since 1.0.0
 */
function sizepsgf_fail_php_version()
{

    $message = sprintf(
        // Translators: %1$s is the plugin title, %2$s is the required PHP version.
        __('%1$s requires PHP version %2$s+, plugin is currently NOT RUNNING.', 'product-size-guide-for-woocommerce'),
        '<strong>' . SIZEPSGF_PLUGIN_TITLE . '</strong>',
        '7.4'
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message));
}
/**
 * Display an admin notice if the WordPress version is not sufficient for the plugin.
 *
 * This function checks the current WordPress version and displays a warning notice in the WordPress admin
 * if the current version is less than the required version.
 *
 * @since 1.0.0
 */
function sizepsgf_fail_wp_version()
{

    $message      = sprintf(
        // Translators: %1$s is the plugin title, %2$s is the WordPress version.
        esc_html__('To function, %1$s needs WordPress version %2$s or higher. The plugin is currently NOT RUNNING due to an outdated version.', 'product-size-guide-for-woocommerce'),
        SIZEPSGF_PLUGIN_TITLE,
        '6.4'
    );

    $error_message = sprintf('<div class="error">%s</div>', wpautop($message));
    echo wp_kses_post($error_message);
}

/**
 * sizepsgf_load_product-size-guide-for-woocommerce loads product-size-guide-for-woocommerce product-size-guide-for-woocommerce.
 *
 * Load gettext translate for the product-size-guide-for-woocommerce text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function sizepsgf_load_textdomain()
{
    // woocommerce  plugin dependency
    if (!function_exists('WC')) {
        add_action('admin_notices', 'sizepsgf_admin_notices');
    }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in src/class-cmfw-size-chart-activator.php
 */
function sizepsgf_activate_func()
{
    require_once SIZEPSGF_PLUGIN_PATH . 'src/class-cmfw-size-chart-activator.php';
    Sizechma_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in src/class-cmfw-size-chart-deactivator.php
 */
function sizepsgf__deactivate_func()
{
    require_once SIZEPSGF_PLUGIN_PATH . 'src/class-cmfw-size-chart-deactivator.php';
    Sizechma_Woo_Deactivator::deactivate();
}

register_activation_hook(SIZEPSGF_PLUGIN_ROOT, 'sizepsgf_activate_func');
register_deactivation_hook(SIZEPSGF_PLUGIN_ROOT, 'sizepsgf__deactivate_func');


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function sizepsgf__run_func()
{
    require_once __DIR__ . '/src/class-cmfw-size-chart-options.php';
}

function sizepsgf_admin_notices()
{

    $woocommerce_plugin = 'woocommerce/woocommerce.php';
    $plugin_name = esc_html__('Product Size Guide For WooCommerce', 'product-size-guide-for-woocommerce');

    // Check if WooCommerce is installed
    if (file_exists(WP_PLUGIN_DIR . '/' . $woocommerce_plugin)) {
        // WooCommerce is installed but may not be active
        if (!is_plugin_active($woocommerce_plugin)) {
            $activation_url = wp_nonce_url(
                'plugins.php?action=activate&amp;plugin=' . $woocommerce_plugin . '&amp;plugin_status=all&amp;paged=1&amp;s',
                'activate-plugin_' . $woocommerce_plugin
            );
            $message = sprintf(
                '<strong>%1$s requires WooCommerce to be active. You can <a href="%2$s" class="message" target="_blank">%3$s</a> here.</strong>',
                $plugin_name,
                esc_url($activation_url),
                __("Activate WooCommerce", "product-size-guide-for-woocommerce"),
            );
        }
    } else {
        // WooCommerce is not installed
        $plugin_name = 'WooCommerce';
        $action = 'install-plugin';
        $slug = 'woocommerce';
        $install_link = wp_nonce_url(
            add_query_arg(
                array(
                    'action' => $action,
                    'plugin' => $slug
                ),
                admin_url('update.php')
            ),
            $action . '_' . $slug
        );
        $message = sprintf(
            '<strong>%1$s requires WooCommerce to be installed. You can download <a href="%2$s" class="message" target="_blank">%3$s</a> here.</strong>',
            $plugin_name,
            esc_url($install_link),
            __("WooCommerce Install", "product-size-guide-for-woocommerce"),
        );
    }
?>
    <div class="error">
        <p><?php echo wp_kses($message, 'sizepsgf_kses'); ?></p>
    </div>
<?php
}


add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', 'product-size-guide-for-woocommerce/product-size-guide-for-woocommerce.php', true);
    }
});

sizepsgf__run_func();
