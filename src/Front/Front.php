<?php
namespace Envatothemely\Sizechartpsgf\Front;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;
use Envatothemely\Sizechartpsgf\Front\SizeChartProduct\SizeChartProduct;
use Envatothemely\Sizechartpsgf\Front\SizeCustomCss;

/**
 * Class Front
 * 
 * Handles the front-end functionality for the Product Size Guide For WooCommerce plugin.
 */
class Front {
    use Traitval;

    /**
     * @var Options $options_instance An instance of the Options class.
     */
    protected $sizechart_instance;
    protected $custom_css;

    /**
     * Initialize the class
     */
    protected function initialize() {
        $this->init_hooks();
        add_action('wp_head', [ $this, 'sizepsgf_add_generate_custom_css'] );
       
    }

    /**
     * Initialize Hooks
     */
    private function init_hooks() {
       $this->sizechart_instance = SizeChartProduct::getInstance();
        
    }

    /**
     * Add custom CSS to the front-end
     */
    public function sizepsgf_add_generate_custom_css() {
        $border_size                   = get_option('sizepsgf_border_size', '1');
        $sizepsgf_popup_btnbg_color   = get_option('sizepsgf_popup_btnbg_color', '');
        $sizepsgf_popup_btntext_color = get_option('sizepsgf_popup_btntext_color', '');
        $sizepsgf_btn_font_size       = get_option('sizepsgf_btn_font_size', '');
        $sizepsgf_btn_padding         = get_option('sizepsgf_btn_padding', '');
        $sizepsgf_popup_btnicon_color = get_option('sizepsgf_popup_btnicon_color', '');
        $sizepsgf_containerbg_color   = get_option('sizepsgf_containerbg_color', '');


        if (is_numeric($border_size)) {
            $border_size .= 'px';
        }

        if (is_numeric($sizepsgf_btn_font_size)) {
            $sizepsgf_btn_font_size .= 'px';
        }

        $styles = [
            ".sizepsgf-cmfw-sizemodal tr:nth-child(odd)" => [
                'background-color' => get_option('sizepsgf_odd_row_color', ''),
                'color'            => get_option('sizepsgf_font_text_color', '')
            ],
            ".sizepsgf-cmfw-sizemodal tr:nth-child(even)" => [
                'background-color' => get_option('sizepsgf_even_row_color', ''),
                'color'            => get_option('sizepsgf_font_text_color', '')
            ], 
            ".sizepsgf-cmfw-sizemodal td" => [
                'border-color' => get_option('sizepsgf_table_border_color', ''),
                'border-width' => $border_size,
                'border-style' => 'solid'
            ],
            ".sizepsgf-size-guide-btn" => [
                'background-color' => $sizepsgf_popup_btnbg_color,
                'color' => $sizepsgf_popup_btntext_color,
                'padding' => $sizepsgf_btn_padding,
                'font-size' => $sizepsgf_btn_font_size
            ],
            ".sizepsgf-size-guide-btn i" => [
                'color' => $sizepsgf_popup_btnicon_color,
            ],
            ".sizepsgf-sizeguide-popup-modal-content" => [
                'background-color' => $sizepsgf_containerbg_color
            ],

            ".sizepsgf-cmfw-sizemodal th" => [
                'background-color' => get_option('sizepsgf_table_head_bg_color', ''),
                'color'            => get_option('sizepsgf_table_head_font_color', '')
            ] 
        ];

        $custom_style = $this->generate_css($styles);

        if (!empty($custom_style)) {
            wp_register_style('sizepsgf_custom_css_global_options', false, array(), SIZEPSGF_VERSION );
            wp_enqueue_style('sizepsgf_custom_css_global_options');
            wp_add_inline_style('sizepsgf_custom_css_global_options', $custom_style);
        }

        SizeCustomCss::getInstance()->add_custom_css();
        
    }

    /**
     * Generate custom CSS from styles array
     *
     * @param array $styles Array of CSS rules and values.
     * @return string Generated CSS.
     */
    private function generate_css(array $styles) {
        $css = '';

        foreach ( $styles as $selector => $properties ) {
            $css .= esc_html($selector) . ' {';

            foreach ( $properties as $property => $value ) {
                if ( $value !== '' ) {
                    $css .= esc_html($property) . ': ' . esc_html( wp_strip_all_tags($value) ) . '; ';
                }
            }

            $css .= '} ';
        }

        return $css;
    }


}