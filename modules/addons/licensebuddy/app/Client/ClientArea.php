<?php

namespace LicenseBuddy\Addon\Client;

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

class ClientArea {

    public static function output($vars) {
        
        if (!Config::get('clientVerifyTool')) {
            return [];
        }

        $valid      = false;
        $message    = null;
        $domain     = trim($_POST['domain']);
        

        if ($_GET['action'] == 'verify') {

            if (empty($domain)) {

                $valid      = false;
                $message    = 'Please enter a domain name';

            } else {

                $fetch = License::where('allowed_domains', 'like', '%' . $domain . '%')->orWhere('allowed_ip_address', 'like', '%' . $domain . '%')->first();
                
                if ($fetch && ($fetch->status == 'Active' || $fetch->status = 'Reissued')) {
                    $valid      = true;
                    $message    = 'This domain is authorized to use our software';
                } else {
                    $valid      = false;
                    $message    = 'This domain is not authorized to use our software';
                }

            }

            die(json_encode([
                'domain'    => $domain,
                'valid'     => $valid,
                'message'   => $message,
            ]));
        
        }

        return [
            'pagetitle'     => 'Verify License',
            'breadcrumb'    => ['index.php?m=licensebuddy' => 'Verify License'],
            'templatefile'  => 'verifylicense',
            'requirelogin'  => false,
        ];

    }

}