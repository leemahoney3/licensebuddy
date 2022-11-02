<?php

namespace LicenseBuddy\Addon;

use LicenseBuddy\Addon\Database\Database;

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

class App {

    public static function activate() {

        if ($result = Database::createTables()) {
            return [
                'status'        => 'success',
                'description'   => 'Module activated successfully',
            ];
        }

        return [
            'status'        => 'error',
            'description'   => $result,
        ];

    }

    public static function deactivate() {

        if ($result = Database::deleteTables()) {
            return [
                'status'        => 'success',
                'description'   => 'Module deactivated successfully',
            ];
        }

        return [
            'status'        => 'error',
            'description'   => $result,
        ];

    }

}