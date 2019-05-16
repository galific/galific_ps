<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductReviewResponse extends JmResponse
{
    public $overview = null;
    public $reviews = array();
    public $reviews_max_pages = 0;
    public $review_per_page = 0;
    public $reviews_count = 0;
}
