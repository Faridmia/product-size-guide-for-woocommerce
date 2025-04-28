<?php
namespace Envatothemely\Sizechartpsgf\Admin\Metaboxes;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;

/**
 * Class Metaboxes
 * 
 * This class uses the Traitval trait to implement singleton functionality and
 * provides methods for creating custom post types within the Product Size Guide For WooCommerce plugin.
 */
class Metaboxes
{
    use Traitval;

    /**
     * Constructor
     * 
     * The constructor adds actions to the WordPress hooks for saving post data
     * and adding meta boxes to the post edit screen. These actions ensure that
     * custom metadata is handled properly within the Product Size Guide For WooCommerce plugin.
     */
    public function __construct()
    {

        // Add an action to the 'add_meta_boxes' hook to add custom meta boxes to the post edit screen
        add_action('add_meta_boxes', array($this, 'sizepsgf_product_size_charts_meta_boxes'));
        add_action('save_post', array($this, 'sizepsgf_product_size_charts_meta_save'));
       
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
    function sizepsgf_product_size_charts_meta_boxes()
    {
        add_meta_box(
            'sizepsgf_product_size_charts_meta_box',
            __('Product Size Chars Custom Fields', 'product-size-guide-for-woocommerce'),
            array($this, 'sizepsgf_product_size_charts_meta_box'),
            array('cmfw-size-chart'),
            'normal',
            'high'
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
    public function sizepsgf_product_size_charts_meta_box()
    {
        global $post;
        $size_chart_data = get_post_meta($post->ID, '_size_chart_data', true);

        $nonce = wp_create_nonce('cmfw-metaboxes-nonce');

        if (empty($size_chart_data)) {
            $default_demo_size = array(
                'rows' => array(
                    array('Size', 'Height', 'Width', 'Depth'),
                    array('XS', '15', '15', '20'),
                    array('S', '20', '25', '28'),
                    array('M', '25', '35', '14'),
                    array('L', '30', '39', '60'),
                    array('XL', '30', '55', '100'),
                )
            );

            update_post_meta($post->ID, '_size_chart_data', json_encode($default_demo_size));
            $size_chart_data = $default_demo_size; 

        } else {
            $size_chart_data = json_decode($size_chart_data, true);
        }

        $page_edit_id = $post->ID;

        $sizepsgf_settings = get_post_meta($post->ID, 'sizepsgf_settings', true);
        $sizepsgf_chart_position = get_post_meta($post->ID, 'sizepsgf_chart_position', true);
        $sizepsgf_popup_icon_input = get_post_meta($post->ID, 'sizepsgf_popup_icon_input', true);
        $sizepsgf_layout_style = get_post_meta($post->ID, 'sizepsgf_layout_style', true);

?>

    <div class="sizepsgf-top-settings-container">
        <h1 class="sizepsgf-heading"><?php esc_html_e('Size Chart Settings', 'product-size-guide-for-woocommerce'); ?></h1>

        <div class="sizepsgf-select-form-group-field">
            <label for="sizepsgf_settings" class="sizepsgf-settings">
                <?php esc_html_e('Size Chart Setting', 'product-size-guide-for-woocommerce'); ?>
            </label>
            <select id="sizepsgf_settings" name="sizepsgf_settings">
                <option value="global" <?php selected($sizepsgf_settings, 'global'); ?>><?php esc_html_e('Global Setting', 'product-size-guide-for-woocommerce'); ?></option>
                <option value="page_post_settings" <?php selected($sizepsgf_settings, 'page_post_settings'); ?>><?php esc_html_e('Single Setting', 'product-size-guide-for-woocommerce'); ?></option>
            </select>
            <p class="description">
                <?php esc_html_e('Select size chart link type. Default it will consider global settings.', 'product-size-guide-for-woocommerce'); ?>
            </p>
        </div>

        <div class="sizepsgf-select-form-group-field">
            <label for="sizepsgf_chart_position" class="sizepsgf-settings">
                <?php esc_html_e('Size Chart Position', 'product-size-guide-for-woocommerce'); ?>
            </label>
            <select id="sizepsgf_chart_position" name="sizepsgf_chart_position" class="sizepsgf_chart_position">
                <option value="modal" <?php selected($sizepsgf_chart_position, 'modal'); ?>><?php esc_html_e('Modal Pop Up', 'product-size-guide-for-woocommerce'); ?></option>
                <option value="tab" <?php selected($sizepsgf_chart_position, 'tab'); ?>><?php esc_html_e('Additional Tab', 'product-size-guide-for-woocommerce'); ?></option>
            </select>
            <p class="description">
                <?php esc_html_e('Select if the chart will display as a popup or as an additional tab.', 'product-size-guide-for-woocommerce'); ?>
            </p>
        </div>

        <div class="sizepsgf-select-form-group-field sizepsgf-popup-setting-wrap" style="<?php echo ($sizepsgf_chart_position === 'modal') ? 'display:block;' : 'display:none;'; ?>">
            <label><?php esc_html_e('Popup Icon', 'product-size-guide-for-woocommerce'); ?></label>
            <div class="sizepsgf-icon-selector icon-select-wapper">
                <input type="text" class="sizepsgf-popup-icon-input" name="sizepsgf_popup_icon_input" value="<?php echo esc_attr($sizepsgf_popup_icon_input); ?>">
            </div>
            <p class="description">
                <?php esc_html_e('Selected chart popup icon will show before chart popup link title.', 'product-size-guide-for-woocommerce'); ?>
            </p>
        </div>

        <div class="sizepsgf-select-form-group-field">
            <label for="sizepsgf_layout_style">
                <?php esc_html_e('Chart Table Style', 'product-size-guide-for-woocommerce'); ?>
            </label>
            <select id="sizepsgf_layout_style" name="sizepsgf_layout_style">
                <option value="sizepsgf_layout_1" <?php selected($sizepsgf_layout_style, 'sizepsgf_layout_1'); ?>><?php esc_html_e('Default Layout', 'product-size-guide-for-woocommerce'); ?></option>
                <option value="sizepsgf_layout_2" <?php selected($sizepsgf_layout_style, 'sizepsgf_layout_2'); ?>><?php esc_html_e('Layout Style 2', 'product-size-guide-for-woocommerce'); ?></option>
                <option value="sizepsgf_layout_3" <?php selected($sizepsgf_layout_style, 'sizepsgf_layout_3'); ?>><?php esc_html_e('Layout Style 3', 'product-size-guide-for-woocommerce'); ?></option>
            </select>
            <p class="description">
                <?php esc_html_e('Chart Table Styles (Default Style).', 'product-size-guide-for-woocommerce'); ?>
            </p>
        </div>
    </div>

    <div class="sizepsgf-container">
        <button class="sizepsgf-toggle-btn">−</button> 
        <div class="sizepsgf-content">
            
            <div class="sizepsgf-left">
                <div class="sizepsgf-field-group">
                    <label class="sizepsgf-label"><?php echo esc_html__( "Insert Row Count", "product-size-guide-for-woocommerce" ); ?></label>
                    <input type="number" class="sizepsgf-input" placeholder="Enter any number">
                    <button class="sizepsgf-btn sizepsgf-add-row"><?php echo esc_html__( "Add", "product-size-guide-for-woocommerce" ); ?></button>
                    <button class="sizepsgf-btn sizepsgf-delete-row"><?php echo esc_html__( "Delete", "product-size-guide-for-woocommerce" ); ?></button>
                </div>

                <div class="sizepsgf-field-group">
                    <label class="sizepsgf-label"><?php echo esc_html__( "Insert Column Count", "product-size-guide-for-woocommerce" ); ?></label>
                    <input type="number" class="sizepsgf-input-column" placeholder="Enter any number">
                    <button class="sizepsgf-btn sizepsgf-add-column"><?php echo esc_html__( "Add", "product-size-guide-for-woocommerce" ); ?></button>
                    <button class="sizepsgf-btn sizepsgf-delete-column"><?php echo esc_html__( "Delete", "product-size-guide-for-woocommerce" ); ?></button>
                </div>

            </div>

            <div class="sizepsgf-right">
                <div class="sizepsgf-table-controls">
                    <button id="sizepsgf_export_data" class="sizepsgf-btn" data-sizecham_export="<?php echo esc_attr( $page_edit_id ); ?>"><?php echo esc_html__( "Export Chart Table", "product-size-guide-for-woocommerce" ); ?></button>
                    <label for="hiddenFileInput" class="sizepsgf-btn sizepsgf-import-button" data-sizecham_import="<?php echo esc_attr( $page_edit_id ); ?>">
                        <?php echo esc_html__("Import Chart Table", "product-size-guide-for-woocommerce"); ?>
                    </label>
                    <input type="file" id="hiddenFileInput" class="sizepsgf_import_file" accept="application/json" style="position: absolute; left: -9999px;">
                </div>
            </div>
        </div>
    </div>
    <div id="sizepsgf_product_size_charts_data" class="sizepsgf_product_size_charts_options_panel">
        <input type="hidden" name="cmfw-metaboxes-nonce" value="<?php echo esc_attr($nonce); ?>" />
        <input type="hidden" id="size-chart-data" name="size_chart_data" value="<?php echo esc_attr(get_post_meta($post->ID, '_size_chart_data', true)); ?>" />
        <div id="upow-extra-options-wrapper">
            <table id="size-chart" class="sizepsgf-product-size-chats">
                <thead>
                    <tr>
                    <?php
                        // Ensure $default_demo_size is defined before use
                        $default_demo_size = $default_demo_size ?? [];

                        if ($size_chart_data && !empty($size_chart_data['rows'][0])) :
                            foreach ($size_chart_data['rows'][0] as $row_index => $row) :
                    ?>
                        <th>
                            <button class="btn btn-add cmfw-add-column">+</button>
                            <button class="btn btn-remove cmfw-remove-column">-</button>
                        </th>
                    <?php
                            endforeach;
                        elseif (!empty($default_demo_size) && is_array($default_demo_size[0])) :
                            foreach ($default_demo_size[0] as $key => $values) :
                    ?>
                        <th>
                            <button class="btn btn-add cmfw-add-column">+</button>
                            <button class="btn btn-remove cmfw-remove-column">-</button>
                        </th>
                    <?php 
                            endforeach;
                        endif; 
                    ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ( $size_chart_data && !empty( $size_chart_data['rows']) ) :
                        foreach ($size_chart_data['rows'] as $row_index => $row) :
                    ?>
                        <tr>
                            <?php foreach ($row as $cell) : ?>
                                <td><input type="text" value="<?php echo esc_attr($cell); ?>" placeholder="Enter value" /></td>
                            <?php endforeach; ?>
                            <td class="action-btns">
                                <button class="btn btn-add cmfw-add-row">+</button>
                                <button class="btn btn-remove cmfw-remove-row">-</button>
                            </td>
                        </tr>
                    <?php
                        endforeach;
                    else :
                        foreach( $default_demo_size  as $key => $values ) : 
                            
                    ?>
                        <tr>
                            <?php foreach ($values as $cell) : ?>
                                <td><input type="text" value="<?php echo esc_attr($cell); ?>" placeholder="Enter value" /></td>
                            <?php endforeach; ?>
                            <td class="action-btns">
                                <button class="btn btn-add cmfw-add-row">+</button>
                                <button class="btn btn-remove cmfw-remove-row">-</button>
                            </td>
                        </tr>
                    <?php 
                    endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
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

    public function sizepsgf_product_size_charts_meta_save($post_id)
    {
        if (!isset($_POST['cmfw-metaboxes-nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cmfw-metaboxes-nonce'])), 'cmfw-metaboxes-nonce')) {
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

        // Handle multiple instances of upow_product
        if (isset($_POST['size_chart_data'])) {
            $size_chart_data = isset($_POST['size_chart_data'])
                ? (is_array($_POST['size_chart_data'])
                    ? array_map('sanitize_textarea_field', wp_unslash($_POST['size_chart_data']))
                    : sanitize_textarea_field(wp_unslash($_POST['size_chart_data'])))
                : '0';
            update_post_meta($post_id, '_size_chart_data', $size_chart_data);
            
        } else {
            delete_post_meta($post_id, '_size_chart_data');
        }

        if (isset($_POST['sizepsgf_settings'])) {
            update_post_meta($post_id, 'sizepsgf_settings', sanitize_text_field(wp_unslash($_POST['sizepsgf_settings'])));
        }

        if (isset($_POST['sizepsgf_chart_position'])) {
            update_post_meta($post_id, 'sizepsgf_chart_position', sanitize_text_field(wp_unslash($_POST['sizepsgf_chart_position'])));
        }

        if (isset($_POST['sizepsgf_popup_icon_input'])) {
            update_post_meta($post_id, 'sizepsgf_popup_icon_input', sanitize_text_field(wp_unslash($_POST['sizepsgf_popup_icon_input'])));
        }

        if (isset($_POST['sizepsgf_layout_style'])) {
            update_post_meta( $post_id, 'sizepsgf_layout_style', sanitize_text_field(wp_unslash($_POST['sizepsgf_layout_style'])));
        }
    }
}