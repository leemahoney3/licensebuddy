<?php

namespace LicenseBuddy\Server\Helpers;

/**
 * License Buddy Server Module
 *
 * A software licensing solution for WHMCS
 *
 * @package    LicenseBuddy
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    1.0.0
 * @link       https://leemahoney.dev
 */

class KeyGenerator {

    public static function generate($length, $prefix) {

        if (!$length) {
            $length = 12;
        }

        $chars = array_merge(range('a', 'f'), range(0, 9));
        $count = count($chars);

        $key = '';

        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, $count - 1)];
        }

        return $prefix . $key;

    }

}