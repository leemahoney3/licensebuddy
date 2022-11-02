<?php

namespace LicenseBuddy\Server;

use WHMCS\Carbon;
use WHMCS\Session;
use WHMCS\Config\Setting;
use WHMCS\Service\Service;

use LicenseBuddy\Server\Models\Log;
use LicenseBuddy\Server\Config\Config;
use LicenseBuddy\Server\Helpers\Trial;
use LicenseBuddy\Server\Models\License;
use LicenseBuddy\Server\Helpers\KeyGenerator;

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

class App {

    public static function createLicense($params) {
        
        if (License::checkExists($params['serviceid'])) {
            return 'A valid license has already been generated for this service';
        }

        $length = Config::get($params['serviceid'], 'keyLength');
        $prefix = Config::get($params['serviceid'], 'keyPrefix');

        $licenseKey = KeyGenerator::generate($length, $prefix);

        License::add($licenseKey, $params['serviceid']);

        Service::where('id', $params['serviceid'])->update([
            'domain'    => $licenseKey,
            'username'  => '',
            'password'  => '',
        ]);

        return 'success';

    }

    public static function suspendLicense($params) {

        if (!License::checkExists($params['serviceid'])) {
            return 'No license exists for this service';
        }
    
        License::suspend($params['serviceid']);

        return 'success';

    }

    public static function unsuspendLicense($params) {

        if (!License::checkExists($params['serviceid'])) {
            return 'No license exists for this service';
        }
    
        License::unsuspend($params['serviceid']);

        return 'success';

    }

    public static function terminateLicense($params) {

        if (!License::checkExists($params['serviceid'])) {
            return 'No license exists for this service';
        }
    
        License::terminate($params['serviceid']);

        return 'success';

    }

    public static function customButtonArray($scope, $params = null) {

        if ($scope == 'admin') {

            return [
                'Reissue License'   => 'reissueLicense',
                'Reset Reissues'    => 'resetReissue',
                'Delete License'    => 'deleteLicense',
            ];

        } else if ($scope == 'client') {

            $allowReissue = Config::get($params['serviceid'], 'allowReissue');

            if ($allowReissue) {
                return [
                    'Reissue License' => 'reissueLicense',
                ];
            }

            return [];

        } else {
            return [];
        }

    }

    public static function reissueLicense($params) {

        $license        = License::grab($params['serviceid']);
        $allowReissue   = Config::get($params['serviceid'], 'allowReissue');

        if (!$license) {
            return 'No license exists for this service';
        }

        if (!Session::get('adminid') && !$allowReissue) {
            return 'This license key cannot be reissued';
        }

        if ($license->status != 'Active') {
            return 'License key must be active before it can be reissued';
        }

        $maxReissues = Config::getMaster('maxReissues');

        if (!Session::get('adminid') && $maxReissues && $maxReissues <= $license->reissue_count) {
            return 'The maximum number of reissues allowed has been reached for this license';
        }

        License::reissue($license->id);

        return 'success';

    }

    public static function resetReissue($params) {

        $license = License::grab($params['serviceid']);

        if (!$license) {
            return 'No license exists for this service';
        }

        License::resetReissues($license->id);

        return 'success';

    }

    public static function deleteLicense($params) {

        $license = License::grab($params['serviceid']);

        if (!$license) {
            return 'No license exists for this service';
        }

        $license->delete();

        Service::where('id', $params['serviceid'])->update([
            'domain'    => '',
            'username'  => '',
            'password'  => '',
        ]);

        return 'success';

    }

    public static function servicePageOutput($params) {

        $license = License::grab($params['serviceid']);

        if (!$license) {
            return [];
        }


        $trialLicense = Trial::isTrial($params['serviceid']);

        $trialExpiry = Trial::expiryDate($params['serviceid']);

        $relevantData       = '<div class="well"><p><b>License Key:</b> ' . $license->license_key . '</p><p><b>Reissue Count:</b> ' . $license->reissue_count . '</p><p><b>Last Accessed:</b> ' . $license->lastAccessedAt() . '</p><p><b>Trial License:</b> ' . $trialLicense . '</p>';
        $relevantData      .=  $trialExpiry ? '<p><b>Trial Expires:</b> ' . $trialExpiry . '</p>' : '';
        $relevantData      .= '</div>';
        $allowedDomains     = '<input type="text" name="licensebuddy[allowed_domains]" class="form-control" style="width: 20%;" value="' . $license->allowed_domains . '" />';
        $allowedIpAddress   = '<input type="text" name="licensebuddy[allowed_ip_address]" class="form-control" style="width: 20%;" value="' . $license->allowed_ip_address . '" />';
        $allowedDirectory   = '<input type="text" name="licensebuddy[allowed_directory]" class="form-control" style="width: 20%;" value="' . $license->allowed_directory . '" />';
        $status             = '<select name="licensebuddy[status]" class="form-control" style="width: 20%;">';
        $status            .= ($license->status == 'Active') ? '<option selected>Active</option>' : '<option>Active</option>';
        $status            .= ($license->status == 'Reissued') ? '<option selected>Reissued</option>' : '<option>Reissued</option>';
        $status            .= ($license->status == 'Suspended') ? '<option selected>Suspended</option>' : '<option>Suspended</option>';
        $status            .= ($license->status == 'Expired') ? '<option selected>Expired</option>' : '<option>Expired</option>';
        $status            .= "</select>";


        $logs = Log::where('license_id', $license->id)->limit(5)->orderBy('created_at', 'desc')->get();
        
        $accessLogs = '
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date/Time</th>
                            <th scope="col">Domain</th>
                            <th scope="col">IP Address</th>
                            <th scope="col">Directory</th>
                            <th scope="col">Description</th>
                        </tr>
                    </thead>
                    <tbody>
            ';
        
        foreach ($logs as $log) {

            $accessLogs .= "
                        <tr>
                            <td>{$log->createdAt()}</td>
                            <td>{$log->domain}</td>
                            <td>{$log->ip_address}</td>
                            <td>{$log->directory}</td>
                            <td>{$log->description}</td>
                        </tr>
            ";

            
        }

        $accessLogs .= '
                    </tbody>
                </table>
            </div>
        </div>
        ';

        return[
            'License Buddy'         => $relevantData,
            'Allowed Domains'       => $allowedDomains,
            'Allowed IP Address'    => $allowedIpAddress,
            'Allowed Directory'     => $allowedDirectory,
            'License Status'        => $status,
            'Recent Access Logs'    => $accessLogs,
        ];

    }

    public static function servicePageSave($params) {

        License::modifyLicense($params['serviceid'], [
            'allowed_domains'       => $_POST['licensebuddy']['allowed_domains'],
            'allowed_ip_address'    => $_POST['licensebuddy']['allowed_ip_address'],
            'allowed_directory'     => $_POST['licensebuddy']['allowed_directory'],
            'status'                => $_POST['licensebuddy']['status'],
        ]);

    }

    public static function clientAreaOutput($params) {

        $license        = License::grab($params['serviceid']);
        $allowReissue   = Config::get($params['serviceid'], 'allowReissue') && $license->reissue_count < Config::getMaster('maxReissues');

        $isTrial        = Trial::isTrial($params['serviceid']);
        $trialExpiry    = Trial::expiryDate($params['serviceid']);

        $date           = Carbon::parse($trialExpiry);
        $now            = Carbon::now();
        $trialDays      = $date->diffInDays($now);

        return [

            'tabOverviewReplacementTemplate'   => 'licenseinfo.tpl',
            'templateVariables'                 => [
                'licenseKey'                    => $license->license_key,
                'allowedDomains'                => !empty($license->allowed_domains) ? explode(',', $license->allowed_domains): ['N/A'],
                'allowedIpAddress'              => !empty($license->allowed_ip_address) ? $license->allowed_ip_address : 'N/A',
                'allowedDirectory'              => !empty($license->allowed_directory) ? $license->allowed_directory : 'N/A',
                'status'                        => $license->status,
                'allowReissue'                  => $allowReissue,
                'showCancellationButton'        => Setting::getValue('showCancellationButton'),
                'isTrial'                       => $isTrial == 'Yes' ? true : false,
                'trialExpiryDays'               => $trialDays > 0 ? $trialDays : 'expired',
                'trialExpiryDate'               => $trialExpiry ?: 'N/A',
            ],
        ];

    }

}