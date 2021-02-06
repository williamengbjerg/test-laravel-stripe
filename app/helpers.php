<?php

namespace App;

class helpers {

    /**
     * Return amount
     *
     * @param int $amount
     *
     * @return int
     */
    public static function planAmount(int $amount): int
    {
        return $amount / 100;
    }

}
