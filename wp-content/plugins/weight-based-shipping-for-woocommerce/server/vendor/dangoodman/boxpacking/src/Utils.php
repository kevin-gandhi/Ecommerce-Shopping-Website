<?php
namespace WbsVendors\BoxPacking;


class Utils
{
    public static function fillSkyline(&$projections, \WbsVendors\BoxPacking\Skyline $skyline, $insertCallback)
    {
        while ($projections && !$skyline->isFull()) {

            list($bestItemIndex, $itemProjection) = self::findBestFitItem($projections, $skyline);

            if (isset($bestItemIndex)) {
                $skyline->insertBox($itemProjection);
                call_user_func(\WbsVendors_CCR::kallable($insertCallback), $bestItemIndex, $itemProjection);
            } else {
                $skyline->fillCurrentGap();
            }
        }
    }

    public static function generateProjections(array $boxes)
    {
        return array_map(function($box) {
            return array_unique(array(
                $box,
                array($box[0], $box[2], $box[1]),
                array($box[1], $box[0], $box[2]),
                array($box[1], $box[2], $box[0]),
                array($box[2], $box[0], $box[1]),
                array($box[2], $box[1], $box[0]),
            ), SORT_REGULAR);
        }, $boxes);
    }

    private static function findBestFitItem($projections, \WbsVendors\BoxPacking\Skyline $skyline)
    {
        $bestItemIndex = null;
        $bestItemProjection = null;

        $bestFitnessValue = -1;
        foreach ($projections as $idx => $list) {

            foreach ($list as $projection) {

                $fitness = $skyline->getFitnessValue($projection);

                if ($fitness > $bestFitnessValue) {

                    $bestFitnessValue = $fitness;
                    $bestItemIndex = $idx;
                    $bestItemProjection = $projection;

                    if ($bestFitnessValue == \WbsVendors\BoxPacking\Skyline::MAX_FITNESS_VALUE) {
                        break;
                    }
                }
            }

            if ($bestFitnessValue == \WbsVendors\BoxPacking\Skyline::MAX_FITNESS_VALUE) {
                break;
            }
        }

        return array($bestItemIndex, $bestItemProjection);
    }
}