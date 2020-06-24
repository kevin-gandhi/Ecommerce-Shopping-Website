<?php
namespace WbsVendors\BoxPacking;

use OverflowException;


class Skyline
{
    const MAX_FITNESS_VALUE = 3;


    public function __construct(array $bounds)
    {
        $this->levels = array();
        $this->levels[] = array(-1, $bounds[1]);
        $this->levels[] = array(0, 0);
        $this->levels[] = array($bounds[0], $bounds[1]);

        $this->setLowestLevelIndex(1);

        $this->depth = $bounds[2];
    }

    public function isFull()
    {
        return count($this->levels) < 2;
    }

    public function insertBox(array $box)
    {
        if ($this->isFull()) {
            throw new OverflowException("Skyline is full");
        }

        $lowestLevelIndex = $this->lowestLevelIndex;

        // Insert a new level before the alignment level
        $newLevelPos = $this->alignLevel[0] + ($this->alignLeft ? 0 : -$box[0]);
        $newLevelHeight = $box[1] + $this->lowestLevel[1];
        $newLevelIndex = $this->alignLevelIndex;
        array_splice($this->levels, $newLevelIndex, 0, array(array($newLevelPos, $newLevelHeight)));

        if ($newLevelIndex <= $lowestLevelIndex) {
            $lowestLevelIndex++;
            $this->lowestLevel[0] += $box[0];
        }

        // Box fills gap exactly
        if ($this->levels[$lowestLevelIndex + 1][0] - $this->lowestLevel[0] == 0) {

            // Remove null-width current level
            unset($this->levels[$lowestLevelIndex]);
            $this->levels = array_values($this->levels);

            if ($lowestLevelIndex < $newLevelIndex) {
                $newLevelIndex--;
            }

            unset($lowestLevelIndex);
        }

        // Remove consecutive levels
        foreach (array($newLevelIndex + 1, $newLevelIndex) as $idx) {

            if ($this->levels[$idx][1] == $this->levels[$idx - 1][1]) {

                unset($this->levels[$idx]);

                if (isset($lowestLevelIndex) && $idx < $lowestLevelIndex) {
                    $lowestLevelIndex--;
                }
            }
        }
        $this->levels = array_values($this->levels);

        if ($this->isFull()) {
            return;
        }

        if (!isset($lowestLevelIndex)) {

            $lowestLevelIndex = 1;

            $min = PHP_INT_MAX;
            foreach ($this->levels as $idx => $level) {

                if (($h = $level[1]) < $min) {

                    $min = $h;
                    $lowestLevelIndex = $idx;

                    if ($min == 0) {
                        break;
                    }
                }
            }
        }

        $this->setLowestLevelIndex($lowestLevelIndex);
    }

    public function getFitnessValue(array $box)
    {
        if ($box[0] > $this->gapWidth || $box[1] > $this->gapHeight || $box[2] > $this->depth) {
            return -1;
        }

        $score = 0;

        if (abs($this->anotherLevel[0] - $this->alignLevel[0]) == $box[0]) {
            $score += 2;
        }

        $boxheight = $this->lowestLevel[0] + $box[1];
        if ($boxheight == $this->previousLevel[1] ||
            $boxheight == $this->nextLevel[1]) {
            $score += 1;
        }

        return $score;
    }

    public function fillCurrentGap()
    {
        $this->insertBox(array(
            $this->gapWidth,
            min($this->previousLevel[1], $this->nextLevel[1]) - $this->lowestLevel[1],
            $this->depth
        ));
    }

    private $depth;

    private $levels;

    private $lowestLevelIndex;
    private $lowestLevel;

    private $previousLevel;
    private $nextLevel;

    private $gapWidth;
    private $gapHeight;

    private $alignLeft;

    private $alignLevelIndex;
    private $alignLevel;

    private $anotherLevelIndex;
    private $anotherLevel;

    private function setLowestLevelIndex($index)
    {
        $this->lowestLevelIndex = $index;
        $this->lowestLevel = &$this->levels[$index];

        $this->previousLevel = &$this->levels[$index - 1];
        $this->nextLevel = &$this->levels[$index + 1];

        $this->gapWidth = $this->nextLevel[0] - $this->lowestLevel[0];
        $this->gapHeight = $this->levels[0][1] - $this->lowestLevel[1];

        $this->alignLeft = $this->previousLevel[1] >= $this->nextLevel[1];

        $this->alignLevelIndex = $index + (int)(!$this->alignLeft);
        $this->alignLevel = &$this->levels[$this->alignLevelIndex];

        $this->anotherLevelIndex = $index + (int)($this->alignLeft);
        $this->anotherLevel = &$this->levels[$this->anotherLevelIndex];
    }
}