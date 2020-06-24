<?php
namespace WbsVendors\Dgm\Shengine\Conditions\Package;

use InvalidArgumentException;
use Dgm\Shengine\Interfaces\ICondition;
use Dgm\Shengine\Interfaces\IPackage;
use Dgm\Shengine\Model\Package;


class TermsCondition extends \WbsVendors\Dgm\Shengine\Conditions\Package\AbstractPackageCondition
{
    const SEARCH_ANY = 'search_any';
    const SEARCH_ALL = 'search_all';
    const SEARCH_NO = 'search_no';


    public function __construct($needleTermsByTaxonomy, $search = self::SEARCH_ANY, $allowOthers = true, \WbsVendors\Dgm\Shengine\Interfaces\ICondition $matchingItemsConstraint = null)
    {
        if (!in_array((string)$search, array(self::SEARCH_ANY, self::SEARCH_ALL, self::SEARCH_NO), true)) {
            throw new InvalidArgumentException("Unknown search mode '{$search}'");
        }

        $this->needleTermsByTaxonomy = self::receiveTermsByTaxonomy($needleTermsByTaxonomy);
        $this->searchMode = $search;
        $this->allowOthers = $allowOthers;
        $this->matchingItemsConstraint = $matchingItemsConstraint;
    }

    protected function isSatisfiedByPackage(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        /* // Eliminated purposely
        if (!$this->needleTermsByTaxonomy) {
            return true;
        }*/

        $match = $this->searchMode === self::SEARCH_ANY ? false : true;

        foreach ($this->needleTermsByTaxonomy as $taxonomy => $needle) {

            $haystack = $package->getTerms($taxonomy);

            $intersections = count(array_intersect($needle, $haystack));

            switch ($this->searchMode) {
                case self::SEARCH_ANY:
                    $match = $match || $intersections > 0;
                    break;
                case self::SEARCH_ALL:
                    $match = $match && $intersections == count($needle);
                    break;
                case self::SEARCH_NO:
                    $match = $match && $intersections == 0;
                    break;
            }

            if ($this->allowOthers) {
                if ($match == ($this->searchMode == self::SEARCH_ANY)) {
                    break;
                }
            } elseif ($intersections != count($haystack)) {
                $match = false;
                break;
            }
        }

        if ($match && isset($this->matchingItemsConstraint)) {

            $matchingItems = array();
            foreach ($package->getItems() as $item) {
                foreach ($this->needleTermsByTaxonomy as $taxonomy => $searchTerms) {
                    if (count(array_intersect($searchTerms, $item->getTerms($taxonomy)))) {
                        $matchingItems[] = $item;
                        break;
                    }
                }
            }

            $matchingPackage = new \WbsVendors\Dgm\Shengine\Model\Package($matchingItems, $package->getDestination(), $package->getCustomer(), $package->getCoupons());

            $match = $this->matchingItemsConstraint->isSatisfiedBy($matchingPackage);
        }

        return $match;
    }

    
    private $needleTermsByTaxonomy;
    private $searchMode;
    private $allowOthers;
    private $matchingItemsConstraint;

    static private function normalize(array $terms)
    {
        return array_unique($terms);
    }

    static private function receiveTermsByTaxonomy(array $input)
    {
        foreach ($input as $taxonomy => &$terms) {

            if (!is_array($terms)) {
                throw new InvalidArgumentException();
            }

            if (!$terms) {
                unset($input[$taxonomy]);
            } else {
                $terms = self::normalize($terms);
            }
        }

        return $input; 
    }
}