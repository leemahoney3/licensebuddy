<?php

namespace LicenseBuddy\Addon\Config;

use WHMCS\Module\Addon\Setting;

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

class Config {

    public static function populate() {

        return [
            'name'          => 'License Buddy',
            'description'   => 'Software licensing module for WHMCS',
            'version'       => '1.0.0',
            'author'        => '<a href="https://leemahoney.dev">Lee Mahoney</a>',
            'language'      => 'english',
            'fields'        => [
                'clientVerifyTool'  => [
                    'FriendlyName'  => 'Client Area License Checker',
                    'Type'          => 'yesno',
                    'Description'   => 'Check to enable the license checker in the client area',
                    'Default'       => 'yes',
                ],
                'maxReissues'       => [
                    'FriendlyName'  => 'Maximum Allowed Reissues',
                    'Type'          => 'text',
                    'Size'          => '4',
                    'Default'       => '10',
                    'Description'   => 'Enter the maximum number of reissues you want to allow. Enter -1 to disable.',
                ],
                'logPrune'          => [
                    'FriendlyName'  => 'Access Logs Prune',
                    'Type'          => 'text',
                    'Size'          => '4',
                    'Default'       => '90',
                    'Description'   => 'Enter the number of days to keep license access log history for. Enter -1 to disable.',
                ],
                'deleteTables'      => [
                    'FriendlyName'  => 'Delete Database Tables',
                    'Type'          => 'yesno',
                    'Description'   => 'Delete database tables when deactivating the module',
                    'Default'       => 'yes',
                ],
                'deleteLicenseWithService' => [
                    'FriendlyName'  => 'Delete License with Service',
                    'Type'          => 'yesno',
                    'Description'   => 'If enabled and a service is deleted, it\'s associated license key will also be deleted',
                    'Default'       => 'yes',
                ],
                'syncServiceLicenseStatuses' => [
                    'FriendlyName'  => 'Sync Service/License Statuses',
                    'Type'          => 'yesno',
                    'Description'   => 'If enabled, the daily cron will sync all license statuses to that of their parent service',
                    'Default'       => 'yes'
                ],
                'trialExpiredStatus'        => [
                    'FriendlyName'  => 'Status of Trial License on Expiry',
                    'Type'          => 'dropdown',
                    'Options'       => 'Suspended,Cancelled',
                    'Default'       => 'Suspended'
                ],
            ],
        ];

    }

    public static function get($setting) {

        return Setting::where('module', 'licensebuddy')->where('setting', $setting)->value('value');

    }

}