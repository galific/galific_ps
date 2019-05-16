<?php
/**
 * Created by PhpStorm.
 * Date: 4/16/18
 * Time: 11:00 AM
 * @author kien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CartDataTransform extends BaseService
{
    private $priceFormatter;

    public function __construct($module_name)
    {
        parent::__construct($module_name);
        $this->priceFormatter = new PriceFormatter();
    }

    public function doExecute()
    {
    }

    /**
     * @param $cart
     * @param bool $shouldSeparateGifts
     * @return array
     * @throws \Exception
     */
    public function present($cart)
    {
        if (!is_a($cart, 'Cart')) {
            throw new \Exception('CartPresenter can only present instance of Cart');
        }
        $cart_summary = $cart->getSummaryDetails();
        $rawProducts = $cart_summary['products'];
        $rawProducts = array_merge($rawProducts, $cart_summary['gift_products']);
        foreach ($rawProducts as &$prod) {
            if ($prod['gift']) {
                $prod['is_gift'] = $prod['gift'];
                unset($prod['gift']);
            } else {
                $prod['is_gift'] = false;
            }
        }


        $subtotals = array();

        $productsTotalExcludingTax = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $total_excluding_tax = $cart->getOrderTotal(false);
        $total_including_tax = $cart->getOrderTotal(true);
        $total_discount = $cart->getOrderTotal($this->includeTaxes($cart->id_customer), Cart::ONLY_DISCOUNTS);
        $totalCartAmount = $cart->getOrderTotal($this->includeTaxes($cart->id_customer), Cart::ONLY_PRODUCTS);

        $subtotals['products'] = array(
            'type' => 'products',
            'label' => $this->getTranslation('Subtotal', 'shopping-cart-service'),
            'amount' => $totalCartAmount,
            'value' => $this->priceFormatter->format($totalCartAmount),
        );

        if ($total_discount) {
            $subtotals['discounts'] = array(
                'type' => 'discount',
                'label' => $this->getTranslation('Discount', 'shopping-cart-service'),
                'amount' => $total_discount,
                'value' => $this->priceFormatter->format($total_discount),
            );
        } else {
            $subtotals['discounts'] = null;
        }

        if ($cart->gift) {
            $giftWrappingPrice = ($cart->getGiftWrappingPrice($this->includeTaxes($cart->id_customer)) != 0)
                ? $cart->getGiftWrappingPrice($this->includeTaxes($cart->id_customer))
                : 0;

            $subtotals['gift_wrapping'] = array(
                'type' => 'gift_wrapping',
                'label' => $this->getTranslation('Gift wrapping', 'shopping-cart-service'),
                'amount' => $giftWrappingPrice,
                'value' => ($giftWrappingPrice > 0)
                    ? $this->priceFormatter->convertAndFormat($giftWrappingPrice)
                    : $this->getTranslation('Free', 'shopping-cart-service'),
            );
        }

        if (!$cart->isVirtualCart()) {
            $shippingCost = $cart->getTotalShippingCost(null, $this->includeTaxes($cart->id_customer));
        } else {
            $shippingCost = 0;
        }
        $subtotals['shipping'] = array(
            'type' => 'shipping',
            'label' => $this->getTranslation('Shipping', 'shopping-cart-service'),
            'amount' => $shippingCost,
            'value' => $shippingCost != 0
                ? $this->priceFormatter->format($shippingCost)
                : $this->getTranslation('Free', 'shopping-cart-service'),
        );

        $subtotals['tax'] = null;
        if (Configuration::get('PS_TAX_DISPLAY')) {
            $taxAmount = $total_including_tax - $total_excluding_tax;
            $subtotals['tax'] = array(
                'type' => 'tax',
                'label' => $this->getTranslation('Taxes', 'shopping-cart-service'),
                'amount' => $taxAmount,
                'value' => $this->priceFormatter->format($taxAmount),
            );
        }

        $totals = array(
            'total' => array(
                'type' => 'total',
                'label' => $this->getTranslation('Total', 'shopping-cart-service'),
                'amount' => $total_including_tax, //PS-955: Always return price incl tax
                'value' => $this->priceFormatter->format($total_including_tax),
            ),
            'total_including_tax' => array(
                'type' => 'total_incl',
                'label' => $this->getTranslation('Total (tax incl.)', 'shopping-cart-service'),
                'amount' => $total_including_tax,
                'value' => $this->priceFormatter->format($total_including_tax),
            ),
            'total_excluding_tax' => array(
                'type' => 'total_excl',
                'label' => $this->getTranslation('Total (tax excl.)', 'shopping-cart-service'),
                'amount' => $total_excluding_tax,
                'value' => $this->priceFormatter->format($total_excluding_tax),
            ),
        );

        $products_count = array_reduce($rawProducts, function ($count, $product) {
            return $count + $product['quantity'];
        }, 0);

        $summary_string = $products_count === 1 ?
            '1 item' :
            sprintf('%d items', $products_count);

        $minimalPurchase = $this->priceFormatter->convertAmount((float)Configuration::get('PS_PURCHASE_MINIMUM'));

        Hook::exec('overrideMinimalPurchasePrice', array(
            'minimalPurchase' => &$minimalPurchase
        ));

        // TODO: move it to a common parent, since it's copied in OrderPresenter and ProductPresenter
        $labels = array(
            'tax_short' => ($this->includeTaxes($cart->id_customer))
                ? $this->getTranslation('(tax incl.)', 'shopping-cart-service')
                : $this->getTranslation('(tax excl.)', 'shopping-cart-service'),
            'tax_long' => ($this->includeTaxes($cart->id_customer))
                ? $this->getTranslation('(tax included)', 'shopping-cart-service')
                : $this->getTranslation('(tax excluded)', 'shopping-cart-service'),
        );

        //format price of product
        if ($rawProducts && count($rawProducts) > 0) {
            foreach ($rawProducts as &$rawProduct) {
                $this->formatRawProduct($rawProduct, $cart->id_customer);
            }
        }

        return array(
            'products' => $rawProducts,
            'totals' => $totals,
            'subtotals' => $subtotals,
            'products_count' => $products_count,
            'summary_string' => $summary_string,
            'labels' => $labels,
            'id_address_delivery' => (int)$cart->id_address_delivery,
            'id_address_invoice' => (int)$cart->id_address_invoice,
            'is_virtual' => $cart->isVirtualCart(),
            'minimalPurchase' => $minimalPurchase,
            'minimalPurchaseRequired' =>
                ($this->priceFormatter->convertAmount($productsTotalExcludingTax) < $minimalPurchase) ?
                    sprintf(
                        $this->getTranslation('A minimum shopping cart total of %s (tax excl.) is required to validate your order. Current cart total is %s (tax excl.).', 'shopping-cart-service'),
                        $this->priceFormatter->convertAndFormat($minimalPurchase),
                        $this->priceFormatter->convertAndFormat($productsTotalExcludingTax)
                    ) : '',
        );
    }

    public function includeTaxes($id_customer)
    {
        if (!Configuration::get('PS_TAX')) {
            return false;
        }

        return !Product::getTaxCalculationMethod($id_customer);
    }


    protected function formatRawProduct(&$rawProduct, $id_customer)
    {
        if ($this->includeTaxes($id_customer)) {
            $rawProduct['price_amount'] = $rawProduct['price_wt'];
            $rawProduct['price'] = $rawProduct['price_wt'];
        } else {
            $rawProduct['price_amount'] = $rawProduct['price'];
            $rawProduct['price_tax_exc'] = $rawProduct['price'];
        }

        if ($rawProduct['price_amount'] && $rawProduct['unit_price_ratio'] > 0) {
            $rawProduct['unit_price'] = $rawProduct['price_amount'] / $rawProduct['unit_price_ratio'];
        }

        $rawProduct['total'] = $this->includeTaxes($id_customer) ? $rawProduct['total_wt'] : $rawProduct['total'];

        return $rawProduct;
    }
}
