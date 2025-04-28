<?php
namespace Envatothemely\Sizechartpsgf\Traitval;

/**
 * Traitval
 * 
 * This trait provides a singleton implementation for initializing and managing
 * certain functionalities within the Product Size Guide For WooCommerce plugin.
 */
trait Traitval {
	/**
	 * @var bool|self $singleton The singleton instance of this trait.
	 */
	private static $singleton = false;

	/**
	 * @var string $plugin_pref The prefix used for plugin-related options and settings.
	 */
	public $plugin_pref = 'product-size-guide-for-woocommerce';

	/**
	 * Constructor
	 * 
	 * The private constructor prevents direct instantiation. It initializes the trait
	 * by calling the initialize method.
	 */
	private function __construct() {
		$this->initialize();
        
	}

	/**
	 * Initialize the trait
	 * 
	 * This protected method can be overridden by classes using this trait to include
	 * additional initialization code.
	 */
	protected function initialize() {
		// Initialization code can be added here by the class using this trait.
        
	}

	/**
	 * Get the Singleton Instance
	 * 
	 * This static method ensures that only one instance of the trait is created.
	 * It returns the singleton instance, creating it if it does not exist.
	 * 
	 * @return self The singleton instance of the trait.
	 */
	public static function getInstance() {
		if (self::$singleton === false) {
			self::$singleton = new self();
		}
		return self::$singleton;
	}

	// Utility method to check option and return checked value
    public function get_option_checked($option_name) {
        $option_value = get_option($option_name, true);
        return ($option_value == 'yes' || $option_value == '1' || !empty($checked_value)) ? "checked='checked'" : '';
    }

	/**
	 * Renders a multi-select field for WooCommerce products.
	 *
	 * @param string $label           Label for the field (unused).
	 * @param string $name            Name attribute of the select field.
	 * @param array  $selected_values Pre-selected product IDs.
	 */
	public function sizepsgf_render_select_product_field($label, $name, $selected_values) {

        $select_product_class = ($name == 'sizepsgf_select_product' ) ? 'sizepsgf-select-product-fields' : '';
        ?>
        <div class="sizepsgf-general-item <?php echo esc_attr( $select_product_class ); ?>">
            <div class="sizepsgf-gen-item-con sizepsgf-extra-product-fields-select">
                <select multiple name="<?php echo esc_attr($name); ?>[]" class="cmfw-select-product">
					<?php 
						echo wp_kses_post( sizepsgf_all_product_panel_output($selected_values) ); 
                    ?>
                </select>
            </div>
        </div>
        <?php
    }

	// render select categories field
	public function sizepsgf_render_select_product_cat_field($label, $name, $selected_values) {
		$select_product_class = ($name == 'sizepsgf_select_product_categories' ) ? 'cmfw-select-product-categories' : '';
        ?>
		<div class="sizepsgf-general-item <?php echo esc_attr( $select_product_class ); ?>">
			<div class="sizepsgf-gen-item-con sizepsgf-extra-product-fields-select">
				<select multiple name="<?php echo esc_attr($name); ?>[]" class="cmfw-select-product">
					<?php
						sizepsgf_get_product_categories($selected_values);
					?>
				</select>
			</div>
		</div>
		<?php
    }

	// Method to render text input items
    public function sizepsgf_render_color_input($label, $name, $value) {
        ?>
        <div class="sizepsgf-general-item">
            <div class="sizepsgf-gen-item-con">
                <label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label) ?></label>
                <input type="text" class="cmfw-section-bg" name="<?php echo esc_attr($name); ?>"
                    value="<?php echo esc_attr($value); ?>">
            </div>
        </div>
        <?php
    }

	// render checkbox input field
	public function sizepsgf_render_checkbox_item( $label, $name, $checked_value = 0 ) {
        ?>
		<label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label) ?></label>
		<label class="sizepsgf-toggle">
			<input type="checkbox" name="<?php echo esc_attr($name); ?>" <?php echo esc_attr($checked_value); ?> value="1">
			<span class="sizepsgf-slider"></span>
		</label>
        <?php
    }

	// render text input field
	public function sizepsgf_render_text_input($label, $name, $value, $placeholder = '', $class = '' ) {
        ?>
		<div id="cmfw-input-field-settings" class="<?php echo esc_attr( $class ); ?>">
			<label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label) ?></label>
			<input type="text" id="cmfw-tab-title" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" name="<?php echo esc_attr($name); ?>">
		</div>
        <?php
    }

	// render select field
	public function sizepsgf_render_select_itemt($name, $label, $options, $class = '',$hide = '' ) {

        ?>
		<div class="<?php echo esc_attr( $class );?>" id="cmfw-select-item-field">
			<label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label) ?></label>
			<select id="difficulty-level" name="<?php echo esc_attr($name); ?>" class="<?php echo esc_attr( $class ); ?>">
				<?php 
					if( $hide != 'hide') {
				?>
				<option value=""><?php echo esc_html__('select', 'product-size-guide-for-woocommerce') ?></option>
				<?php } ?>
					<?php foreach ($options as $value => $option_label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($this->options[$name], $value); ?>>
							<?php echo esc_html($option_label); ?>
						</option>
					<?php endforeach; ?>
			</select>
		</div>
        <?php
    }

	public function sizepsgf_render_border_size_input_func( $label, $name, $value, $placeholder = '' ) {
        ?>
        <div class="sizepsgf-general-item sizepsgf-border-size-input-field">
            <div class="sizepsgf-gen-item-con">
                <label  for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label) ?></label>
                <div class="sizepsgf-border-size-wrapper">
                    <input placeholder="<?php echo esc_attr($placeholder); ?>" type="text" class="sizepsgf-border-size" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
                    <span class="sizepsgf-border-size-unit"><?php echo esc_html__("Px","product-size-guide-for-woocommerce");?></span>
                </div>
            </div>
        </div>
     <?php
    }

	// render text input field
	public function sizepsgf_render_textarea_input($label, $name, $value, $placeholder = '', $class = '' ) {
        ?>
		<div id="cmfw-input-field-settings" class="<?php echo esc_attr( $class ); ?>">
			<label for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label) ?></label>
			<textarea rows="4" cols="58" id="cmfw-tab-title" placeholder="<?php echo esc_attr($placeholder); ?>" name="<?php echo esc_attr($name); ?>"><?php echo esc_attr($value); ?></textarea>
		</div>
        <?php
    }

}
