<?php

namespace App\Helpers;

class MathHelper
{
    public static function getPercentageChange($oldNumber, $newNumber): float|int
    {
        if ($oldNumber === 0 && $newNumber === 0) {
            return 0;
        }

        $decreaseValue = $newNumber - $oldNumber;

        if ($oldNumber > $newNumber) {
            if ($newNumber > 0) {
                $result = round(($decreaseValue / $newNumber) * 100, 2);
            } else {
                $result = -100;
            }
        } else {
            if ($oldNumber > 0) {
                $result = round(($decreaseValue / $oldNumber) * 100, 2);
            } else {
                $result = 100;
            }
        }

        return $result;
    }

}
