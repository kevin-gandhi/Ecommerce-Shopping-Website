<?php
namespace WbsVendors\Dgm\Shengine\Interfaces;


interface IGrouping
{
    /**
     * Implementations might return the original $package, even multiple times and/or create new packages even if they
     * are same to the original one. No assumptions on that should be made since IPackage is a Value Object.
     *
     * @param IPackage $package
     * @return IPackage[]
     */
    function split(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package);

    /**
     * @return bool False if no more than one package is expected to be produced by this grouping. Expected to be true
     *              for all groupings except {@see FakeGrouping}.
     */
    function multiplePackagesExpected();
}