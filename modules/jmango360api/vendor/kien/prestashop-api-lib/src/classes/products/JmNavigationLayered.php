<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmNavigationLayered
{
    public $layered_show_qties;
    public $id_category_layered;
    public $selected_filters;
    public $nbr_filterBlocks;
    public $title_values;
    public $meta_values;
    public $current_friendly_url;
    public $param_product_url;
    public $no_follow;
    public $order_by;
    public $order_way;
    public $order_by_values;
    public $order_way_values;
    
    /** @var array JmFilter objects */
    public $filters;
}
