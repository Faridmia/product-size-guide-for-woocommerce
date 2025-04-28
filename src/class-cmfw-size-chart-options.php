<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
use Envatothemely\Sizechartpsgf\Admin\Admin;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;
use Envatothemely\Sizechartpsgf\Common\Common;
use Envatothemely\Sizechartpsgf\Front\Front;


final class SizechmaWooProductOptions
{

    use Traitval;
    /**
     * Plugin Version
     *
     * @since 1.0.0
     * @var string The plugin version.
     */

    private static $instance;
    public $admin;
    public $front;
    public $common;
    public $hookwoo;

    private function __construct() {
        
        $this->define_constants();
        add_action('plugins_loaded', array($this, 'init_plugin'));
       
        add_action('wp_enqueue_scripts', array($this, 'sizepsgf_enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'sizepsgf_enqueue_admin_assets'));
        add_filter('plugin_action_links_' . SIZEPSGF_PLUGIN_BASE,  array($this, 'sizepsgf_setting_page_link_func'));

    }
    
    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        // general constants
        define('SIZEPSGF_PLUGIN_URL', plugins_url('/', SIZEPSGF_PLUGIN_ROOT));
        define('SIZEPSGF_PLUGIN_BASE', plugin_basename(SIZEPSGF_PLUGIN_ROOT));
        define('SIZEPSGF_CORE_ASSETS', SIZEPSGF_PLUGIN_URL);
    }
    /**
     * Enqueues frontend CSS and JavaScript for the Product Size Guide For WooCommerce plugin.
     *
     * This function hooks into 'wp_enqueue_scripts' to load the necessary frontend assets (CSS and JS)
     * for the plugin. It ensures the assets are loaded on the front end of the site.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function sizepsgf_enqueue_frontend_assets() {

        // Enqueue frontend CSS
        wp_enqueue_style('cmfw-front-css', SIZEPSGF_CORE_ASSETS . 'assets/frontend/css/cmfw-front.css', array(), SIZEPSGF_VERSION  );
        wp_enqueue_style('fontawesome', SIZEPSGF_CORE_ASSETS . 'assets/frontend/css/all.min.css', array(), SIZEPSGF_VERSION  );
        // Enqueue frontend JS
        wp_enqueue_script('cmfw-frontend-script', SIZEPSGF_CORE_ASSETS . 'assets/frontend/js/cmfw-script.js', array('jquery'), SIZEPSGF_VERSION, true);

    }

    /**
     * Enqueues admin CSS and JavaScript for the Product Size Guide For WooCommerce plugin.
     *
     * This function hooks into 'admin_enqueue_scripts' to load the necessary admin assets (CSS and JS)
     * for the plugin. It ensures the assets are loaded on the admin side of the site.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function sizepsgf_enqueue_admin_assets() {

        if( !is_admin() && get_post_type() != 'cmfw-size-chart' ) {
            return;
        }

        // CSS and JS files to enqueue
        $enqueue_styles = array(
            array(
                'handle' => 'cmfw-admin-css', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/css/cmfw-admin.css', 
                'deps' => array(), 
                'ver' => SIZEPSGF_VERSION, 
                'media' => 'all'
            ),
            array(
                'handle' => 'select2-min-css', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/css/select2.min.css', 
                'deps' => array(), 
                'ver' => SIZEPSGF_VERSION, 
                'media' => 'all'
            ),
            array(
                'handle' => 'fontawesome', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/css/all.min.css', 
                'deps' => array(), 
                'ver' => SIZEPSGF_VERSION, 
                'media' => 'all'
            ),
        );

        $enqueue_scripts = array(
            array(
                'handle' => 'wp-color-picker', 
                'src' => '', 'deps' => array(), 
                'ver' => '', 
                'in_footer' => false
            ), // Default WP script
            array(
                'handle' => 'select2-min-js', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/js/select2.min.js', 
                'deps' => array('jquery'), 
                'ver' => SIZEPSGF_VERSION, 
                'in_footer' => true
            ),
            array(
                'handle' => 'select2-full-js', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/js/select2.full.js', 
                'deps' => array('jquery'), 
                'ver' => SIZEPSGF_VERSION, 
                'in_footer' => true
            ),
            array(
                'handle' => 'jquery-ui-sortable', 
                'src' => '', 
                'deps' => array(), 
                'ver' => '', 
                'in_footer' => false
            ), // Default WP script
            array(
                'handle' => 'cmfw-admin-js', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/js/cmfw-admin.js', 
                'deps' => array('jquery', 'jquery-ui-sortable'), 
                'ver' => SIZEPSGF_VERSION, 
                'in_footer' => true
            ),
            array(
                'handle' => 'sizepsgf-icon-picker', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/js/sizepsgf-icon-picker.js', 
                'deps' => array('jquery', 'jquery-ui-sortable'), 
                'ver' => SIZEPSGF_VERSION, 
                'in_footer' => true
            ),
            
            array(
                'handle' => 'cmfw-admin-ajax-script', 
                'src' => SIZEPSGF_CORE_ASSETS . 'assets/admin/js/admin-ajax-script.js', 
                'deps' => array('jquery'), 
                'ver' => SIZEPSGF_VERSION, 
                'in_footer' => true
            ),
        );

        // Enqueue styles
        foreach ($enqueue_styles as $style) {
            wp_enqueue_style($style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media']);
        }

        // Enqueue scripts
        foreach ($enqueue_scripts as $script) {
            if (!empty($script['src'])) {
                wp_enqueue_script($script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer']);
            } else {
                wp_enqueue_script($script['handle']); // For default WP scripts like 'wp-color-picker' and 'jquery-ui-sortable'
            }
        }

        // Localize script
        $data_to_pass = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'homeUrl' => home_url('/'),
            "select_placeholder" => esc_html__('Select Product', 'product-size-guide-for-woocommerce'),
            "exclude_placeholder" => esc_html__('Exclude Product', 'product-size-guide-for-woocommerce'),
            "select_categories" => esc_html__('Select Categories', 'product-size-guide-for-woocommerce'),
            'nonce' => wp_create_nonce('sizepsgf_save_size_chart')
        );

        wp_localize_script('cmfw-admin-js', 'sizepsgf_localize_obj', $data_to_pass);
        wp_localize_script('cmfw-admin-ajax-script', 'sizepsgf_localize_obj', $data_to_pass);
        
    }
    /**
     * Add a settings link to the plugin action links.
     *
     * This function adds a link to the settings page in the plugin's action links on the Plugins page.
     * It uses the 'plugin_action_links_' filter to append the settings link to the existing array of links.
     *
     * @since 1.0.0
     *
     * @param array $links An array of the plugin's action links.
     * @return array The modified array of action links with the settings page link appended.
     */
    function sizepsgf_setting_page_link_func( $links ) {
        $action_link = sprintf("<a href='%s'>%s</a>", admin_url('edit.php?post_type=cmfw-size-chart'), __('Size Charts', 'product-size-guide-for-woocommerce'));
        array_push($links, $action_link);
        $action_link = sprintf("<a href='%s'>%s</a>", admin_url('edit.php?post_type=cmfw-size-chart&page=cmfw-size-settings'), __('Settings', 'product-size-guide-for-woocommerce'));
        array_push($links, $action_link);
        return $links;
    }

    /**
	 * Check if a plugin is installed
	 *
	 * @since v1.0.0
	 */
	public function is_plugin_installed( $basename ) {
		if ( !function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $basename ] );
	}

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {
        if (is_null(self::$instance)) {
            self::$instance = self::getInstance();
    
            if (class_exists('WooCommerce')) {
                self::$instance->common  = Common::getInstance();
                self::$instance->front   = Front::getInstance();
    
                if (is_admin()) {
                    self::$instance->admin = Admin::getInstance();
                }
            }
        }
    }
}

/**
 * Initializes the main plugin
 *
 * This function returns the singleton instance of the SizechmaWooProductOptions class,
 * ensuring that there is only one instance of the plugin running at any time.
 *
 * @return \SizechmaWooProductOptions The singleton instance of the SizechmaWooProductOptions class.
 */
function SIZEPSGF_WPO() {
    return SizechmaWooProductOptions::getInstance();
}

// Kick-off the plugin by calling the SIZEPSGF_WPO function to initialize the plugin.
SIZEPSGF_WPO();
