<?php
namespace WbsVendors\Dgm\Shengine\Interfaces;

use Dgm\Shengine\Model\Customer;
use Dgm\Shengine\Model\Destination;


interface IPackage extends \WbsVendors\Dgm\Shengine\Interfaces\IItemAggregatables
{
    const NONE_VIRTUAL_TERM_ID = '-1';

    /**
     * @return IItem[]
     */
    function getItems();
    
    /**
     * @return bool
     */
    function isEmpty();

    /**
     * @return Destination|null
     */
    function getDestination();

    /**
     * @return Customer|null
     */
    function getCustomer();

    /**
     * @return string[]
     */
    function getCoupons();
    
    /**
     * @param IGrouping $by
     * @return IPackage[]
     */
    function split(\WbsVendors\Dgm\Shengine\Interfaces\IGrouping $by);

    /**
     * @param IPackage[]|IPackage $with
     * @return IPackage
     */
    function merge($with);

    /**
     * @param IPackage[]|IPackage $other
     * @return IPackage
     */
    function exclude($other);
}