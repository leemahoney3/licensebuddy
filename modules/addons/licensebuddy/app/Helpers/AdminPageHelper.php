<?php

namespace LicenseBuddy\Addon\Helpers;

use WHMCS\Config\Setting;
use LicenseBuddy\Addon\Helpers\RedirectHelper;
use LicenseBuddy\Addon\Helpers\TemplateHelper;

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

class AdminPageHelper {
    
    protected static $pages = [
        [
            'name'      => 'Dashboard',
            'slug'      => 'dashboard',
            'icon'      => '<i class="fas fa-home"></i>',
            'dropdown'  => false,
            'showInNav' => true,
        ],
        [
            'name'      => 'Licenses',
            'slug'      => 'licenses',
            'icon'      => '<i class="far fa-key"></i>',
            'extraNavActive' => 'manage',
            'dropdown'  => true,
            'links'     => [
                'All Licenses'          => '',
                'Active Licenses'       => '&status=active',
                'Reissued Licenses'     => '&status=reissued',
                'Suspended Licenses'    => '&status=suspended',
                'Expired Licenses'      => '&status=expired',
            ],
            'showInNav' => true,
        ],
        [
            'name'      => 'Access Logs',
            'slug'      => 'logs',
            'icon'      => '<i class="far fa-list-alt"></i>',
            'dropdown'  => false,
            'showInNav' => true,
        ],
        [
            'name'      => 'Blocked Licenses',
            'slug'      => 'blocks',
            'icon'      => '<i class="far fa-do-not-enter"></i>',
            'dropdown'  => false,
            'showInNav' => true,
        ],
        [
            'name'      => 'Manage License',
            'slug'      => 'manage',
            'icon'      => '',
            'dropdown'  => false,
            'showInNav' => false,
        ]
    ];

    public static function pageExists($page) {
        return (self::getAttribute($page) && !empty(self::getAttribute($page)) && in_array(self::getCurrentPage(), array_column(self::getAllPages(), 'slug'))) ? true : false;
    }

    public static function getPage($page, $args) {
        TemplateHelper::getTemplate($page, $args);
    }

    public static function getPageInfo($page, $property) {

        foreach (self::getAllPages() as $thePage) {
            if ($thePage['slug'] == $page) {
                return $thePage[$property];
            }
        }
        
        return null;
    }

    public static function getAllPages($nav = false) {

        $pages = [];

        if ($nav) {
            foreach (self::$pages as $page) {
                if ($page['showInNav']) {
                    $pages[] = $page;
                }
            }
        } else {
            $pages = self::$pages;
        }

        return $pages;

    }

    public static function getCurrentPage() {
        return (!empty(self::getAttribute('page'))) ? self::getAttribute('page') : 'none';
    }

    public static function getCurrentPageName() {

        foreach (self::getAllPages() as $page) {

            if (self::getCurrentPage() != $page['slug']) {
                continue;
            }

            return $page['name'];

        }

        return null;

    }

    public static function outputPage($args) {

        $args['allPages']           = self::getAllPages();
        $args['allNavPages']        = self::getAllPages(true);
        $args['currentPage']        = self::getCurrentPage();
        $args['currentPageName']    = self::getCurrentPageName();
        $args['systemURL']          = Setting::getValue('SystemURL');

        if (in_array(self::getCurrentPage(), array_column(self::getAllPages(), 'slug'))) {

            foreach (self::getAllPages() as $page) {

                if (self::getCurrentPage() != $page['slug']) {
                    continue;
                }

                self::getPage("admin.{$page['slug']}", $args);

            }

        } else {
            RedirectHelper::page('dashboard');
        }

    }

    public static function getAction() {
        return self::getAttribute('action');
    }

    public static function getAttribute($attr) {
        return $_GET[$attr];
    }

}