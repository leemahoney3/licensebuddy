<?php

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

require_once __DIR__ . '/vendor/autoload.php';


add_hook('ServiceDelete', 1, [\LicenseBuddy\Addon\Hooks\Service::class, 'deleteLicenseOnServiceDeletion']);

add_hook('DailyCronJob', 1, [\LicenseBuddy\Addon\Hooks\Cron::class, 'pruneAccessLogs']);
add_hook('DailyCronJob', 2, [\LicenseBuddy\Addon\Hooks\Cron::class, 'syncLicenseStatusWithParentServiceStatus']);