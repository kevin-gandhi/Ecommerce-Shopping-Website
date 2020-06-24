<?php
namespace WbsVendors\Dgm\Shengine;

use Dgm\Shengine\Interfaces\ICondition;
use Dgm\Shengine\Interfaces\IMatcher;
use Dgm\Shengine\Interfaces\IPackage;
use Dgm\Shengine\Model\Package;


class RuleMatcher implements \WbsVendors\Dgm\Shengine\Interfaces\IMatcher
{
    public function __construct(\WbsVendors\Dgm\Shengine\RuleMatcherMeta $meta, \WbsVendors\Dgm\Shengine\Interfaces\ICondition $condition)
    {
        $this->meta = $meta;
        $this->condition = $condition;
    }

    public function getMatchingPackage(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        $packages = $package->split($this->meta->grouping);

        $matchingPackages = array();
        foreach ($packages as $package) {
            if ($this->condition->isSatisfiedBy($package)) {
                $matchingPackages[] = $package;
            } else if ($this->meta->requireAllPackages) {
                return null;
            }
        }

        if (!$matchingPackages) {
            return null;
        }

        return \WbsVendors\Dgm\Shengine\Model\Package::fromOther($matchingPackages, $package->getDestination(), $package->getCustomer(), $package->getCoupons());
    }

    public function isCapturingMatcher()
    {
        return $this->meta->capture;
    }

    private $meta;
    private $condition;
}