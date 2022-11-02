<?php

namespace LicenseBuddy\Server\Config;

use WHMCS\Service\Service;
use WHMCS\Module\Addon\Setting;

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

class Config {

    public static function metaData() {
        
        return [
            'DisplayName'       => 'License Buddy',
            'APIVersion'        => '1.1',
            'RequiresServer'    => false,
        ];

    }

    public static function populate() {

        return [
            'License Key Length'    => [
                'Type'          => 'text',
                'Size'          => '20',
                'Description'   => 'Length of the license key (not including the prefix)',
                'Default'       => '12',
            ],
            'License Key Prefix'    => [
                'Type'          => 'text',
                'Size'          => '20',
                'Description'   => 'Optional prefix for the license key (e.g. License-)',
            ],
            'Allow License Reissue' => [
                'Type'          => 'yesno',
                'Description'   => 'Allow clients to reissue the license key',
                'Default'       => 'yes',
            ],
            'Application Key'       => [
                'Type'          => 'text',
                'Size'          => '20',
                'Description'   => 'Unique string to validate the license key and prevent misuse between license keys',
            ],
            'Domain Validation'     => [
                'Type'          => 'yesno',
                'Description'   => 'Whether to validate the installation domain against the key',
                'Default'       => 'yes',
            ],
            'Directory Validation'  => [
                'Type'          => 'yesno',
                'Description'   => 'Whether to validate the installation directory against the key',
                'Default'       => 'yes',
            ],
            'IP Address Validation' => [
                'Type'          => 'yesno',
                'Description'   => 'Whether to validation the installation IP address against the key',
                'Default'       => 'yes',
            ],
            'Trial License'         => [
                'Type'          => 'yesno',
                'Description'   => 'Use this key as a trial license',
                'Default'       => '',
            ],
            'Trial Length'          => [
                'Type'          => 'text',
                'Size'          => '20',
                'Description'   => 'Length of the trial period for this key (in days)',
                'Default'       => '14',
            ],
            'Restrict Trial By'     => [
                'Type'          => 'dropdown',
                'Options'       => 'Domain & IP Address,Domain Only,IP Address Only,No Restriction',
                'Description'   => 'Choose how to restrict the use of duplicate trial licenses and prevent trial abuse',
                'Default'       => 'Domain & IP Address',
            ],
        ];

    }

    public static function getMaster($setting) {
        return Setting::where('module', 'licensebuddy')->where('setting', $setting)->value('value');
    }

    public static function get($serviceId, $setting) {

        $option = null;

        switch ($setting) {

            case 'keyLength':
                $option = 'configoption1';
                break;
            case 'keyPrefix':
                $option = 'configoption2';
                break;
            case 'allowReissue':
                $option = 'configoption3';
                break;
            case 'applicationKey':
                $option = 'configoption4';
                break;
            case 'validateDomain':
                $option = 'configoption5';
                break;
            case 'validateDirectory':
                $option = 'configoption6';
                break;
            case 'validateIpAddress':
                $option = 'configoption7';
                break;
            case 'trialLicense':
                $option = 'configoption8';
                break;
            case 'trialLength':
                $option = 'configoption9';
                break;
            case 'trialRestriction':
                $option = 'configoption10';
                break;

        }

        $service =  Service::where('id', $serviceId)->with('product')->first();

        return $service->product->{$option};

    }

}