<?php
namespace WbsVendors\Dgm\Shengine\Model;

use Dgm\Shengine\Interfaces\IItem;


class Item implements \WbsVendors\Dgm\Shengine\Interfaces\IItem
{
    public function __construct()
    {
        $defaults = ItemDefaults::get();
        $this->price = $defaults->price;
        $this->weight = $defaults->weight;
        $this->dimensions = $defaults->dimensions;
    }

    public static function create()
    {
        return new static();
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = self::receiveString($productId);
        return $this;
    }

    public function getProductVariationId()
    {
        return $this->productVariationId;
    }

    public function setProductVariationId($productVariationId)
    {
        $this->productVariationId = self::receiveString($productVariationId);
        return $this;
    }

    public function getPrice($flags = \WbsVendors\Dgm\Shengine\Model\Price::BASE)
    {
        return $this->price->getPrice($flags);
    }

    public function setPrice(\WbsVendors\Dgm\Shengine\Model\Price $price)
    {
        $this->price = $price;
        return $this;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = (float)$weight;
        return $this;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }

    public function setDimensions(\WbsVendors\Dgm\Shengine\Model\Dimensions $dimensions)
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    public function getTerms($taxonomy)
    {
        return (array)@$this->terms[$taxonomy];
    }

    public function setTerms($taxonomy, array $terms = null)
    {
        if (func_num_args() == 1 && is_array($taxonomy)) {
            $terms = $taxonomy;
        } else {
            $terms = array($taxonomy => $terms);
        }

        $this->terms = array_merge($this->terms, $terms);
        
        return $this;
    }


    private $productId;
    private $productVariationId;
    private $price;
    private $weight;
    private $dimensions;
    private $terms = array(); 

    private static function receiveString($value)
    {
        return isset($value) ? (string)$value : null;
    }
}


class ItemDefaults
{
    public $price;
    public $weight;
    public $dimensions;


    public static function get()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    private static $instance;

    private function __construct()
    {
        $this->price = new \WbsVendors\Dgm\Shengine\Model\Price(0, 0, 0, 0);
        $this->weight = 0;
        $this->dimensions = new \WbsVendors\Dgm\Shengine\Model\Dimensions(0, 0, 0);
    }
}