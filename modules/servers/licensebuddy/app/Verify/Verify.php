<?php

namespace LicenseBuddy\Server\Verify;

use WHMCS\Carbon;
use WHMCS\Service\Service;

use LicenseBuddy\Server\Models\Log;
use LicenseBuddy\Server\Models\Block;
use LicenseBuddy\Server\Config\Config;
use LicenseBuddy\Server\Helpers\Trial;
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

class Verify {

    public static function handleVerification($vars) {

        if (isset($_POST['licenseCheck']) && !empty($_POST['licenseKey'])) {
            header('Content-Type: application/json');
            
            die(self::check($_POST));

        }

    }

    public static function check($post) {

        $token      = isset($post['token']) ? $post['token'] : '';
        $domain     = isset($post['domain']) ? $post['domain'] : '';
        $ipAddress  = isset($post['ipAddress']) ? $post['ipAddress'] : '';
        $directory  = isset($post['directory']) ? $post['directory'] : '';
        $licenseKey = isset($post['licenseKey']) ? $post['licenseKey'] : '';

        if (empty($licenseKey)) {
            
            Log::create([
                'license_id'    => 0,
                'description'   => 'No license key provided',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'No license key provided',
                'licenseData'   => [],
            ]);

        }

        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {

            Log::create([
                'license_id'    => 0,
                'description'   => 'Invalid License Key',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'Invalid license key',
                'licenseData'   => [],
            ]);

        }

        $model = Service::with('product')->find($license->service_id);

        $pid            = $model->packageId;
        $applicationKey = Config::get($license->service_id, 'applicationKey');

        if ($post['validationHash'] != hash('sha256', $applicationKey . $token)) {
            Log::create([
                'license_id'    => 0,
                'description'   => 'Validation Hash Fail',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'Validation hash fail',
                'licenseData'   => [],
            ]);
        }

        if (empty($ipAddress)) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        License::setLastAccessed($license->id);

        if ($license->status == 'Expired') {

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'Expired License Key',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'expired',
                'message'       => 'The license key provided has expired',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        if ($license->status == 'Suspended') {

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'Suspended License Key',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'suspended',
                'message'       => 'The license key provided has been suspended',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        $allowedDomains     = $license->allowed_domains;
        $allowedIpAddress   = $license->allowed_ip_address;
        $allowedDirectory   = $license->allowed_directory;

        if ($license->status == 'Reissued') {

            if (substr($domain, 0, 4) == 'www.') {
                $domain = substr($domain, 4);
            }

            $allowedDomains     = $domain . ',www.' . $domain;
            $allowedIpAddress   = $ipAddress;
            $allowedDirectory   = $directory;

            License::where('id', $license->id)->update([
                'allowed_domains'       => $allowedDomains,
                'allowed_ip_address'    => $allowedIpAddress,
                'allowed_directory'     => $allowedDirectory,
                'status'                => 'Active',
            ]);

            if ($license->reissue_count > 0) {
                Log::create([
                    'license_id'    => $license->id,
                    'description'   => 'Reissued License Key',
                    'domain'        => $domain,
                    'ip_address'    => $ipAddress,
                    'directory'     => $directory,
                ]);
            }

        }

        if ($license->status == 'Reissued' || $license->status == 'Active') {

            $checkBlocked = Block::where('domain_ip', $domain)->orWhere('domain_ip', $ipAddress)->first();

            if ($checkBlocked) {

                License::suspend($license->service_id);

                localAPI('ModuleSuspend', [
                    'serviceid' => $license->service->id,
                    'suspendreason' => 'Blocked Domain/IP'
                ]);

                Log::create([
                    'license_id'    => $license->id,
                    'description'   => 'License Suspended due to a block on the Domain/IP (' . $checkBlocked->reason . ')',
                    'domain'        => $domain,
                    'ip_address'    => $ipAddress,
                    'directory'     => $directory,
                ]);

                return json_encode([
                    'status'        => 'suspended',
                    'message'       => 'The license key provided has been suspended due to a block put in place. Please contact support',
                    'licenseData'   => License::getData($license->id, $token),
                ]);

            }

        }

        $allowedDomainsArray = explode(',', $allowedDomains);

        if (!in_array($domain, $allowedDomainsArray) && Config::get($license->service_id, 'validateDomain') == 'on') {

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'Domain does not match the allowed domains on this license',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'Domain does not match the allowed domains on this license',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        if ($ipAddress != $allowedIpAddress && Config::get($license->service_id, 'validateIpAddress') == 'on') {

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'IP does not match the allowed IP address on this license',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'IP does not match the allowed IP address on this license',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        if ($directory != $allowedDirectory && Config::get($license->service_id, 'validateDirectory') == 'on') {

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'Directory does not match the allowed directory on this license',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'Directory does not match the allowed directory on this license',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        # Check if its a trial key.. if it is firstly check to see if expired, if not check for abuse

        $service = $license->service;

        // Check is trial
        $isTrial        = Trial::isTrial($service->id);
        $todaysDate     = Carbon::now();

        if ($isTrial == 'Yes' && $todaysDate->gt(Carbon::createFromFormat('d-m-Y', Trial::expiryDate($service->id)))) {

             localAPI('ModuleSuspend', [
                 'serviceid' => $service->id,
                 'suspendreason' => 'Trial License Expired'
             ]);

             // Suspend the license and the service
             License::where('service_id', $service->id)->update([
                 'status' => 'Expired'
             ]);

             $service->status = Config::getMaster('trialExpiredStatus');
             $service->save();

             Log::create([
                 'license_id'    => $license->id,
                 'description'   => 'The license key has been suspended as the trial license used has expired',
                 'domain'        => $domain,
                 'ip_address'    => $ipAddress,
                 'directory'     => $directory,
             ]);

             return json_encode([
                 'status'        => 'invalid',
                 'message'       => 'The license key has been suspended as the trial license used has expired',
                 'licenseData'   => License::getData($license->id, $token),
             ]);

        }

        # If is is a trial license and we are restricting domain and IP, do a check
        if ($isTrial == 'Yes' && Config::get($license->service_id, 'trialRestriction') == 'Domain & IP Address' && (Trial::checkDuplicateDomains($license->license_key, $allowedDomains) || Trial::checkDuplicateIpAddress($license->license_key, $allowedIpAddress))) {

            // Suspend the license and the service
            License::suspend($service->id);

            localAPI('ModuleSuspend', [
                'serviceid' => $service->id,
                'suspendreason' => 'Trial Key Abuse (Domain and/or IP Address match)'
            ]);

            $service->status = Config::getMaster('trialExpiredStatus');
            $service->save();

            License::where('license_key', $license->license_key)->update([
                'allowed_domains'       => '',
                'allowed_directory'     => '',
                'allowed_ip_address'    => ''
            ]);

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'The license key has been suspended as another trial key has been used in the past on the allowed domains and/or IP address specified',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'The license key has been suspended as another trial key has been used in the past on the allowed domains and/or IP address specified',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        # If it is a trial license and we are restricting the domain only, do a check
        if ($isTrial == 'Yes' && Config::get($license->service_id, 'trialRestriction') == 'Domain Only' && Trial::checkDuplicateDomains($license->license_key, $allowedDomains)) {

            // Suspend the license and the service
            License::suspend($service->id);

            localAPI('ModuleSuspend', [
                'serviceid'     => $service->id,
                'suspendreason' => 'Trial Key Abuse (Domain match)'
            ]);

            $service->status = Config::getMaster('trialExpiredStatus');
            $service->save();

            License::where('license_key', $license->license_key)->update([
                'allowed_domains'       => '',
                'allowed_directory'     => '',
                'allowed_ip_address'    => ''
            ]);

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'The license key has been suspended as another trial key has been used in the past on the allowed domains specified',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'The license key has been suspended as another trial key has been used in the past on the allowed domains specified',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        # If it is a trial license and we are restricting the IP Address only, do a check
        if ($isTrial == 'Yes' && Config::get($license->service_id, 'trialRestriction') == 'IP Address Only' && Trial::checkDuplicateIpAddress($license->license_key, $allowedIpAddress)) {

            // Suspend the license and the service
            License::suspend($service->id);

            localAPI('ModuleSuspend', [
                'serviceid'     => $service->id,
                'suspendreason' => 'Trial Key Abuse (IP Address match)'
            ]);

            $service->status = Config::getMaster('trialExpiredStatus');
            $service->save();

            License::where('license_key', $license->license_key)->update([
                'allowed_domains'       => '',
                'allowed_directory'     => '',
                'allowed_ip_address'    => ''
            ]);

            Log::create([
                'license_id'    => $license->id,
                'description'   => 'The license key has been suspended as another trial key has been used in the past on the allowed IP Address specified',
                'domain'        => $domain,
                'ip_address'    => $ipAddress,
                'directory'     => $directory,
            ]);

            return json_encode([
                'status'        => 'invalid',
                'message'       => 'The license key has been suspended as another trial key has been used in the past on the allowed IP Address specified',
                'licenseData'   => License::getData($license->id, $token),
            ]);

        }

        Log::create([
            'license_id'    => $license->id,
            'description'   => 'Remote license call successful',
            'domain'        => $domain,
            'ip_address'    => $ipAddress,
            'directory'     => $directory,
        ]);

        return json_encode([
            'status'        => 'active',
            'message'       => '',
            'licenseData'   => License::getData($license->id, $token),
        ]);

    }

}
