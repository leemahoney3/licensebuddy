<?php

namespace LicenseBuddy\Addon\Admin;

use WHMCS\Carbon;
use WHMCS\User\Client;
use WHMCS\Config\Setting;
use WHMCS\Product\Product;

use LicenseBuddy\Addon\Models\Log;
use LicenseBuddy\Addon\Models\Block;
use LicenseBuddy\Addon\Models\License;
use LicenseBuddy\Addon\Helpers\RedirectHelper;
use LicenseBuddy\Addon\Helpers\AdminPageHelper;
use LicenseBuddy\Addon\Helpers\PaginationHelper;

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

class Admin {

    public static function output($vars) {

        $passThru = [
            'moduleLink'    => $vars['modulelink'],
            'systemURL'     => Setting::getValue('SystemURL'),
            'alerts'        => [],
            'formData'      => [],
        ];

        if (AdminPageHelper::getCurrentPage() == 'dashboard') {

            $passThru['licenses'] = [
                'all'               => License::all(),
                'active'            => License::where('status', 'Active')->get(),
                'reissued'          => License::where('status', 'Reissued')->get(),
                'suspended'         => License::where('status', 'Suspended')->get(),
                'expired'           => License::where('status', 'Expired')->get(),
                'last_accessed'     => License::where('last_accessed', '>=', Carbon::now()->subDays(30))->get(),
                'recent'            => License::orderBy('created_at', 'DESC')->limit(13)->get(),
                'popular'           => License::select('service_id')->groupBy('service_id')->orderByRaw('COUNT(*) DESC')->limit(2)->get(),
            ];

            $passThru['logs']   = [
                'recent'    => Log::orderBy('created_at', 'DESC')->limit(5)->get(),
            ];

            $passThru['blocks'] = [
                'recent'    => Block::orderBy('created_at', 'DESC')->limit(5)->get(),
            ];

            $passThru['search'] = [
                'products'  => Product::where('servertype', 'licensebuddy')->get(),
                'clients'   => Client::orderBy('id', 'desc')->get(),
            ];

        }

        if (AdminPageHelper::getCurrentPage() == 'licenses') {

            $passThru['searchForm'] = [
                'products'  => Product::where('servertype', 'licensebuddy')->get(),
                'clients'   => Client::orderBy('id', 'desc')->get(),
            ];

            $type = 'All';

            if (AdminPageHelper::getAttribute('status')) {

                switch (AdminPageHelper::getAttribute('status')) {

                    case 'active': 
                        $type       = 'Active';
                        $licenses   = License::where('status', 'Active')->orderBy('id', 'DESC')->get();
                        break;
                    
                    case 'reissued': 
                        $type       = 'Reissued';
                        $licenses   = License::where('status', 'Reissued')->orderBy('id', 'DESC')->get();
                        break;

                    case 'suspended': 
                        $type       = 'Suspended';
                        $licenses   = License::where('status', 'Suspended')->orderBy('id', 'DESC')->get();
                        break;

                    case 'expired': 
                        $type       = 'Expired';
                        $licenses   = License::where('status', 'Expired')->orderBy('id', 'DESC')->get();
                        break;
                    
                    default:
                        $type       = 'All';
                        $licenses   = License::orderBy('id', 'DESC')->get();
                        break;  

                }

            }

            $where  = [];
            $search = false;

            if ($_REQUEST['product']) {
                $search     = true;
                $where[]    = ['packageid', '=', (int) $_REQUEST['product']];
            }

            if ($_REQUEST['license_key']) {
                $search     = true;
                $where[]    = ['license_key', 'like', '%' . trim($_REQUEST['license_key']) . '%'];
            }

            if ($_REQUEST['domain']) {
                $search     = true;
                $where[]    = ['allowed_domains', 'like', '%' . trim($_REQUEST['domain']) . '%'];
            }

            if ($_REQUEST['directory']) {
                $search     = true;
                $where[]    = ['allowed_directory', 'like', '%' . trim($_REQUEST['directory']) . '%'];
            }

            if ($_REQUEST['ip_address']) {
                $search     = true;
                $where[]    = ['allowed_ip_address', 'like', '%' . trim($_REQUEST['ip_address']) . '%'];
            }

            if ($_REQUEST['status']) {
                $search     = true;
                $where[]    = ['status', '=', $_REQUEST['status']];
            }

            if($_REQUEST['client']) {
                $search     = true;
                $where[]    = ['userid', '=', $_REQUEST['client']];
            }

            $licenses = new PaginationHelper('p', $where, 10, License::class, ['created_at', 'desc'], [['tblhosting', 'tblhosting.id', '=', 'mod_licensebuddy_licenses.service_id']], 'mod_licensebuddy_licenses.*');

            $passThru['search']     = $search;
            $passThru['type']       = $type;
            $passThru['request']    = $_REQUEST;
            $passThru['licenses']   = [
                'data'  => $licenses->data(),
                'links' => $licenses->links(),
            ];

        }

        if (AdminPageHelper::getCurrentPage() == 'logs') {

            $passThru['searchForm'] = [
                'licenses'  => License::orderBy('id', 'desc')->get(),
            ];

            $where  = [];
            $search = false;

            if ($_REQUEST['license_key']) {
                $search     = true;
                $where[]    = ['license_id', '=', (int) $_REQUEST['license_key']];
            }

            if ($_REQUEST['domain']) {
                $search     = true;
                $where[]    = ['domain', 'like', '%' . trim($_REQUEST['domain']) . '%'];
            }

            if ($_REQUEST['directory']) {
                $search     = true;
                $where[]    = ['directory', 'like', '%' . trim($_REQUEST['directory']) . '%'];
            }

            if ($_REQUEST['ip_address']) {
                $search     = true;
                $where[]    = ['ip_address', 'like', '%' . trim($_REQUEST['ip_address']) . '%'];
            }

            if ($_REQUEST['description']) {
                $search     = true;
                $where[]    = ['description', 'like', '%' . $_REQUEST['description'] . '%'];
            }

            $logs = new PaginationHelper('p', $where, 10, Log::class, ['created_at', 'desc']);

            $passThru['search']     = $search;
            $passThru['request']    = $_REQUEST;
            $passThru['logs']       = [
                'data'  => $logs->data(),
                'links' => $logs->links(),
            ];


        }

        if (AdminPageHelper::getCurrentPage() == 'blocks') {

            if(AdminPageHelper::getAttribute('action') == 'remove' && is_numeric(AdminPageHelper::getAttribute('id'))) {

                Block::where('id', AdminPageHelper::getAttribute('id'))->delete();
                
                RedirectHelper::page('blocks', ['success' => 'true']);

            }

            if($_REQUEST['save'] && trim($_REQUEST['add_domain_ip']) && trim($_REQUEST['add_reason'])) {

                Block::create([
                    'domain_ip' => trim($_REQUEST['add_domain_ip']),
                    'reason'    => trim($_REQUEST['add_reason'])
                ]);

                RedirectHelper::page('blocks', ['success' => 'true']);
            
            }

            $where  = [];
            $search = false;

            if ($_REQUEST['domain_ip']) {
                $search     = true;
                $where[]    = ['domain_ip', 'like', '%' . trim($_REQUEST['domain_ip']) . '%'];
            }

            if ($_REQUEST['reason']) {
                $search     = true;
                $where[]    = ['reason', 'like', '%' . trim($_REQUEST['reason']) . '%'];
            }

            $blocks = new PaginationHelper('p', $where, 10, Block::class, ['created_at', 'desc']);

            $passThru['search']     = $search;
            $passThru['request']    = $_REQUEST;
            $passThru['blocks']     = [
                'data'  => $blocks->data(),
                'links' => $blocks->links(),
            ];

        }

        if (AdminPageHelper::getCurrentPage() == 'manage') {
            
            $license = License::where('id', AdminPageHelper::getAttribute('id'))->first();

            if (!$license) {
                RedirectHelper::page('dashboard');
            }

            if ($_REQUEST['save']) {
                
                License::where('id', $license->id)->update([
                    'allowed_domains'       => $_REQUEST['allowed_domains'],
                    'allowed_directory'     => $_REQUEST['allowed_directory'],
                    'allowed_ip_address'    => $_REQUEST['allowed_ip_address'],
                    'reissue_count'         => (int) $_REQUEST['reissue_count'] ?: $license->reissue_count,
                    'status'                => $_REQUEST['status'],
                ]);

                RedirectHelper::page('manage', ['id' => $license->id, 'success' => 'true']);

            }

            $passThru['license'] = $license;
            $passThru['logs']    = Log::where('license_id', $license->id)->orderBy('created_at', 'desc')->limit(10)->get();

        }

        AdminPageHelper::outputPage($passThru);

    }
    
}