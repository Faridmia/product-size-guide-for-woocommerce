<?php
namespace Envatothemely\Sizechartpsgf\Admin\AdminPanel;
use Envatothemely\Sizechartpsgf\Admin\Ajax\SizeChartAjax;
use Envatothemely\Sizechartpsgf\Admin\AdminPanel\Settings\SizeChartManager;
/**
 * Class Admin
 *
 * This class uses the Traitval trait to implement singleton functionality and
 * provides methods for initializing the admin menu and other admin-related features
 * within the Product Size Guide For WooCommerce plugin.
 */
class AdminPanel {

    protected $size_chart_ajax;
    protected $sizechart_manager;

    /**
     * Initialize the class
     *
     * This method overrides the initialize method from the Traitval trait.
     * It sets up the necessary classes and features for the admin area.
     */

     public function __construct() {

        $this->size_chart_ajax = new SizeChartAjax();
        $this->sizechart_manager = new SizeChartManager();
        $this->initialize_hooks();
    }
    
    protected function initialize_hooks() {

        // swatch variations ajax actions
        add_action( 'wp_ajax_sizepsgf_size_chart_save_options', array($this->size_chart_ajax, 'sizepsgf_size_chart_save_options') );
        add_action( 'wp_ajax_nopriv_sizepsgf_size_chart_save_options', array($this->size_chart_ajax, 'sizepsgf_size_chart_save_options') );

        // color save settings

        add_action( 'wp_ajax_sizepsgf_size_chart_color_save_options', array($this->size_chart_ajax, 'sizepsgf_size_chart_color_save_options') );
        add_action( 'wp_ajax_nopriv_sizepsgf_size_chart_color_save_options', array($this->size_chart_ajax, 'sizepsgf_size_chart_color_save_options') );

        // advanced color settings

        add_action( 'wp_ajax_sizepsgf_advancedsize_color_save_options', array($this->size_chart_ajax, 'sizepsgf_advancedsize_color_save_options') );
        add_action( 'wp_ajax_nopriv_sizepsgf_advancedsize_color_save_options', array($this->size_chart_ajax, 'sizepsgf_advancedsize_color_save_options') );


        // search products ajax

        add_action('wp_ajax_sizepsgf_search_products', array($this->size_chart_ajax,'sizepsgf_search_products'));
        add_action('wp_ajax_nopriv_sizepsgf_search_products', array($this->size_chart_ajax, 'sizepsgf_search_products'));

        add_action('wp_ajax_sizepsgf_export_data', array($this->size_chart_ajax,'sizepsgf_export_data'));
        add_action('wp_ajax_sizepsgf_import_data', array($this->size_chart_ajax,'sizepsgf_import_data'));
        
        
    }
}