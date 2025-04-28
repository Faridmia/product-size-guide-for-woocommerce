<?php
namespace Envatothemely\Sizechartpsgf\Admin\AdminPanel\Settings;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;
/**
 * Class SortingManager
 * 
 * Manages sorting-related settings for the Product Size Guide For WooCommerce plugin.
 */
class SizeChartManager
{
    use Traitval;
    private $options = [];
    /**
     * Constructor
     * 
     * The constructor adds actions to the WordPress hooks for saving post data
     * and adding meta boxes to the post edit screen. These actions ensure that
     * custom metadata is handled properly within the Product Size Guide For WooCommerce plugin.
     */
    public function __construct()
    {
        // Add an action to the 'save_post' hook to handle saving custom meta data
        add_action('admin_menu', array( $this, 'sizepsgf_add_size_settings_submenu') );
        add_action('admin_head', array( $this,'sizepsgf_disable_other_plugin_notices') );
       
    }

    /**
     * Retrieves general size chart options and stores them in the options array.
     *
     * @return void
     */
    public function get_sizepsgf_size_general_options() {
        $options = [
            'size_chart_enabled'                 => $this->get_option_checked('sizepsgf_size_chart_enable_disable'),
            'sizepsgf_size_chart_position'      => get_option('sizepsgf_size_chart_position', ''),
            'sizepsgf_layout_style'             => get_option('sizepsgf_layout_style', ''),
            'sizepsgf_sizechart_popup_position' => get_option('sizepsgf_sizechart_popup_position', ''),
            'size_chart_tab_title'               => get_option('sizepsgf_size_chart_tab_title', ''),
            'sizepsgf_even_row_color'           => get_option('sizepsgf_even_row_color', ''),
            'sizepsgf_odd_row_color'            => get_option('sizepsgf_odd_row_color', ''),
            'sizepsgf_font_text_color'          => get_option('sizepsgf_font_text_color', ''),
            'size_guide_heading_title'           => get_option('sizepsgf_size_heading_title', ''),
            'size_guide_content'                 => get_option('sizepsgf_size_guide_content', ''),
            'sizepsgf_table_border_color'       => get_option('sizepsgf_table_border_color', ''),
            'sizepsgf_border_size'              => get_option('sizepsgf_border_size', ''),
            'sizepsgf_popup_btnbg_color'        => get_option('sizepsgf_popup_btnbg_color', ''),
            'sizepsgf_popup_btntext_color'      => get_option('sizepsgf_popup_btntext_color', ''),
            'sizepsgf_popup_btnicon_color'      => get_option('sizepsgf_popup_btnicon_color', ''),
            'sizepsgf_containerbg_color'        => get_option('sizepsgf_containerbg_color', ''),
            'sizepsgf_btn_font_size'            => get_option('sizepsgf_btn_font_size', ''),
            'sizepsgf_btn_padding'              => get_option('sizepsgf_btn_padding', ''),
            'size_guide_custom_css'              => get_option('sizepsgf_size_guide_custom_css', ''),
            'sizepsgf_table_head_bg_color'      => get_option('sizepsgf_table_head_bg_color', ''),
            'sizepsgf_table_head_font_color'    => get_option('sizepsgf_table_head_font_color', ''),

            
        ];


        foreach ($options as $key => $default) {
            $this->options[$key] = get_option( $key, $default );
        }

    }
    
    /**
     * disable other plugin notice function
     * remove admin notice from current screen
     *
     * @return void
     */
    function sizepsgf_disable_other_plugin_notices() {

        $current_screen = get_current_screen();

        if ( ( $current_screen && $current_screen->id === 'cmfw-size-chart_page_cmfw-size-settings')  
        || ( $current_screen->id === 'edit-cmfw-size-chart') 
        || ( $current_screen->id === 'cmfw-size-chart') ) {
            remove_all_actions('admin_notices'); // Remove notices from admin_notices
            remove_all_actions('all_admin_notices'); // Remove notices from all_admin_notices
        }
    }

    /**
     * settings submenu function
     *
     * @return void
     */
    function sizepsgf_add_size_settings_submenu() {
        if ( !current_user_can('manage_options') ) {
            return;
        }
        add_submenu_page(
            'edit.php?post_type=cmfw-size-chart',
            __('Size Settings', 'product-size-guide-for-woocommerce'), 
            __('Size Settings', 'product-size-guide-for-woocommerce'), 
            'manage_options',  
            'cmfw-size-settings', 
            array( $this, 'sizepsgf_render_size_settings_page')
        );
       
    }

    /**
     * render size settings function
     * size chart settings page save options
     * @return void
     */
    function sizepsgf_render_size_settings_page() {

        if ( !current_user_can('manage_options') ) {
            return;
        }

        $options = $this->get_sizepsgf_size_general_options();
        $size_chart_pos_modal = $this->options['sizepsgf_size_chart_position'];

        ?>

        <div class="sizepsgf-settings-wrapper">
            <div class="cmfw-tab-left-wrapper">
                <div class="sizepsgf-tabs">
                    <button class="sizepsgf-tab-link active" onclick="openTab(event, 'general')"><?php echo esc_html__("General Settings","product-size-guide-for-woocommerce"); ?></button>
                    <button class="sizepsgf-tab-link" onclick="openTab(event, 'settings')"><?php echo esc_html__("Table Size Style","product-size-guide-for-woocommerce"); ?></button>
                    <button class="sizepsgf-tab-link" onclick="openTab(event, 'advancedcolor')"><?php echo esc_html__("Advanced Color","product-size-guide-for-woocommerce"); ?></button>
                    
                </div>
            </div>
            <div class="sizepsgf-content-right-wrapper">
                <div id="general" class="sizepsgf-tab-content-settings active-tab">
                    <form action="" method="post" class="cmfw-settings-general-tab">
                        <?php $this->sizepsgf_render_checkbox_item('Size Chart Enable?', 'sizepsgf_size_chart_enable_disable', $this->options['size_chart_enabled']); 
                         
                            $this->sizepsgf_render_select_itemt(
                                'sizepsgf_layout_style',
                                'Layout Style',
                                [
                                    'cmfw-layout-style1' => 'Layout 1',
                                    'cmfw-layout-style2' => 'Layout 2',
                                    'cmfw-layout-style3' => 'Layout 3'
                                ],
                                'sizepsgf_size_chart_layout',
                                'hide'
                            ); 

                            $this->sizepsgf_render_select_itemt(
                                'sizepsgf_size_chart_position',
                                'Size Chart Display',
                                [
                                    'cmfw-additional-tab' => 'Additional Tab',
                                    'cmfw-popup-modal' => 'Popup Modal'
                                ],
                                'sizepsgf_size_chart_display',
                                'hide'
                            ); 
                            
                            
                            $this->sizepsgf_render_select_itemt(
                                'sizepsgf_sizechart_popup_position',
                                'Size Chart Button Position',
                                [
                                    'woocommerce_after_add_to_cart_form' => 'After - Add to cart',
                                    'woocommerce_before_add_to_cart_form' => 'Before - Add to cart',
                                    'woocommerce_product_meta_end' => 'After - Product Meta',
                                    'woocommerce_product_meta_start' => 'Before - Product Meta',
                                    'woocommerce_single_product_summary' => 'Before - Product summary',
                                    'woocommerce_after_single_product_summary' => 'After - Product summary'
                                ],
                                'sizepsgf_size_chart_popup_position'
                            );
                            
                            
                            $this->sizepsgf_render_text_input('Tab Title Text', 'sizepsgf_size_chart_tab_title', $this->options['size_chart_tab_title'], 'Size Chart', 'cmfw-tab-title'); 
                       
                       
                            $this->sizepsgf_render_text_input('Size Guide Heading', 'sizepsgf_size_heading_title', $this->options['size_guide_heading_title'], 'Size Guide','cmfw-tab-heading-title'); 
                       
                        
                            $this->sizepsgf_render_textarea_input('Size Guide Content', 'sizepsgf_size_guide_content', $this->options['size_guide_content'], 'Content', 'sizepsgf-tab-content-field'); 
                        
                        ?>
                        <p class="submit cmfw-save-button-option"><input type="submit" name="sizepsgf_size_chart_submit" id="sizepsgf-size-chart-submit" class="button button-primary" value="Save Changes"></p>
                    </form>
                </div>
                <div id="settings" class="sizepsgf-tab-content-settings">
                    <form action="" method="post" class="cmfw-settings-color-normal-tab">
                    <?php 

                        $this->sizepsgf_render_color_input(
                            'Table Head Background Color',
                            'sizepsgf_table_head_bg_color',
                            $this->options['sizepsgf_table_head_bg_color']
                        ); 
                        
                        $this->sizepsgf_render_color_input(
                            'Table Head Font Color',
                            'sizepsgf_table_head_font_color',
                            $this->options['sizepsgf_table_head_font_color']
                        
                        ); 

                        $this->sizepsgf_render_color_input(
                            'Select Color for Even Rows',
                            'sizepsgf_even_row_color',
                            $this->options['sizepsgf_even_row_color']
                        ); 
                     
                        $this->sizepsgf_render_color_input(
                            'Select Color for Odd Rows',
                            'sizepsgf_odd_row_color',
                            $this->options['sizepsgf_odd_row_color']
                           
                        ); 
                     
                        $this->sizepsgf_render_color_input(
                            'Table Font Text Color',
                            'sizepsgf_font_text_color',
                            $this->options['sizepsgf_font_text_color']
                        ); 

                        $this->sizepsgf_render_border_size_input_func('Border size', 'sizepsgf_border_size', $this->options['sizepsgf_border_size'], '1'); 
                     
                        $this->sizepsgf_render_color_input(
                            'Table Border Color',
                            'sizepsgf_table_border_color',
                            $this->options['sizepsgf_table_border_color']
                        ); 
                    
                        $this->sizepsgf_render_textarea_input('Custom Css', 'sizepsgf_size_guide_custom_css', $this->options['size_guide_custom_css'], '', 'sizepsgf-tab-content-field'); 
                        
                        ?>


                    <p class="submit cmfw-save-button-color-option"><input type="submit" name="sizepsgf_size_chart_color_save" id="sizepsgf-size-chart-color-save" class="button button-primary sizepsgf-size-chart-color-save" value="Save Changes"></p>
                    </form>
                </div>
                <div id="advancedcolor" class="sizepsgf-tab-content-settings">
                    <form action="" method="post" class="cmfw-advancedcolor-color-normal-tab">
                    <?php 
                        $this->sizepsgf_render_color_input(
                            'Popup Button BG Color',
                            'sizepsgf_popup_btnbg_color',
                            $this->options['sizepsgf_popup_btnbg_color']
                        ); 

                        $this->sizepsgf_render_color_input(
                            'Popup Button text Color',
                            'sizepsgf_popup_btntext_color',
                            $this->options['sizepsgf_popup_btntext_color']
                        ); 

                        $this->sizepsgf_render_color_input(
                            'Popup Button Icon Color',
                            'sizepsgf_popup_btnicon_color',
                            $this->options['sizepsgf_popup_btnicon_color']
                        ); 
                        $this->sizepsgf_render_border_size_input_func('Popup Button Font size', 'sizepsgf_btn_font_size', $this->options['sizepsgf_btn_font_size'], '16'); 
                        $this->sizepsgf_render_border_size_input_func('Popup Button Padding', 'sizepsgf_btn_padding', $this->options['sizepsgf_btn_padding'], '6px 15px');
                        ?>
                        <hr/>
                        <?php
                        $this->sizepsgf_render_color_input(
                            'Container Background Color',
                            'sizepsgf_containerbg_color',
                            $this->options['sizepsgf_containerbg_color']
                        ); 
                    ?>

                    <p class="submit cmfw-save-button-color-option"><input type="submit" name="sizepsgf_size_chart_advanced_color_save" id="sizepsgf-size-chart-advanced-color-save" class="button button-primary sizepsgf-size-chart-color-save" value="Save Changes"></p>
                    </form>
                </div>
                
            </div>
        </div>
        <?php
    } 
}