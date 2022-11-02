<?php

namespace LicenseBuddy\Addon\Hooks;

use WHMCS\Carbon;
use WHMCS\Service\Service;
use WHMCS\Service\CancellationRequest;

use LicenseBuddy\Addon\Models\Log;
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

class Cron {

    public static function syncLicenseStatusWithParentServiceStatus($vars) {

        if (!Config::get('syncServiceLicenseStatuses')) {
            return;
        }
    
        $licenses = License::all();
    
        foreach ($licenses as $license) {
    
            $service = Service::where('id', $license->service_id)->first();
    
            if ($service->status == 'Active' && $license->status != 'Active' && $license->status != 'Reissued') {
    
                # Update license status to Active
                $license->status = 'Active';
                $license->save();
    
                logActivity("License Buddy Status Sync Cron: Updated status of license key '{$license->license_key}' under service ID: {$license->service_id} to Active due to parent service being marked as Active", $service->userid);
                continue;
    
            } else if ($service->status == 'Suspended' && $license->status != 'Suspended') {
                
                # Update license status to Suspended
                $license->status = 'Suspended';
                $license->save();
    
                logActivity("License Buddy Status Sync Cron: Updated status of license key '{$license->license_key}' under service ID: {$license->service_id} to Suspended due to parent service being marked as Suspended", $service->userid);
                continue;
    
            } else if ($service->status == 'Terminated' && $license->status != 'Expired') {
    
                # Update license status to Expired
                $license->status = 'Expired';
                $license->save();
    
                logActivity("License Buddy Status Sync Cron: Updated status of license key '{$license->license_key}' under service ID: {$license->service_id} to Expired due to parent service being marked as Terminated", $service->userid);
                continue;
    
            } else if ($service->status == 'Fraud' && $license->status != 'Suspended') {
    
                # Update license status to Suspended
                $license->status = 'Suspended';
                $license->save();
    
                logActivity("License Buddy Status Sync Cron: Updated status of license key '{$license->license_key}' under service ID: {$license->service_id} to Suspended due to parent service being marked as Fraud", $service->userid);
                continue;
    
    
            } else if ($service->status == 'Cancelled' && CancellationRequest::where('relid', $license->service_id)->where('type', 'Immediate')->count() != 0 && $license->status != 'Suspended') {
    
                # Update license status to Suspended
                $license->status = 'Suspended';
                $license->save();
    
                logActivity("License Buddy Status Sync Cron: Updated status of license key '{$license->license_key}' under service ID: {$license->service_id} to Cancelled due to parent service being marked as Cancelled", $service->userid);
                continue;
    
            }
    
        }

    }

    public static function pruneAccessLogs($vars) {

        if (!Config::get('logPrune') || Config::get('logPrune') == '-1' || !is_numeric(Config::get('logPrune'))) {
            return;
        }
    
        Log::where('created_at', '<=', Carbon::now()->subDays(Config::get('logPrune'))->toDateTimeString())->delete();

        logActivity("License Buddy Cron: Pruned all access logs before " . Carbon::now()->subDays(Config::get('logPrune'))->toDateTimeString());
    
    }

}