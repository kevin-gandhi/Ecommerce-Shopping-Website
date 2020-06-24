<?php
namespace WbsVendors\Dgm\Shengine\Operations;

use Exception;
use Dgm\Shengine\Interfaces\ICalculator;
use Dgm\Shengine\Interfaces\IPackage;
use Dgm\Shengine\Model\Rate;
use Dgm\Shengine\Processing\RateRegister;
use Dgm\Shengine\Processing\Registers;


class AddOperation extends \WbsVendors\Dgm\Shengine\Operations\AbstractOperation
{
    public function __construct(\WbsVendors\Dgm\Shengine\Interfaces\ICalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function process(\WbsVendors\Dgm\Shengine\Processing\Registers $registers, \WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        $newRates = isset($this->calculator) ? $this->calculator->calculateRatesFor($package) : array();
        if (!$newRates) {
            return;
        }

        if (count($registers->rates) > 1 && count($newRates) > 1) {
            throw new Exception("Adding up two rate sets is not supported due to ambiguity");
        }

        $registersRates = $registers->rates;
        if (!$registersRates) {
            $registersRates = array(new \WbsVendors\Dgm\Shengine\Model\Rate(0));
        }

        $newRegistersRates = array();
        foreach ($registersRates as $rate1) {
            foreach ($newRates as $rate2) {
                $rate = new \WbsVendors\Dgm\Shengine\Processing\RateRegister($rate1->getCost(), $rate1->getTitle());
                $rate->add($rate2);
                $newRegistersRates[] = $rate;
            }
        }

        $registers->rates = $newRegistersRates;
    }

    public function getType()
    {
        return $this->calculator->multipleRatesExpected() ? self::OTHER : self::MODIFIER;
    }

    public function canOperateOnMultipleRates()
    {
        return !$this->calculator->multipleRatesExpected();
    }

    private $calculator;
}