<?php

use LicenseBuddy\Addon\App;
use LicenseBuddy\Addon\Admin\Admin;
use LicenseBuddy\Addon\Config\Config;
use LicenseBuddy\Addon\Client\ClientArea;

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

if (!defined('WHMCS')) {
    exit('This file cannot be accessed directly.');
}

require_once __DIR__ . '/vendor/autoload.php';

function licensebuddy_config() {

    return Config::populate();

}

function licensebuddy_activate() {

    return App::activate();

}

function licensebuddy_deactivate() {

    return App::deactivate();

}

function licensebuddy_output($vars) {

    return Admin::output($vars);

}

function licensebuddy_clientarea($vars) {

    return ClientArea::output($vars);

}