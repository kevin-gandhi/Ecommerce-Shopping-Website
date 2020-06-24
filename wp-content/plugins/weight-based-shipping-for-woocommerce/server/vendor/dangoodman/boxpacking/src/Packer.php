<?php
namespace WbsVendors\BoxPacking;


class Packer
{
    public function __construct($precision = 1, $checkGrossVolume = true)
    {
        $this->precision = $precision;
        $this->checkGrossVolume = $checkGrossVolume;
    }

    public function canPack($box, $items)
    {
        $box = @reset(self::prepareBoxes(array($box), $this->precision, false));
        if (!$box) {
            return false;
        }

        $items = self::orderByPerimiter(self::prepareBoxes($items, $this->precision, true));
        if (!$items) {
            return true;
        }

        if ($this->checkGrossVolume && self::calculateVolume($items) > self::calculateVolume(array($box))) {
            return false;
        }

        return self::place($box, $items);
    }

    private static function place($box, $items)
    {
        $rotate = function($box) {
            return array($box[1], $box[2], $box[0]);
        };

        $sideBox = $rotate($box);
        $sideItems = array_map($rotate, $items);

        $projections = \WbsVendors\BoxPacking\Utils::generateProjections($items);
        $sideProjections = \WbsVendors\BoxPacking\Utils::generateProjections($sideItems);

        unset($items, $sideItems);


        $sideSkyline = new \WbsVendors\BoxPacking\Skyline($sideBox);

        \WbsVendors\BoxPacking\Utils::fillSkyline($sideProjections, $sideSkyline, function ($bestItemIndex, $bestSideProjection) use ($box, &$sideProjections, &$projections) {

            $frontSkyline = new \WbsVendors\BoxPacking\Skyline(array($box[0], $bestSideProjection[0], $bestSideProjection[1]));
            $frontSkyline->insertBox(array($bestSideProjection[2], $bestSideProjection[0], $bestSideProjection[1]));

            unset($projections[$bestItemIndex], $sideProjections[$bestItemIndex]);

            \WbsVendors\BoxPacking\Utils::fillSkyline($projections, $frontSkyline, function ($bestItemIndex) use (&$sideProjections, &$projections) {
                unset($projections[$bestItemIndex], $sideProjections[$bestItemIndex]);
            });
        });


        return empty($sideProjections);
    }

    private static function calculateVolume(array $boxes)
    {
        $volume = 0;

        foreach ($boxes as $box) {
            $volume += $box[0] * $box[1] * $box[2];
        }

        return $volume;
    }

    private static function orderByPerimiter(array $boxes)
    {
        usort($boxes, function($b1, $b2) {
            $diff = array_sum($b2) - array_sum($b1);
            return ($diff > 0) - ($diff < 0);
        });

        return $boxes;
    }

    private static function prepareBoxes(array $boxes, $precision = 0, $roundUp = true)
    {
        return array_filter(array_map(function($box) use($precision, $roundUp) {

            // Convert box dimensions to integer
            foreach ($box as &$dimension) {
                $dimension *= $precision;
                $dimension = $roundUp ? ceil($dimension) : floor($dimension);
                $dimension = (int)$dimension;
            }

            // Layout boxes uniformly
            rsort($box, SORT_DESC);

            if (end($box) == 0) {
                return null;
            }

            return $box;

        }, $boxes));
    }

    private $precision;
    private $checkGrossVolume;
}