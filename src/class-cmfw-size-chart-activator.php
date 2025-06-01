<?php
/**
 * Fired during plugin activation
 *
 * @link       https://github.com/faridmia/product-size-guide-for-woocommerce
 * @since      1.0.0
 *
 * @package    Product Size Guide For WooCommerce
 * @subpackage Product Size Guide For WooCommerce/src
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Product Size Guide For WooCommerce
 * @subpackage Product Size Guide For WooCommerce/src
 */
class Sizechma_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		if (!class_exists('WooCommerce')) {
			return false;
		}
	}
}