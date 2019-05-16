<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductDetailReloadService extends BaseService
{
    protected $errors = array();

    private $selected_options;

    private $id_product_attribute;

    private $id_lang;

    private $enable_color_swatch;

    public function doExecute()
    {
        $this->id_lang = Tools::getValue('id_lang');
        $this->selected_options = Tools::getValue('selected_options');
        $this->enable_color_swatch = (int)Tools::getValue('enable_color_swatch', 0);
        if (!$this->selected_options) {
            $this->errors[] = Tools::displayError('Invalid input selected_options parameter');
        }

        $jm_product_detail = new JmProductDetail();
        $productService = new ProductDetailService($this->module_name);
        $productService->doExecute();
        $jm_product_detail = $productService->response;

        // Calculate product price
        $this->calculateProductPrice($jm_product_detail);

        $this->assignAttributesGroups($jm_product_detail);

        $gallery = $this->assignCombinationImage($jm_product_detail->id_product);
        if ($this->enable_color_swatch && $gallery != null && !empty($gallery)) {
            $jm_product_detail->gallery = $gallery;
        }

        $this->response = $jm_product_detail;
    }

    public function calculateProductPrice(JmProductDetail &$jm_product_detail)
    {
        // Convert selected options into array
        $optionArray = array();
        $attributes = explode('/', ltrim($this->selected_options, '/'));
        if (!empty($attributes)) {
            // For each options
            foreach ($attributes as $attr) {
                $parameters = explode('-', $attr);
                $attribute_name = $parameters[0];
                $attribute_value = $parameters[1];
                $optionArray[] = $attribute_value;
            }
        }

        // Ignore the function if product is not configurable
        if (empty($optionArray) || empty($jm_product_detail) || empty($jm_product_detail->combinations)) {
            return;
        }

        $specificPrice = new JmProductSpecificPrice();
        $combinePrice = 0;
        // Loop combinations
        foreach ($jm_product_detail->combinations as $combine) {
            $attributes = $combine['attributes'];
            $countDiff = count(array_diff($attributes, $optionArray));
            // options match to the combination
            if ($countDiff == 0) {
                $this->id_product_attribute = $combine['id_combination'];
                // Price can change for each combinations
                $combinePrice = $combine['price'];
                $specific_price = $combine['specific_price'];
                if (!empty($specific_price)) {
                    // Set specific price
                    $specificPrice->price = $specific_price['price'];
                    $specificPrice->from_quantity = $specific_price['from_quantity'];
                    $specificPrice->reduction = $specific_price['reduction'];
                    $specificPrice->reduction_tax_incl = $specific_price['reduction_tax'];
                    $specificPrice->reduction_type = $specific_price['reduction_type'];
                    $specificPrice->from = $specific_price['from'];
                    $specificPrice->to = $specific_price['to'];
                }
            }
        }
        $jm_product_detail->calculated_base_price = $jm_product_detail->base_price;
        $jm_product_detail->calculated_final_price = $jm_product_detail->price;

        // Calculate price
//        $this->calculatePrice($jm_product_detail, $specificPrice, $combinePrice);
    }

    public function calculatePrice(
        JmProductDetail &$jm_product_detail,
        JmProductSpecificPrice $specificPrice,
        $combinePrice
    ) {
        $basePrice = $jm_product_detail->base_price;
        $basePriceDisplay = $basePrice + $combinePrice;
        $finalPrice = $basePriceDisplay;

        // Display price with tax
        $taxRate = $jm_product_detail->customer_group_without_tax ? 1 : (1 + $jm_product_detail->tax_rate/100);

        // Customer group discount, apply for original base price without combination price
        // Customer group discount is also applied for specific price
        $groupReduction = $jm_product_detail->group_reduction;
        $groupApplied = (is_numeric($groupReduction) && $groupReduction > 0) ? (1 - $groupReduction) : 1;

        $discount = 0;
        // Specific price
        if (!empty($specificPrice) && isset($specificPrice->reduction_type) && $specificPrice->from_quantity == 1) {
            // In case base price is changed
            if ($specificPrice->price > 0) {
                $basePrice = $specificPrice->price;
                $basePriceDisplay = $basePrice;
                $finalPrice = $basePriceDisplay;
            }
            // Discount by percentage
            if (strcmp(JmProductSpecificPrice::REDUCTION_TYPE_PERCENTAGE, $specificPrice->reduction_type) == 0) {
                $discount = $finalPrice * $taxRate * $specificPrice->reduction;
            } elseif (strcmp(JmProductSpecificPrice::REDUCTION_TYPE_AMOUNT, $specificPrice->reduction_type) == 0) {
                $discount = $specificPrice->reduction;
                $discount = ($specificPrice->reduction_tax_incl) ? $discount : $discount * $taxRate;
            }
        }

        $basePriceDisplay = ($basePriceDisplay - $basePrice * $groupReduction) * $taxRate;
        $finalPrice = ($finalPrice * $taxRate - $discount) * $groupApplied;
        $jm_product_detail->calculated_base_price = $basePriceDisplay;
        $jm_product_detail->calculated_final_price = $finalPrice;
    }

    /**
     * Assign template vars related to attribute groups and colors
     */
    protected function assignAttributesGroups(JmProductDetail &$jm_product_detail)
    {
        // Convert selected options into array
        $optionArray = array();
        $attributes = explode('/', ltrim($this->selected_options, '/'));
        if (!empty($attributes)) {
            // For each options
            foreach ($attributes as $attr) {
                $parameters = explode('-', $attr);
                $attribute_name = $parameters[0];
                $attribute_value = $parameters[1];
                $optionArray[] = $attribute_value;
            }
        }

        // Ignore the function if product is not configurable
        if (empty($optionArray)
            || empty($jm_product_detail)
            || empty($jm_product_detail->combinations)) {
            return;
        }

        // Loop combinations
        foreach ($jm_product_detail->combinations as &$combination) {
            $combination['reference'] =
                Tools::isEmpty($combination['reference']) ? $jm_product_detail->reference : $combination['reference'];
        }
    }

    public function assignCombinationImage($id_product)
    {
        $product = new ProductCore($id_product, false, $this->id_lang);
        $gallery = array();
        $combination_images = ImageCore::getImages($this->id_lang, $id_product, $this->id_product_attribute);
        $link = new LinkCore();
        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $image_type = ProductDataTransform::getLargestProductImageType();
        foreach ($combination_images as $image) {
            $imgUrl = sprintf('%s%s', $protocol_link, $link->getImageLink(
                $product->link_rewrite,
                $image['id_image'],
                $image_type
            ));
            $gallery[] = array(
                array(
                    'key' => $image_type,
                    'value' => $imgUrl
                )
            );
        }

        return $gallery;
    }
}
