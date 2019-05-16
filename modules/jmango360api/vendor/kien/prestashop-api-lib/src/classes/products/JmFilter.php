<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmFilter
{
    public $type_lite;
    public $type;
    public $id_key;
    public $name;
    /** This @var nameKey is combined with valueKey to request selected filters */
    public $nameKey;
    public $is_color_group;
    /** @var array JmFilterValue objects */
    public $values;
}
