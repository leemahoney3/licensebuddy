<?php

use LicenseBuddy\Server\App;
use LicenseBuddy\Server\Config\Config;

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

if (!defined('WHMCS')) {
    exit('This file cannot be accessed directly.');
}

require_once __DIR__ . '/vendor/autoload.php';

function licensebuddy_MetaData() {
    return Config::metaData();
}

function licensebuddy_ConfigOptions() {
    return Config::populate();
}

function licensebuddy_CreateAccount($params) {
    return App::createLicense($params);
}

function licensebuddy_SuspendAccount($params) {
    return App::suspendLicense($params);
}

function licensebuddy_UnsuspendAccount($params) {
    return App::unsuspendLicense($params);
}

function licensebuddy_TerminateAccount($params) {
    return App::terminateLicense($params);
}

function licensebuddy_AdminCustomButtonArray() {
    return App::customButtonArray('admin');
}

function licensebuddy_ClientAreaCustomButtonArray($params) {
    return App::customButtonArray('client', $params);
}

function licensebuddy_reissueLicense($params) {
    return App::reissueLicense($params);
}

function licensebuddy_resetReissue($params) {
    return App::resetReissue($params);
}

function licensebuddy_deleteLicense($params) {
    return App::deleteLicense($params);
}

function licensebuddy_AdminServicesTabFields($params) {
    return App::servicePageOutput($params);
}

function licensebuddy_AdminServicesTabFieldsSave($params) {
    return App::servicePageSave($params);
}

function licensebuddy_ClientArea($params) {
    return App::clientAreaOutput($params);
}