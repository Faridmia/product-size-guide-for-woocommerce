<?php
namespace Envatothemely\Sizechartpsgf\Common\PostType;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;

/**
 * Class PostType
 * 
 * Handles the creation of a custom post type for the Product Size Guide For WooCommerce plugin.
 */
class PostType
{
    use Traitval;

    /**
     * @var string $post_type The name of the custom post type
     */
    private $post_type = 'cmfw-size-chart';

    /**
     * @var array $labels The labels for the post type
     */
    private $labels = [];

    /**
     * @var array $args The arguments for the post type
     */
    private $args = [];

    /**
     * Initializes the class and creates the custom post type.
     */
    protected function initialize()
    {
        $this->set_labels();
        $this->set_args();

        add_action('init', [$this, 'register_custom_post_type']);

        // disable gutenberg for this post type
        add_filter( 'gutenberg_can_edit_post_type', [ $this, 'sizepsgf_gutenberg_can_edit_post_type' ], 10, 2 );
        add_filter( 'use_block_editor_for_post_type', [ $this, 'sizepsgf_gutenberg_can_edit_post_type' ], 10, 2 );
    }

    /**
     * Sets the labels for the custom post type.
     */
    public function set_labels()
    {
        $this->labels = [
            'name'                  => esc_html_x('Size Chart', 'Post Type General Name', 'product-size-guide-for-woocommerce'),
            'singular_name'         => esc_html_x('Size Chart', 'Post Type Singular Name', 'product-size-guide-for-woocommerce'),
            'menu_name'             => esc_html__('Size Chart', 'product-size-guide-for-woocommerce'),
            'name_admin_bar'        => esc_html__('Size Chart', 'product-size-guide-for-woocommerce'),
            'archives'              => esc_html__('Size Chart Archives', 'product-size-guide-for-woocommerce'),
            'attributes'            => esc_html__('Size Chart Attributes', 'product-size-guide-for-woocommerce'),
            'parent_item_colon'     => esc_html__('Parent Item:', 'product-size-guide-for-woocommerce'),
            'all_items'             => esc_html__('Size Charts', 'product-size-guide-for-woocommerce'),
            'add_new_item'          => esc_html__('Add New Size Chart', 'product-size-guide-for-woocommerce'),
            'add_new'               => esc_html__('Add New', 'product-size-guide-for-woocommerce'),
            'new_item'              => esc_html__('New Size Chart', 'product-size-guide-for-woocommerce'),
            'edit_item'             => esc_html__('Edit Size Chart', 'product-size-guide-for-woocommerce'),
            'update_item'           => esc_html__('Update Size Chart', 'product-size-guide-for-woocommerce'),
            'view_item'             => esc_html__('View Size Chart', 'product-size-guide-for-woocommerce'),
            'view_items'            => esc_html__('View Size Charts', 'product-size-guide-for-woocommerce'),
            'search_items'          => esc_html__('Search Size Charts', 'product-size-guide-for-woocommerce'),
            'not_found'             => esc_html__('Not found', 'product-size-guide-for-woocommerce'),
            'not_found_in_trash'    => esc_html__('Not found in Trash', 'product-size-guide-for-woocommerce'),
            'featured_image'        => esc_html__('Size chart Image', 'product-size-guide-for-woocommerce'),
            'set_featured_image'    => esc_html__('Set size chart image', 'product-size-guide-for-woocommerce'),
            'remove_featured_image' => esc_html__('Remove image', 'product-size-guide-for-woocommerce'),
            'use_featured_image'    => esc_html__('Use as size chart image', 'product-size-guide-for-woocommerce'),
            'insert_into_item'      => esc_html__('Insert into Size Chart', 'product-size-guide-for-woocommerce'),
            'uploaded_to_this_item' => esc_html__('Uploaded to this Size Chart', 'product-size-guide-for-woocommerce'),
            'items_list'            => esc_html__('Size Charts', 'product-size-guide-for-woocommerce'),
            'items_list_navigation' => esc_html__('Size Charts navigation', 'product-size-guide-for-woocommerce'),
            'filter_items_list'     => esc_html__('Filter from charts', 'product-size-guide-for-woocommerce'),
        ];
    }

    /**
     * Sets the arguments for the custom post type.
     */
    public function set_args()
    {
        $this->args = [
            'label'               => esc_html__('Size Chart', 'product-size-guide-for-woocommerce'),
            'description'         => esc_html__('Size Chart', 'product-size-guide-for-woocommerce'),
            'labels'              => $this->labels,
            'supports'            => array('title', 'editor', 'thumbnail', 'revisions'),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'rewrite'             => array(
                'slug'       => 'cmfw-size-chart',
                'pages'      => false,
                'with_front' => true,
                'feeds'      => false,
            ),
            'query_var'           => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'page',
            'show_in_rest'        => true,
            'rest_base'           => $this->post_type,
            'menu_icon'           => 'dashicons-chart-bar',
        ];
    }

    public function sizepsgf_gutenberg_can_edit_post_type( $can_edit, $post_type ) {

        $edit_post = $this->post_type == 'cmfw-size-chart' ? false : $can_edit;
        return $edit_post;
        
    }

    /**
     * Registers the custom post type.
     */
    public function register_custom_post_type()
    {
        register_post_type($this->post_type, $this->args);
    }

    /**
     * Flushes rewrite rules upon theme activation.
     */
    public function flush_rewrite_rules()
    {
        $this->register_custom_post_type();
        flush_rewrite_rules();
    }
}