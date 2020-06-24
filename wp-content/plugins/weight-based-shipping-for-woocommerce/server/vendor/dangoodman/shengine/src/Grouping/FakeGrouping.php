<?php
namespace WbsVendors\Dgm\Shengine\Grouping;

use Dgm\Shengine\Interfaces\IGrouping;
use Dgm\Shengine\Interfaces\IPackage;


class FakeGrouping implements \WbsVendors\Dgm\Shengine\Interfaces\IGrouping
{
    public function split(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        return array($package);
    }

    public function multiplePackagesExpected()
    {
        return false;
    }
}