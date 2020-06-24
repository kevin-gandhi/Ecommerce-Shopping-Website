<?php
namespace WbsVendors\Dgm\Shengine\Model;

use InvalidArgumentException;
use Dgm\Shengine\Interfaces\IRate;


class Rate implements \WbsVendors\Dgm\Shengine\Interfaces\IRate
{
    public function __construct($cost, $title = null, $taxable = null)
    {
        if (!is_numeric($cost)) {
            throw new InvalidArgumentException();
        }

        $this->cost = $cost;
        $this->title = $title;
        $this->taxable = $taxable;
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

    private $cost;
    private $title;
    private $taxable;
}