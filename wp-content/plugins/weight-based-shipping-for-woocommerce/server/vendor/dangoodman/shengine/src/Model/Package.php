<?php
namespace WbsVendors\Dgm\Shengine\Model;

use Dgm\Arrays\Arrays;
use Dgm\Shengine\Interfaces\IGrouping;
use Dgm\Shengine\Interfaces\IItem;
use Dgm\Shengine\Interfaces\IPackage;


class Package implements \WbsVendors\Dgm\Shengine\Interfaces\IPackage
{
    public function __construct(array $items = array(), \WbsVendors\Dgm\Shengine\Model\Destination $destination = null, \WbsVendors\Dgm\Shengine\Model\Customer $customer = null, array $coupons = array())
    {
        $this->items = $items;
        $this->destination = $destination;
        $this->customer = $customer;
        $this->coupons = $coupons;
    }

    public static function fromOther($other, \WbsVendors\Dgm\Shengine\Model\Destination $destination = null, \WbsVendors\Dgm\Shengine\Model\Customer $customer = null, array $coupons = array())
    {
        $package = new self(array(), $destination, $customer, $coupons);
        $package = $package->merge($other);
        return $package;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getPrice($flags = \WbsVendors\Dgm\Shengine\Model\Price::BASE)
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getPrice($flags);
        }

        return $sum;
    }

    public function getWeight()
    {
        $weight = 0;
        foreach ($this->getItems() as $item) {
            $weight += $item->getWeight();
        }

        return $weight;
    }

    public function getTerms($taxonomy)
    {
        $terms = \WbsVendors\Dgm\Arrays\Arrays::map($this->getItems(), function (\WbsVendors\Dgm\Shengine\Interfaces\IItem $item) use ($taxonomy) {
            
            $terms = $item->getTerms($taxonomy);
            
            if (!$terms) {
                $terms[] = \WbsVendors\Dgm\Shengine\Interfaces\IPackage::NONE_VIRTUAL_TERM_ID;
            }
            
            $terms = \WbsVendors\Dgm\Arrays\Arrays::map($terms, 'strval');
            
            return $terms;
        });

        $terms = $terms ? call_user_func_array('array_merge', $terms) : $terms;

        $terms = array_values(array_unique($terms, SORT_STRING));

        return $terms;
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getCoupons()
    {
        return $this->coupons;
    }

    public function split(\WbsVendors\Dgm\Shengine\Interfaces\IGrouping $by)
    {
        return $by->split($this);
    }

    public function merge($with)
    {
        if (!is_array($with)) {
            $with = array($with);
        }

        if (!$with) {
            return $this;
        }

        $otherItems = call_user_func_array(
            'array_merge',
            \WbsVendors\Dgm\Arrays\Arrays::map($with, function(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $pkg) {
                return $pkg->getItems();
            })
        );

        $theseItems = $this->getItems();

        $mergedItems = array();
        foreach (array_merge($theseItems, $otherItems) as $item) {
            $mergedItems[spl_object_hash($item)] = $item;
        }

        $package = $this;
        if (count($mergedItems) > count($theseItems)) {
            $package = new \WbsVendors\Dgm\Shengine\Model\Package(array_values($mergedItems), $this->getDestination(), $this->getCustomer(), $this->getCoupons());
        }

        return $package;
    }

    public function exclude($other)
    {
        if (!is_array($other)) {
            $other = array($other);
        }

        $theseItems = $this->getItems();

        $restItems = array(); {

            foreach ($theseItems as $item) {
                $restItems[spl_object_hash($item)] = $item;
            }

            /** @var IPackage $pkg */
            foreach ($other as $pkg) {
                foreach ($pkg->getItems() as $item) {
                    unset($restItems[spl_object_hash($item)]);
                }
            }
        }

        $package = $this;
        if (count($restItems) < count($theseItems)) {
            $package = new \WbsVendors\Dgm\Shengine\Model\Package($restItems, $this->getDestination(), $this->getCustomer(), $this->getCoupons());
        }

        return $package;
    }

    /** @var IItem[] */
    private $items;
    private $destination;
    private $customer;
    private $coupons;
}