<?php

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

require __DIR__ . '/vendor/autoload.php';

add_hook('ClientAreaPage', 1, [\LicenseBuddy\Server\Verify\Verify::class, 'handleVerification']);
add_hook('DailyCronJob', 1, [\LicenseBuddy\Server\Helpers\Trial::class, 'checkTrialLicenseExpiredAndSuspend']);