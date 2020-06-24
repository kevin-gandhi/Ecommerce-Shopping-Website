<?php
namespace WbsVendors\Dgm\Shengine\Processing;

use Dgm\Shengine\Interfaces\IRate;
use Dgm\Shengine\Model\Rate;


class RateRegister implements \WbsVendors\Dgm\Shengine\Interfaces\IRate
{
    public $cost;
    public $title;
    public $taxable;

    public function __construct($cost = 0, $title = null, $taxable = null)
    {
        $this->cost = $cost;
        $this->title = $title;
        $this->taxable = $taxable;
    }

    public function toRate()
    {
        return new \WbsVendors\Dgm\Shengine\Model\Rate($this->getCost(), $this->getTitle(), $this->isTaxable());
    }

    static public function fromRate(\WbsVendors\Dgm\Shengine\Interfaces\IRate $rate)
    {
        return new self($rate->getCost(), $rate->getTitle(), $rate->isTaxable());
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function isTaxable()
    {
        return $this->taxable;
    }

    public function add(\WbsVendors\Dgm\Shengine\Interfaces\IRate $other)
    {
        $this->cost += $other->getCost();

        if (($title = $other->getTitle()) !== null) {
            $this->title = $title;
        }

        if (($taxable = $other->isTaxable()) !== null) {
            $this->taxable = $taxable;
        }
    }
}