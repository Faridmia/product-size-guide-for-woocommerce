<?php
namespace Envatothemely\Sizechartpsgf\Admin\Ajax;

class SizeChartAjax
{
    /**
     * size chart save option function
     *
     * @return void
     */
    public function sizepsgf_size_chart_save_options() {
        
        check_ajax_referer('sizepsgf_save_size_chart', 'nonce');

        // check manage options capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized user');
        }
        
        $datas = isset( $_POST ) 
            ? (is_array( $_POST ) 
                ? array_map('sanitize_text_field', wp_unslash( $_POST )) 
                : sanitize_text_field(wp_unslash( $_POST ) )
            ) 
            : [];
        unset($datas['action']);
        unset($datas['nonce']);

        // Set default values for specific options
        $default_options = [
            'sizepsgf_size_chart_position' => '',
            'sizepsgf_layout_style' => '',
            'sizepsgf_size_chart_enable_disable' => '',
            'sizepsgf_size_chart_tab_title' => '',
            'sizepsgf_sizechart_popup_position' => '',
            'sizepsgf_size_heading_title' => '',
            'sizepsgf_size_guide_content' => '',
        ];

        foreach ( $default_options as $key => $default_value ) {
            if (!isset($datas[$key])) {
                $datas[$key] = $default_value;
            }
        }

        foreach ( $datas as $key => $value ) {
            update_option($key, $value);
        }

        wp_send_json_success();
    }


    /**
     * size chart color function
     *
     * @return void
     */
    public function sizepsgf_size_chart_color_save_options() {
        
        check_ajax_referer('sizepsgf_save_size_chart', 'nonce');

        // check manage options capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized user');
        }
        
        $datas = isset( $_POST ) 
            ? (is_array( $_POST ) 
                ? array_map('sanitize_text_field', wp_unslash( $_POST )) 
                : sanitize_text_field(wp_unslash( $_POST ) )
            ) 
            : '0';
        unset($datas['action']);
        unset($datas['nonce']);

        // Set default values for specific options
        $default_options = [
            'sizepsgf_even_row_color' => '',
            'sizepsgf_odd_row_color' => '',
            'sizepsgf_font_text_color' => '', 
            'sizepsgf_table_border_color' => '', 
            'sizepsgf_border_size' => '1', 
            'sizepsgf_size_guide_custom_css' => '', 
        ];

        foreach ( $default_options as $key => $default_value ) {
            if (!isset($datas[$key])) {
                $datas[$key] = $default_value;
            }
        }

        foreach ( $datas as $key => $value ) {
            update_option($key, $value);
        }

        wp_send_json_success();
    }

     /**
     * advanced color function
     *
     * @return void
     */
    public function sizepsgf_advancedsize_color_save_options() {
        
        check_ajax_referer('sizepsgf_save_size_chart', 'nonce');

        // check manage options capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized user');
        }
        
        $datas = isset( $_POST ) 
            ? (is_array( $_POST ) 
                ? array_map('sanitize_text_field', wp_unslash( $_POST )) 
                : sanitize_text_field(wp_unslash( $_POST ) )
            ) 
            : '0';
        unset($datas['action']);
        unset($datas['nonce']);

        // Set default values for specific options
        $default_options = [
            'sizepsgf_popup_btnbg_color'   => '',
            'sizepsgf_popup_btntext_color' => '',
            'sizepsgf_btn_font_size' => '',
            'sizepsgf_popup_btnicon_color' => '',
            'sizepsgf_containerbg_color' => '',
            'sizepsgf_btn_padding' => '',
        ];

        foreach ( $default_options as $key => $default_value ) {
            if (!isset($datas[$key])) {
                $datas[$key] = $default_value;
            }
        }

        foreach ( $datas as $key => $value ) {
            update_option($key, $value);
        }

        wp_send_json_success();
    }


    /**
     * sizepsgf_search_products function
     *
     * @return void
     */
    function sizepsgf_search_products() {

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'sizepsgf_save_size_chart' ) ) {
            return;
        }
        if (!isset($_POST['keyword'])) {
            wp_send_json_error(['message' => __('Keyword is missing.', 'product-size-guide-for-woocommerce')]);
        }

        $keyword = sanitize_text_field(wp_unslash($_POST['keyword']));

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            's'              => $keyword, 
            'posts_per_page' => 10, 
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $products = [];

            while ($query->have_posts()) {
                $query->the_post();

                $products[] = [
                    'id'   => get_the_ID(),
                    'name' => get_the_title(),
                ];
            }

            wp_reset_postdata();

            wp_send_json_success($products); 
        } else {
            wp_send_json_success([]); 
        }

    }

    // Export Data as JSON
    function sizepsgf_export_data() {

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'sizepsgf_save_size_chart' ) ) {
            return;
        }

        if (!isset($_POST['post_id'])) {
            wp_send_json_error("Invalid request");
        }
    
        $post_id = intval($_POST['post_id']);
        $size_chart_data = get_post_meta($post_id, '_size_chart_data', true);

        if (empty($size_chart_data)) {
            wp_send_json_error("No size chart data found");
        }
    
        $size_chart_data = json_decode($size_chart_data, true);
    
        if (!isset($size_chart_data['rows'])) {
            wp_send_json_error("Invalid data format!");
        }

        wp_send_json_success(['rows' => $size_chart_data['rows'] ]);
    }

    // Import JSON Data
    function sizepsgf_import_data() {

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) ,'sizepsgf_save_size_chart' ) ) {
            return;
        }

        if (!isset($_POST['post_id'], $_POST['data'])) {
            wp_send_json_error("Invalid request");
        }

        $post_id = intval($_POST['post_id']);
        $data = sanitize_text_field( wp_unslash ( $_POST['data'] ) ); 
        $import_data = json_decode( $data, true );

        if (!is_array($import_data)) {
            wp_send_json_error("Invalid JSON format");
        }

        update_post_meta($post_id, '_size_chart_data', json_encode($import_data));

        wp_send_json_success("Size chart data imported successfully");
    }

}
