<?php

namespace LicenseBuddy\Server\Helpers;

use WHMCS\Carbon;
use WHMCS\Product\Product;
use WHMCS\Service\Service;

use LicenseBuddy\Server\Config\Config;
use LicenseBuddy\Server\Models\License;

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

class Trial {

    public static function isTrial($serviceId) {

        return Config::get($serviceId, 'trialLicense') == 'on' ? 'Yes' : 'No';

    }
   
    public static function expiryDate($serviceId) {

        $service = Service::where('id', $serviceId)->first();

        return (self::isTrial($service->id) == 'Yes' && Config::get($service->id, 'trialLength') !== '' && is_numeric(Config::get($service->id, 'trialLength'))) ? Carbon::createFromFormat('Y-m-d', $service->regdate)->addDays(Config::get($service->id, 'trialLength'))->format('d-m-Y') : null;

    }

    public static function checkDuplicateDomains($licenseKey, $domains) {

        $licenses = License::where('allowed_domains', $domains)->get();

        foreach ($licenses as $license) {

            if (self::isTrial($license->service_id) == 'Yes' && $licenseKey != $license->license_key) {
                return true;
            }

        }

        return false;

    }

    public static function checkDuplicateIpAddress($licenseKey, $ipAddress) {

        $licenses = License::where('allowed_ip_address', $ipAddress)->get();

        foreach ($licenses as $license) {
            
            if (self::isTrial($license->service_id) == 'Yes' && $licenseKey != $license->license_key) {
                return true;
            }

        }

        return false;

    }

    public static function checkTrialLicenseExpiredAndSuspend($vars) {

        $products = Product::where([
            'servertype'    => 'licensebuddy',
            'configoption8' => 'on'
        ])->pluck('id')->toArray();

        $services = Service::where('domainstatus', 'Active')->whereIn('packageid', $products)->get();

        foreach ($services as $service) {
            
            // Check is trial
            $isTrial        = self::isTrial($service->id);
            $trialExpiry    = Carbon::createFromFormat('d-m-Y', self::expiryDate($service->id));
            $todaysDate     = Carbon::now();

            if ($isTrial == 'Yes' && $todaysDate->gt($trialExpiry)) {

                $license = License::where('service_id', $service->id)->first();

                // Suspend the license and the service
                License::suspend($service->id);

                localAPI('ModuleSuspend', [
                    'serviceid' => $service->id,
                    'suspendreason' => 'Trial License Expired'
                ]);

                logActivity("License Buddy Trial Cron: Trial license key '{$license->license_key}' has expired (expiry date: {$trialExpiry}). Updated status of service ID: {$license->service_id} to Cancelled", $service->userid);

                $service->status = Config::getMaster('trialExpiredStatus');
                $service->save();

            }

        }

    }

}