<?php
namespace WbsVendors\Dgm\Shengine\Woocommerce\Converters;

use Dgm\Arrays\Arrays;
use Dgm\Shengine\Interfaces\IRate;
use Dgm\Shengine\Model\Rate;
use WC_Shipping_Rate;


class RateConverter
{
    /**
     * @param WC_Shipping_Rate[] $_rates
     * @return IRate[]
     */
    public static function fromWoocommerceToCore(array $_rates)
    {
        return \WbsVendors\Dgm\Arrays\Arrays::map($_rates, function(WC_Shipping_Rate $_rate) {
            return new \WbsVendors\Dgm\Shengine\Model\Rate($_rate->cost, $_rate->label);
        });
    }

    /**
     * @param IRate[] $rates
     * @param string $defaultTitle
     * @param string|null $idPrefix
     * @return array  Woocommerce rates to add with WC_Shipping_Method::add_rate()
     */
    static public function fromCoreToWoocommerce(array $rates, $defaultTitle, $idPrefix = null)
    {
        $_rates = array();

        $wcRateIdsCounters = array();

        foreach ($rates as $rate) {

            $title = $rate->getTitle();
            if (!isset($title)) {
                $title = $defaultTitle;
            }

            $idParts = array();

            $hash = substr(md5($title), 0, 8);
            $idParts[] = $hash;

            $slug = strtolower($title);
            $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
            $slug = preg_replace('/_+/', '_', $slug);
            $slug = trim($slug, '_');
            if ($slug !== '') {
                $idParts[] = $slug;
            }

            $id = join('_', $idParts);

            isset($wcRateIdsCounters[$id]) ? $wcRateIdsCounters[$id]++ : ($wcRateIdsCounters[$id]=0);
            if (($count = $wcRateIdsCounters[$id]) > 0) {
                $id .= '_'.($count+1);
            }

            if (isset($idPrefix)) {
                $id = $idPrefix.$id;
            }

            $_rates[] = array(
                'id' => $id,
                'label' => $title,
                'cost' => $rate->getCost(),
                'taxes' => $rate->isTaxable() === false ? false : null
            );
        }

        return $_rates;
    }
}