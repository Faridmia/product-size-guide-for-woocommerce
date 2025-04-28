<?php
namespace Envatothemely\Sizechartpsgf\Front;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;
use Envatothemely\Sizechartpsgf\Front\SizeCustomCss;
/**
 * Class Front
 * 
 * Handles the front-end functionality for the Ultimate Product Options For WooCommerce plugin.
 */
class SizeCustomCss
{
    use Traitval;

    /**
     * Add custom CSS to the front-end
     */
    public function add_custom_css()
    {
        $sizepsgf_custom_css = get_option('sizepsgf_size_guide_custom_css', false);
        if ($sizepsgf_custom_css) {

            wp_register_style('sizepsgf_custom_css', false, array(), SIZEPSGF_VERSION );
            wp_enqueue_style('sizepsgf_custom_css');
            wp_add_inline_style('sizepsgf_custom_css', $sizepsgf_custom_css);
            
        }
    }
}