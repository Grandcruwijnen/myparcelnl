<?php
/**
 * LICENSE: This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 *
 * If you want to add improvements, please create a fork in our GitHub:
 * https://github.com/myparcelnl
 *
 * @author      Reindert Vetter <reindert@myparcel.nl>
 * @copyright   2010-2016 MyParcel
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US  CC BY-NC-ND 3.0 NL
 * @link        https://github.com/myparcelnl/magento
 * @since       File available since Release v0.1.0
 */

namespace MyParcelNL\Magento\Helper;

class Checkout extends Data
{
    private $base_price = 0;

    private $tmp_scope;

    /**
     * @return int
     */
    public function getBasePrice(): int
    {
        return $this->base_price;
    }

    /**
     * @param int $base_price
     */
    public function setBasePrice(int $base_price)
    {
        $this->base_price = $base_price;
    }

    /**
     * Get extra price. Check if total shipping price is not below 0 euro
     *
     * @param $extraPrice
     *
     * @return float
     */
    public function getExtraPrice($extraPrice)
    {
        if ($this->getBasePrice() + $extraPrice < 0) {
            return 0;
        }
        return (float)$this->getBasePrice() + $extraPrice;
    }

    /**
     * Get shipping price
     *
     * @param $price
     * @param $flag
     *
     * @return mixed
     */
    /*private function getShippingPrice($price, $flag = false)
    {
        $flag = $flag ? true : Mage::helper('tax')->displayShippingPriceIncludingTax();
        return (float)Mage::helper('tax')->getShippingPrice($price, $flag, $quote->getShippingAddress());
    }*/

    /**
     * @return mixed
     */
    public function getTmpScope()
    {
        return $this->tmp_scope;
    }

    /**
     * @param mixed $tmp_scope
     */
    public function setTmpScope($tmp_scope)
    {
        $this->tmp_scope = $this->getConfigValue(self::XML_PATH_CHECKOUT . $tmp_scope);
        if (!is_array($this->tmp_scope)) {
            var_dump(self::XML_PATH_CHECKOUT . $tmp_scope);
            exit('help');
        }
        /*if ($tmp_scope == 'delivery') {
            var_dump(self::XML_PATH_CHECKOUT . $tmp_scope);
            exit('good');
        }*/
    }

    /**
     * Get checkout setting
     *
     * @param string $code
     * @param null   $storeId
     *
     * @return mixed
     */
    public function getCheckoutConfig($code, $storeId = null)
    {
        $settings = $this->getTmpScope();

        /** @todo throw exception */
        if (!is_array($settings)) {
            var_dump($settings);
            var_dump($code);
            exit('tesfd');
        }

        if (!key_exists($code, $settings)) {
            var_dump($settings);
            var_dump($code);
            exit('tesfd');
        }
        return $settings[$code];
    }

    /**
     * Get bool of setting
     *
     * @param string $key
     *
     * @return bool
     */
    public function getBoolConfig($key)
    {
        return $this->getCheckoutConfig($key) == "1" ? true : false;
    }

    /**
     * Get time for delivery endpoint
     *
     * @param string $key
     *
     * @return string
     */
    public function getTimeConfig($key)
    {
        return str_replace(',', ':', $this->getCheckoutConfig($key));
    }

    /**
     * Get array for delivery endpoint
     *
     * @param string $key
     *
     * @return string
     */
    public function getArrayConfig($key)
    {
        return str_replace(',', ';', $this->getCheckoutConfig($key));
    }

    /**
     * Get array for delivery endpoint
     *
     * @param string $key
     *
     * @return float
     */
    public function getIntergerConfig($key)
    {
        return (float)$this->getCheckoutConfig($key);
    }
}