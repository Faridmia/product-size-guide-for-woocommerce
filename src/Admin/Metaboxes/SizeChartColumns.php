<?php
namespace Envatothemely\Sizechartpsgf\Admin\Metaboxes;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;

/**
 * Class Metaboxes
 * 
 * This class uses the Traitval trait to implement singleton functionality and
 * provides methods for creating custom post types within the Product Size Guide For WooCommerce plugin.
 */
class SizeChartColumns
{
    use Traitval;

    public function __construct()
    {
        add_action('manage_cmfw-size-chart_posts_custom_column', array( $this, 'sizepsgf_size_chart_custom_column_content'), 10, 2) ;
        add_filter('manage_cmfw-size-chart_posts_columns', array( $this, 'sizepsgf_size_chart_add_custom_columns') );
    }

    /**
     * Adds custom columns to the size chart post list in the admin dashboard.
     *
     * @param array $columns The existing columns.
     * @return array The modified columns.
     */
    public function sizepsgf_size_chart_add_custom_columns( $columns ) {

        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];

        $new_columns['category_assign'] = __('Category Assign', 'product-size-guide-for-woocommerce');
        $new_columns['tag_assign'] = __('Tag Assign', 'product-size-guide-for-woocommerce');
        $new_columns['product_assign'] = __('Product Assign', 'product-size-guide-for-woocommerce');
        $new_columns['shortcode'] = __('Shortcode', 'product-size-guide-for-woocommerce');
    
        unset($columns['date']);
        $columns = array_merge($new_columns, $columns);
        $columns['date'] = __('Date', 'product-size-guide-for-woocommerce'); 
        
        return $columns;
    }

    /**
     * Displays custom content in the size chart post list columns in the admin dashboard.
     *
     * @param string $column The name of the column.
     * @param int $post_id The ID of the current post.
     */
    public function sizepsgf_size_chart_custom_column_content($column, $post_id) {
        switch ($column) {
            case 'category_assign':
                $categories = get_post_meta($post_id, '_sizepsgf_selected_categories', true);
                if (!empty($categories) && is_array($categories)) {
                    $category_names = array_map(function($cat_id) {
                        $term = get_term($cat_id, 'product_cat'); 
                        return $term ? $term->name : '';
                    }, $categories);
                    echo !empty($category_names) 
                    ? wp_kses_post(implode(', ', $category_names)) 
                    : esc_html__('No categories assigned', 'product-size-guide-for-woocommerce');
                } else {
                    echo esc_html__('No categories assigned', 'product-size-guide-for-woocommerce');
                }
                break;

            case 'tag_assign':
                $tags = get_post_meta($post_id, '_sizepsgf_selected_tags', true);
                if (!empty($tags) && is_array($tags)) {
                    $tag_names = array_map(function($tag_id) {
                        $term = get_term($tag_id, 'product_tag');
                        return $term ? $term->name : '';
                    }, $tags);
                    echo !empty($tag_names) 
                    ? wp_kses_post(implode(', ', $tag_names)) 
                    : esc_html__('No tags assigned', 'product-size-guide-for-woocommerce');
                } else {
                    echo esc_html__('No tags assigned', 'product-size-guide-for-woocommerce');
                }
                break;

            case 'product_assign':
                $products = get_post_meta($post_id, '_sizepsgf_products', true);
                if (!empty($products) && is_array($products)) {
                    $product_names = array_map(function($product_id) {
                        $product = get_post($product_id);
                        return $product ? $product->post_title : '';
                    }, $products);
                    echo !empty($product_names) 
                    ? wp_kses_post(implode(', ', $product_names)) 
                    : esc_html__('No products assigned', 'product-size-guide-for-woocommerce');
                } else {
                    echo esc_html__('No products assigned', 'product-size-guide-for-woocommerce');
                }
                break;
            case 'shortcode':
                $shortcode    = '[sizepsgf_size_chart id="'. $post_id .'"]';
                    $success_text = __('Copied!', 'product-size-guide-for-woocommerce');
                    $failed_text  = __('Copying to clipboard failed. You should be able to right-click the button and copy.', 'product-size-guide-for-woocommerce');
        
                    printf(
                    "<input type='text' class='shortcode-field' readonly='readonly' value='%s' data-tip='%s' data-tip-failed='%s'>",
                    esc_attr($shortcode),      // Escapes the value attribute
                    esc_attr($success_text),   // Escapes the data-tip attribute
                    esc_attr($failed_text)     // Escapes the data-tip-failed attribute
                );
                break;
        }
    }
    
}