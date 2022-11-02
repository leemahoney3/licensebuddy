<?php

namespace LicenseBuddy\Addon\Hooks;

use LicenseBuddy\Addon\Config\Config;
use LicenseBuddy\Addon\Models\License;

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

class Service {

    public static function deleteLicenseOnServiceDeletion($vars) {

        if (!Config::get('deleteLicenseWithService')) {
            return;
        }
        
        License::where('service_id', $vars['serviceid'])->delete();

    }

}