<?php
namespace Envatothemely\Sizechartpsgf\Admin;
use Envatothemely\Sizechartpsgf\Admin\Metaboxes\Metaboxes;
use Envatothemely\Sizechartpsgf\Admin\Metaboxes\SizeChartMetaBoxes;
use Envatothemely\Sizechartpsgf\Admin\Metaboxes\SizeChartColumns;
use Envatothemely\Sizechartpsgf\Traitval\Traitval;
use Envatothemely\Sizechartpsgf\Admin\AdminPanel\AdminPanel;

/**
 * Class Admin
 * 
 * This class uses the Traitval trait to implement singleton functionality and
 * provides methods for initializing the admin menu and other admin-related features
 * within the Product Size Guide For WooCommerce plugin.
 */
class Admin
{
    use Traitval;

    /**
     * @var Menu $menu_instance An instance of the Menu class.
     */
    protected $metabox_instance;
    protected $admin_panel_instance;
    protected $size_chart_instance;
    protected $size_chart_column;

    /**
     * Initialize the class
     * 
     * This method overrides the initialize method from the Traitval trait.
     * It sets up the necessary classes and features for the admin area.
     */
    protected function initialize()
    {

        $this->define_classes();
    }

    /**
     * Define Classes
     * 
     * This method initializes the classes used in the admin area, specifically the
     * Menu class, and assigns an instance of it to the $menu_instance property.
     */
    private function define_classes()
    {
        $this->metabox_instance     = new Metaboxes();
        $this->admin_panel_instance = new AdminPanel();
        $this->size_chart_instance = new SizeChartMetaBoxes();
        $this->size_chart_column = new SizeChartColumns(); 
    }
}
