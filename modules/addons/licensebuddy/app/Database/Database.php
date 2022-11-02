<?php

namespace LicenseBuddy\Addon\Database;

use WHMCS\Database\Capsule;
use LicenseBuddy\Addon\Config\Config;

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

class Database {

    public static function createTables() {

        # Licenses Table
        if (!Capsule::schema()->hasTable('mod_licensebuddy_licenses')) {
            Capsule::schema()->create('mod_licensebuddy_licenses', function ($table) {
                $table->increments('id');
                $table->text('license_key');
                $table->text('allowed_domains');
                $table->text('allowed_directory');
                $table->text('allowed_ip_address');
                $table->integer('reissue_count');
                $table->enum('status', ['Active', 'Reissued', 'Suspended', 'Expired']);
                $table->integer('service_id');
                $table->datetime('last_accessed')->default('0000-00-00 00:00:00');
                $table->timestamps();
            });
        }

        # Logs Table
        if (!Capsule::schema()->hasTable('mod_licensebuddy_logs')) {
            Capsule::schema()->create('mod_licensebuddy_logs', function ($table) {
                $table->increments('id');
                $table->text('description');
                $table->integer('license_id');
                $table->text('domain');
                $table->text('directory');
                $table->text('ip_address');
                $table->timestamps();
            });
        }

        # License Blocks Table
        if (!Capsule::schema()->hasTable('mod_licensebuddy_blocks')) {
            Capsule::schema()->create('mod_licensebuddy_blocks', function ($table) {
                $table->increments('id');
                $table->text('domain_ip');
                $table->text('reason');
                $table->timestamps();
            });
        }

        return true;

    }

    public static function deleteTables() {

        if (Config::get('deleteTables')) {
            Capsule::schema()->dropIfExists('mod_licensebuddy_licenses');
            Capsule::schema()->dropIfExists('mod_licensebuddy_logs');
            Capsule::schema()->dropIfExists('mod_licensebuddy_blocks');
        }

        return true;

    }

}