<?php

namespace LicenseBuddy\Addon\Helpers;

/**
 * License Buddy Addon
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

class RedirectHelper {

    public static function to($url) {
        
        header("Location: {$url}");
        exit;

    }

    public static function page($page, $args = []) {

        $url = "addonmodules.php?module=licensebuddy&page={$page}";

        if (!empty($args)) {

            foreach ($args as $arg => $value) {
                $url .= "&{$arg}={$value}";
            }

        }

        self::to($url);

    }

}