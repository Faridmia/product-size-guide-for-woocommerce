<?php
namespace Envatothemely\Sizechartpsgf\Admin\Metaboxes;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;

/**
 * Class Metaboxes
 * 
 * This class uses the Traitval trait to implement singleton functionality and
 * provides methods for creating custom post types within the Product Size Guide For WooCommerce plugin.
 */
class SizeChartMetaBoxes {
    use Traitval;

    /**
     * Constructor
     * 
     * The constructor adds actions to the WordPress hooks for saving post data
     * and adding meta boxes to the post edit screen. These actions ensure that
     * custom metadata is handled properly within the Product Size Guide For WooCommerce plugin.
     */
    public function __construct() {

        // Add an action to the 'save_post' hook to handle saving custom meta data
        add_action('add_meta_boxes', array( $this, 'sizepsgf_add_custom_tag_metaboxes'));
        add_action('add_meta_boxes', array($this, 'sizepsgf_metaboxes_select_product'));
        add_action('add_meta_boxes', array($this, 'sizepsgf_metaboxes_select_category'));
        add_action('add_meta_boxes', array($this,'sizepsgf_add_custom_attribute_metabox'));
        add_action('add_meta_boxes', array($this,'sizepsgf_add_custom_product_metabox'));
        add_action('add_meta_boxes', array($this,'sizepsgf_add_show_hide_metabox'));
        add_action('save_post', array($this, 'sizepsgf_save_metabox_data'));
       
    }

    /**
     * Add a custom meta box for extra item text details.
     *
     * This function hooks into the 'add_meta_boxes' action to add a custom meta box 
     * for entering extra item text details in the product field screen.
     *
     * @since 1.0.0
     *
     * @return void
     */

    function sizepsgf_add_custom_tag_metaboxes() {
        add_meta_box(
            'sizepsgf_chart_table',
            __('Assign Tags', 'product-size-guide-for-woocommerce'),
            array($this, 'sizepsgf_render_chart_tags_metabox_func'), 
            'cmfw-size-chart',
            'side', 
            'default'
        );
    }


    function sizepsgf_metaboxes_select_category() {
        add_meta_box(
            'sizepsgf_chart_table_select_category',
            __('Assign Category', 'product-size-guide-for-woocommerce'), 
            array($this, 'sizepsgf_render_chart_table_category_metabox'), 
            'cmfw-size-chart', 
            'side', 
            'default' 
        );
    }

    function sizepsgf_metaboxes_select_product() {
        add_meta_box(
            'sizepsgf_assign_products_metabox',
            __('Assign Product', 'product-size-guide-for-woocommerce'),
            array($this, 'sizepsgf_render_product_metabox'),
            'cmfw-size-chart',
            'side',
            'default'
        );
    }


    function sizepsgf_add_custom_attribute_metabox() {
        add_meta_box(
            'sizepsgf_product_attributes', 
            __('Assign Attribute', 'product-size-guide-for-woocommerce'), 
            array($this, 'sizepsgf_render_metabox_content'), 
            'cmfw-size-chart', 
            'side' 
        );
    }

    // Meta box to select chart in product page.
    function sizepsgf_add_custom_product_metabox() {
        add_meta_box(
            'sizepsgf_additional_chart',
            __( 'Search/Select Size Chart', 'product-size-guide-for-woocommerce' ),
            array($this, 'sizepsgf_product_size_chart_callback'),
            'product',
            'side',
            'default'
        );
    }

    function sizepsgf_add_show_hide_metabox() {
        add_meta_box(
            'sizepsgf_other_options_metabox', 
            __('Other Options', 'product-size-guide-for-woocommerce'), 
            array($this, 'sizepsgf_render_show_hide_metabox'),                  
            'cmfw-size-chart', 
            'side'                   
        );
    }

    /**
     * Display the Extra Product Data meta box.
     *
     * This function is the callback used by `add_meta_box` to render the content of the
     * extra item text meta box on the product field screen. It retrieves the current value
     * of the 'sizepsgf_product_size_charts' meta field for the current post and displays an input field
     * for editing it.
     *
     * @since 1.0.0
     *
     * @return void
     */
    // Render the metabox
    function sizepsgf_render_chart_tags_metabox_func($post) {
        wp_nonce_field('sizepsgf_save_metabox', 'sizepsgf_metabox_nonce');
        // Fetch all tags
        $tags = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false ]);
        $selected_tags = get_post_meta($post->ID, '_sizepsgf_selected_tags', true) ?: [];

        ?>
        <div class="sizepsgf-general-item cmfw-tag-metabox-item">
			<div class="sizepsgf-gen-item-con cmfw-tags-meta-product-fields-select">
				<select multiple name="sizepsgf_tags[]" class="cmfw-select-product-tags">
					<?php
                        foreach ($tags as $tag) { 
                            $selected = in_array($tag->term_id, $selected_tags) ? "selected='selected'" : ''; ?>
                            <option value="<?php echo esc_attr($tag->term_id); ?>" <?php echo wp_kses_post( $selected); ?>><?php echo esc_html($tag->name); ?></option>
                        <?php }
					?>
				</select>
			</div>
		</div>
        <?php
    }

    /**
     * Renders a metabox for selecting product categories for the size chart.
     *
     * @param WP_Post $post The current post object.
     * 
     * @since 1.0.0
     * @return void
     */
    public function sizepsgf_render_chart_table_category_metabox($post) {

        $selected_categories = get_post_meta($post->ID, '_sizepsgf_selected_categories', true) ?: [];
        $this->sizepsgf_render_select_product_cat_field('Select Categories', 'sizepsgf_select_product_categories', $selected_categories ); 
        
    }
   
    /**
     * Renders a metabox for selecting products to apply the size chart.
     *
     * @param WP_Post $post The current post object.
     * 
     * @since 1.0.0
     * @return void
     */
    public function sizepsgf_render_product_metabox($post) {
       
        wp_nonce_field('sizepsgf_save_metabox', 'sizepsgf_metabox_nonce');
        $checked_products = get_post_meta($post->ID, '_sizepsgf_products', true) ?: [];
        $apply_all_product = get_post_meta($post->ID, '_sizepsgf_apply_all_product', true);

    
        ?>
        <div class="sizepsgf-apply-all-product">
            <label><?php echo esc_html__("Apply On All Product?","product-size-guide-for-woocommerce"); ?>
                <input type="checkbox" name="sizepsgf_apply_all_product" value="1" <?php checked($apply_all_product, '1'); ?>>
            </label>
        </div>
        <div id="cmfw-metabox">
            <ul class="sizepsgf-tabs">
                <li><a href="#cmfw-tab-products"><?php esc_html_e('Products', 'product-size-guide-for-woocommerce'); ?></a></li>
                <li><a href="#cmfw-tab-search"><?php esc_html_e('Search', 'product-size-guide-for-woocommerce'); ?></a></li>
            </ul>
            <div id="cmfw-tab-products" class="sizepsgf-tab-content">
                <ul id="cmfw-products-list">
                    <?php
                    if (!empty($checked_products)) {
                        foreach ($checked_products as $product_id) {
                            $product = get_post($product_id);
                            if ($product && $product->post_type === 'product') { ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="sizepsgf_products[]" value="<?php echo esc_attr($product->ID); ?>" checked>
                                        <?php echo esc_html($product->post_title); ?>
                                    </label>
                                </li>
                            <?php }
                        }
                    } else {
                        $no_products = '<li>' . esc_html__('No products saved yet.', 'product-size-guide-for-woocommerce') . '</li>';
                        echo wp_kses_post( $no_products );
                    }
                    ?>
                </ul>
            </div>
            <div id="cmfw-tab-search" class="sizepsgf-tab-content" style="display: none;">
                <input type="search" id="sizepsgf-product-search" placeholder="<?php esc_attr_e('Search product name', 'product-size-guide-for-woocommerce'); ?>" autocomplete="off">
                <ul id="cmfw-search-results"></ul>
            </div>
        </div>
        <?php
    }

    /**
     * Renders the metabox for selecting size charts to be associated with a product.
     *
     * @param WP_Post $post The current post object.
     * 
     * @since 1.0.0
     * @return void
     */
    public function sizepsgf_product_size_chart_callback($post) {
        
        wp_nonce_field('sizepsgf_save_metabox', 'sizepsgf_metabox_nonce');
        $checked_size_chart = get_post_meta($post->ID, '_sizepsgf_size_chart_product', true) ?: [];
        $args = array(
            'post_type'      => 'cmfw-size-chart',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
    
        $size_charts = get_posts($args);
        ?>
        <div class="sizepsgf-general-item cmfw-tag-metabox-item">
            <div class="sizepsgf-gen-item-con cmfw-meta-product-fields-select">
                <select multiple name="sizepsgf_size_chart[]" class="cmfw-select-product-sizechart">
                    <?php
                    if (!empty($size_charts)) {
                        foreach ($size_charts as $size_chart) {
                            $chart_id = $size_chart->ID;
                            $selected = in_array($chart_id, $checked_size_chart) ? "selected='selected'" : '';
                            ?>
                            <option value="<?php echo esc_attr($chart_id); ?>" <?php echo esc_attr( $selected ); ?>>
                                <?php echo esc_html($size_chart->post_title); ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
    }
    
    /**
     * Renders the metabox content for grouped product attributes in WooCommerce.
     *
     * @param WP_Post $post The current post object.
     * 
     * @since 1.0.0
     * @return void
     */
    public function sizepsgf_render_metabox_content($post) {

        // Get all WooCommerce product attributes
        $product_attributes = wc_get_attribute_taxonomies();
        $get_grouped_attributes = get_post_meta($post->ID, '_sizepsgf_grouped_attributes', true) ?: [];
        wp_nonce_field('sizepsgf_save_metabox', 'sizepsgf_metabox_nonce');
        
        ?>
        <select id="sizepsgf_grouped_attributes" name="sizepsgf_grouped_attributes[]" multiple="multiple">
            <?php 
            if (!empty($product_attributes)) {
                foreach ($product_attributes as $key => $attribute) {

                    $selected_attribute = isset( $get_grouped_attributes[$key] ) ? $get_grouped_attributes[$key] : '';
                    $taxonomy = 'pa_' . $attribute->attribute_name;
                    $terms = get_terms(array(
                        'taxonomy' => $taxonomy,
                        'hide_empty' => false,
                    ));

                    if (!empty($terms) && !is_wp_error($terms)) { ?>
                        <optgroup label="<?php esc_html($attribute->attribute_label); ?>">
                        <?php 
                            foreach ($terms as $term) {
                                $value = $taxonomy . '|' . $term->slug;
                                $selected = in_array($value, $get_grouped_attributes) ? "selected='selected'" : ''; ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php echo wp_kses_post( $selected); ?>><?php echo esc_html($term->name); ?></option>
                            <?php }
                        ?>
                        </optgroup>
                        <?php
                    }
                }
            } 
            ?>
        </select>
    <?php 
    }

    /**
     * Renders the show/hide metabox for the size chart options.
     *
     * @param WP_Post $post The current post object.
     * 
     * @since 1.0.0
     * @return void
     */
    function sizepsgf_render_show_hide_metabox( $post ) {

        wp_nonce_field('sizepsgf_save_metabox', 'sizepsgf_metabox_nonce');
        // Retrieve current values
        $hide_thumbnail = get_post_meta($post->ID, '_sizepsgf_hide_thumbnail', true);
        $hide_description = get_post_meta($post->ID, '_sizepsgf_hide_description', true);
        $hide_chart_table = get_post_meta($post->ID, '_sizepsgf_hide_chart_table', true);
        ?>
        <div>
            <label>
                <input type="checkbox" name="sizepsgf_hide_thumbnail" value="1" <?php checked($hide_thumbnail, '1'); ?>>
                <?php echo esc_html__("Hide Thumbnail","product-size-guide-for-woocommerce"); ?>
            </label><br>
            <label>
                <input type="checkbox" name="sizepsgf_hide_description" value="1" <?php checked($hide_description, '1'); ?>>
                <?php echo esc_html__("Hide Description","product-size-guide-for-woocommerce"); ?>
            </label><br>
            <label>
                <input type="checkbox" name="sizepsgf_hide_chart_table" value="1" <?php checked($hide_chart_table, '1'); ?>>
                <?php echo esc_html__("Hide Chart Table","product-size-guide-for-woocommerce"); ?>
            </label>
        </div>
        <?php
    }

    /**
     * Save the Extra Product Data meta field.
     *
     * This function saves the value of the 'sizepsgf_product_size_charts' meta field when a post is saved.
     * It verifies a nonce to ensure the request is legitimate, then updates the meta field
     * with the value from the form input.
     *
     * @since 1.0.0
     *
     * @param int $post_id The ID of the post being saved.
     * @return int The post ID if the nonce is invalid.
     */

     // Save the metabox data
    function sizepsgf_save_metabox_data($post_id) {

        // Verify nonce
 
        if ( ! isset( $_POST['sizepsgf_metabox_nonce'] ) 
            || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['sizepsgf_metabox_nonce'] ) ) , 'sizepsgf_save_metabox' ) ) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check permissions
        $post_type = get_post_type($post_id);
        if ('cmfw-size-chart' === $post_type) {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        }

        // Fields to sanitize and save
        $fields = [
            'sizepsgf_select_product_categories' => '_sizepsgf_selected_categories',
            'sizepsgf_attributes' => '_sizepsgf_selected_attributes',
            'sizepsgf_tags' => '_sizepsgf_selected_tags',
            'sizepsgf_products' => '_sizepsgf_products',
            'sizepsgf_size_chart' => '_sizepsgf_size_chart_product',
            'sizepsgf_grouped_attributes' => '_sizepsgf_grouped_attributes',
            'sizepsgf_apply_all_product' => '_sizepsgf_apply_all_product',
            'sizepsgf_hide_thumbnail' => '_sizepsgf_hide_thumbnail',
            'sizepsgf_hide_description' => '_sizepsgf_hide_description',
            'sizepsgf_hide_chart_table' => '_sizepsgf_hide_chart_table',
        ];

        foreach ( $fields as $field => $meta_key ) {

            $value = isset( $_POST[$field] ) 
            ? (is_array( $_POST[$field] ) 
                ? array_map('sanitize_text_field', wp_unslash( $_POST[$field] )) 
                : sanitize_text_field(wp_unslash( $_POST[$field] ) )
            ) 
            : '0';
        
            $value = sizepsgf_sanitize_custom_field_items($value);
        
            update_post_meta($post_id, $meta_key, $value);
        }

    }
}