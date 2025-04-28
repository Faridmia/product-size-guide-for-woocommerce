<?php
namespace Envatothemely\Sizechartpsgf\Front\SizeChartProduct;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;

/**
 * Class SizeChartProduct
 * 
 * Handles the front-end functionality for the Product Size Guide For WooCommerce plugin.
 */
class SizeChartProduct {
    use Traitval;

    public $settings = [];

    
    public $size_chart_store_id = [];

    /**
     * Constructor for initializing settings and hooking necessary actions.
     */
    public function __construct() {

        $this->settings = [
            'size_chart_enabled'             => get_option('sizepsgf_size_chart_enable_disable'),
            'sizepsgf_size_chart_position'  => get_option('sizepsgf_size_chart_position'),
            'size_chart_tab_title'           => get_option('sizepsgf_size_chart_tab_title'),
            'sizepsgf_even_row_color'       => get_option('sizepsgf_even_row_color'),
            'sizepsgf_odd_row_color'        => get_option('sizepsgf_odd_row_color'),
            'sizepsgf_font_text_color'      => get_option('sizepsgf_font_text_color'),
            'sizepsgf_size_heading_title'   => get_option('sizepsgf_size_heading_title'),
            'size_guide_content'             => get_option('sizepsgf_size_guide_content'),
            'sizepsgf_layout_style'         => get_option('sizepsgf_layout_style'),
        ];


        if ( $this->settings['size_chart_enabled'] != '1' ) {
            return;
        }
       
        add_action('template_redirect', [$this, 'init_template_redirect']);
    }

    public function init_template_redirect() {
        if (!is_product()) {
            return; 
        }
    
        $product = $this->get_current_product();
        if (!$product) {
            return; 
        }
    
        $size_chart_id = $this->get_matching_size_chart_id($product->get_id());
        $this->handle_size_chart_display($size_chart_id);
    
        // Shortcode Registration for Size Chart (keep it here)
        add_shortcode('sizepsgf_size_chart', [$this, 'sizepsgf_size_chart_shortcode']);
    }
    
    private function get_current_product() {
        global $post;
        if (!$post) {
            return null;
        }
        return wc_get_product($post->ID);
    }
    
    private function get_matching_size_chart_id($product_id) {
        $size_chart = $this->get_matching_size_chart($product_id);
        return $size_chart ? $size_chart['id'] : 0;
    }
    
    private function handle_size_chart_display($size_chart_id) {
        if (!$size_chart_id) {
            return; 
        }
    
        $settings = get_post_meta($size_chart_id, 'sizepsgf_settings', true);
        $position = get_post_meta($size_chart_id, 'sizepsgf_chart_position', true);
    
        if ($settings === 'page_post_settings') {
            $this->handle_custom_settings_display($position );
        } else {
            $this->handle_global_settings_display($position );
        }
    }
    
    private function handle_custom_settings_display($position ) {

       
        if ($position === 'modal') {
            $this->add_popup_action();
        } else {
            $this->add_tab_filter();
        }
    }
    
    private function handle_global_settings_display($position ) {

        if ($this->settings['sizepsgf_size_chart_position'] == 'cmfw-popup-modal') {
            $this->add_popup_action();
        } else {
            $this->add_tab_filter();
        }
    }
    
    private function add_popup_action() {
        $size_chart_pos = get_option('sizepsgf_sizechart_popup_position');
        add_action($size_chart_pos, [$this, 'sizepsgf_size_product_button']);
    }
    
    private function add_tab_filter() {
        add_filter('woocommerce_product_tabs', [$this, 'sizepsgf_size_guide_tab'], 1005);
    }
   

    /**
     * Renders the size chart button with a popup type.
     *
     * @return void
     */
    public function sizepsgf_size_product_button() {

        $short_code_attrs = [
            'type' => 'popup',
        ];

        echo wp_kses_post(sizepsgf_do_shortcode('sizepsgf_size_chart', $short_code_attrs));
    }

    /**
     * Add a custom Size Guide tab to the WooCommerce product page.
     *
     * @param array $tabs Existing product tabs.
     * @return array Modified product tabs with the custom Size Guide tab.
     */
    public function sizepsgf_size_guide_tab( $tabs ) {

        $size_chart_tab_title = $this->settings['size_chart_tab_title'] 
            ? $this->settings['size_chart_tab_title'] 
            : __('Size Chart', 'product-size-guide-for-woocommerce');

        $tabs['sizepsgf_size_guide'] = [
            'title'    => $size_chart_tab_title,
            'priority' => 50,
            'callback' => [$this, 'sizepsgf_size_product_tab_content_shortcode'],
        ];

        return $tabs;
    }

    /**
     * Render the content for the custom Size Guide tab using a shortcode.
     *
     * This function outputs the content of the custom Size Guide tab
     * by executing the `sizepsgf_size_chart` shortcode with attributes.
     *
     * @return void
     */
    public function sizepsgf_size_product_tab_content_shortcode() {

        $short_code_attrs = [];
        echo wp_kses_post( sizepsgf_do_shortcode('sizepsgf_size_chart', $short_code_attrs) );

    }

    /**
     * Handles the `sizepsgf_size_chart` shortcode.
     *
     * @param array  $atts    Shortcode attributes.
     * @param string $content Content between shortcode tags.
     * @return string Rendered HTML output for the size chart.
     */
    public function sizepsgf_size_chart_shortcode( $atts, $content = '' ) {
        // Exit early if not on shop, product, or product archive pages
        if ( ! is_shop() && ! is_singular( 'product' ) && ! is_post_type_archive( 'product' ) ) {
            return;
        }
    
        global $product;
    
        // Default shortcode attributes
        $atts = shortcode_atts(
            [
                'type' => '',
                'id'   => null,
            ],
            $atts,
            'sizepsgf_size_chart'
        );
    
        // Determine the size chart ID
        if ( ! $atts['id'] ) {
            $size_chart = $this->get_matching_size_chart( $product->get_id() );
            if ( $size_chart ) {
                $atts['id'] = $size_chart['id'];
            }
        }

        $this->size_chart_store_id[] = $size_chart['id'];
    
        if ( ! $atts['id'] ) {
            return;
        }
    
        $size_chart_id = $atts['id'];
    
        // Fetch chart metadata
        $hide_thumbnail   = get_post_meta( $size_chart_id, '_sizepsgf_hide_thumbnail', true );
        $hide_description = get_post_meta( $size_chart_id, '_sizepsgf_hide_description', true );
        $hide_chart_table = get_post_meta( $size_chart_id, '_sizepsgf_hide_chart_table', true );

        $sizepsgf_settings         = get_post_meta($size_chart_id, 'sizepsgf_settings', true);
        $sizepsgf_chart_position  = get_post_meta($size_chart_id, 'sizepsgf_chart_position', true);
        $sizepsgf_popup_icon_input = get_post_meta($size_chart_id, 'sizepsgf_popup_icon_input', true);
        $sizepsgf_layout_style     = get_post_meta($size_chart_id, 'sizepsgf_layout_style', true);

        $is_popup = false;
        if( $sizepsgf_settings == 'page_post_settings' ) {
            $is_popup              = ( $sizepsgf_chart_position === 'modal' );
            $size_chart_layout = ($sizepsgf_layout_style === 'sizepsgf_layout_2') ? 'sizepsgf-layout-two' : (($sizepsgf_layout_style === 'sizepsgf_layout_3') ? 'sizepsgf-layout-three' : '');
        }  else {
           $is_popup              = ( $atts['type'] === 'popup' );
           
           $size_chart_layout = ($this->settings['sizepsgf_layout_style'] === 'cmfw-layout-style2') ? 'sizepsgf-layout-two' : (($this->settings['sizepsgf_layout_style'] === 'cmfw-layout-style3') ? 'sizepsgf-layout-three' : '');
        }
    
        // Modal settings
        $modal_tab_class       = $is_popup ? 'sizepsgf-sizeguide-modal' : 'cmfw-sizeguide-tab';
        $modal_content_class   = $is_popup ? 'sizepsgf-sizeguide-popup-modal-content' : 'sizepsgf-sizechart-modal-content';
        $modal_class           = 'sizepsgf-cmfw-sizemodal sizepsgf-size-additional-tab' . ( $is_popup ? ' ' : ' cmfw-size-modal-extra' );
        $heading_title         = $this->settings['sizepsgf_size_heading_title'] ?? __( 'Size Guide', 'product-size-guide-for-woocommerce' );
        
        $guide_content         = $this->settings['size_guide_content'] ?? __( 'This is an approximate conversion table to help you find your size.', 'product-size-guide-for-woocommerce' );
    
        // Start output buffering
        ob_start();
    
        if ( $is_popup ) {
            ?>
            <!-- Size Guide Button -->
            <button id="cmfw-sizeguidebtn" class="sizepsgf-size-guide-btn">
                <i class="fas fa-ruler"></i> 
                <?php echo esc_html__( 'Size Guide', 'product-size-guide-for-woocommerce' ); ?>
            </button>
            <?php
        }
        ?>
    
        <!-- Render modal structure -->
        <div id="<?php echo esc_attr( $modal_tab_class ); ?>" class="<?php echo esc_attr( $modal_class ); ?>">
            <div class="<?php echo esc_attr( $modal_content_class . ' ' . $size_chart_layout ); ?>">
                <?php if ( $is_popup ) : ?>
                    <span class="sizepsgf-sizechart-close">&times;</span>
                    <h2><?php echo esc_html( $heading_title ); ?></h2>
                    <p><?php echo esc_html( $guide_content ); ?></p>
                <?php endif; 
                    $img_chart = '';
                    $hide_img_desc = '';
                    $description_html = $this->sizepsgf_render_size_chart_desc($size_chart_id);
                    $thumbnail_html = $this->sizepsgf_render_size_chart_thumbnail($size_chart_id);
                    if ( ( $hide_description !== '1' && empty(trim($description_html) ) && ( $hide_thumbnail !== '1' && empty(trim($thumbnail_html)))) ) :
                        $img_chart = 'image-and-chart';
                    endif;

                    if  ( $hide_description == '1'  &&  $hide_thumbnail == '1'  ) :
                        $hide_img_desc = 'hide-image-and-desc';
                    endif;
                ?>
    
                <div class="cmfw-size-chart-image-and-chart <?php echo esc_attr( $img_chart ); ?> <?php echo esc_attr( $hide_img_desc ); ?>">
                <?php
                    if ($hide_description !== '1' || $hide_thumbnail !== '1') :
                        

                        $description_show_hide = ($hide_description === '1' || empty(trim($description_html))) ? 'cmfw-desc-hidden' : '';
                        $image_show_hide = ($hide_thumbnail === '1' || empty(trim($thumbnail_html))) ? 'sizepsgf-image-hidden' : '';
                    ?>
                        <div class="sizepsgf-size-chart-chart-image-desc <?php echo esc_attr($description_show_hide . ' ' . $image_show_hide); ?>">
                            <?php
                            if ($hide_description !== '1' && !empty(trim($description_html))) {
                                echo wp_kses_post($description_html);
                            }
                            if ($hide_thumbnail !== '1' && !empty(trim($thumbnail_html))) {
                                echo wp_kses_post($thumbnail_html);
                            }
                            ?>
                        </div>
                    <?php endif; ?>
    
                    <?php if ( $hide_chart_table !== '1' ) : ?>
                        <div class="sizepsgf-size-chart-table-content">
                            <?php 
                            if ( is_array( $size_chart['rows'] ) || is_object( $size_chart['rows'] ) ) { 
                                echo wp_kses_post( $this->render_size_chart_rows( $size_chart['rows']['rows'] ) ); 
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    
        <?php
        // Return the buffered output
        return ob_get_clean();
    }
    
    
    /**
     * Get the matching size chart for a product.
     *
     * @param int $product_id The product ID to check.
     * @return array|null The matching size chart data or null if no chart matches.
     */
    public function get_matching_size_chart( $product_id ) {
        $product_categories = wc_get_product_term_ids($product_id, 'product_cat');
        $product_tags       = wc_get_product_term_ids($product_id, 'product_tag');

        $args = [
            'post_type'      => 'cmfw-size-chart',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $size_chart_query = new \WP_Query($args);

        if ($size_chart_query->have_posts()) {
            while ($size_chart_query->have_posts()) {
                $size_chart_query->the_post();
                $chart_id = get_the_ID();

                if ($this->check_size_chart_conditions($chart_id, $product_id, $product_categories, $product_tags)) {
                    
                    wp_reset_postdata();
                    return [
                        'id'   => $chart_id,
                        'rows' => json_decode(get_post_meta($chart_id, '_size_chart_data', true), true),
                    ];
                }
            }
        }

        wp_reset_postdata();
        return null;
    }

    /**
     * Determines if a size chart applies to a specific product.
     *
     * @param int   $chart_id          The size chart ID.
     * @param int   $product_id        The product ID.
     * @param array $product_categories Array of product category IDs.
     * @param array $product_tags       Array of product tag IDs.
     * @return bool True if the size chart applies, false otherwise.
     */
    public function check_size_chart_conditions( $chart_id, $product_id, $product_categories, $product_tags ) {

        $apply_all_product = get_post_meta($chart_id, '_sizepsgf_apply_all_product', true);
        $selected_categories = get_post_meta($chart_id, '_sizepsgf_selected_categories', true) ?: [];
        $selected_tags = get_post_meta($chart_id, '_sizepsgf_selected_tags', true) ?: [];
        $checked_products = get_post_meta($chart_id, '_sizepsgf_products', true) ?: [];

        $checked_products = is_array($checked_products) ? $checked_products : explode(',', $checked_products);

        return (
            $apply_all_product == '1' ||
            array_intersect( $product_categories, $selected_categories ) ||
            array_intersect( $product_tags, $selected_tags ) ||
            in_array( $product_id, $checked_products )
        );

    }

    /**
     * Renders the thumbnail for a given size chart.
     *
     * @param int $chart_id The size chart ID.
     * @return string|null The HTML for the size chart thumbnail or null if not available.
     */
    public function sizepsgf_render_size_chart_thumbnail( $chart_id ) {
        
        if (!is_numeric($chart_id) || $chart_id <= 0) {
            return;
        }
    
        // Fetch the post using the ID
        $chart_post = get_post($chart_id);
    
        if ( $chart_post && !is_wp_error( $chart_post ) ) {
            $thumbnail_id = get_post_thumbnail_id($chart_id);
            $thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'large');
    
            if ($thumbnail_html) {
                $output = '<div class="sizepsgf-size-chart-thumbnail">';
                $output .= $thumbnail_html;
                $output .= '</div>';
                return $output;
            } 
        }
    
        return null;
    }

    /**
     * Renders the description for a given size chart.
     *
     * @param int $chart_id The size chart ID.
     * @return string|null The HTML for the size chart description or null if not available.
     */
    public function sizepsgf_render_size_chart_desc( $chart_id ) {

        if (!is_numeric($chart_id) || $chart_id <= 0) {
            return;
        }
       
        $chart_post = get_post($chart_id);
    
        if ($chart_post && !is_wp_error($chart_post)) {
            // Retrieve the content of the post
            $content = apply_filters('the_content', $chart_post->post_content);
    
            $output = '';
            if(!empty($content)) {
                $output .= '<div class="sizepsgf-size-chart-desc">';
                $output .= $content; // The content of the specific chart
                $output .= '</div>';
            }
    
            return $output;
        }
    
        // Return an empty output if no chart is found
        return null;
    }
    
    /**
     * Renders the rows of a size chart as an HTML table.
     *
     * @param array $rows An array of rows, where each row is an array of cell values.
     * @return string The HTML for the size chart table.
     */
    public function render_size_chart_rows( $rows ) {
        $output = '';
    
        if ( is_array( $rows ) || is_object( $rows ) ) {
            $output .= '<table>';
    
            $first_row = true; 
            
            foreach ( $rows as $row ) {
                $output .= '<tr>';
                foreach ( $row as $cell ) {
                    if ( $first_row ) {
                        $output .= '<th>' . esc_html( $cell ) . '</th>'; 
                    } else {
                        $output .= '<td>' . esc_html( $cell ) . '</td>'; 
                    }
                }
                $output .= '</tr>';
                $first_row = false; 
            }
    
            $output .= '</table>';
        }
    
        return $output;
    }
}
