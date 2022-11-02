<?php

namespace LicenseBuddy\Server\Models;

use WHMCS\Carbon;

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

class Log extends \WHMCS\Model\AbstractModel {

    protected $table = 'mod_licensebuddy_logs';

    protected $fillable = ['description', 'license_id', 'domain', 'ip_address', 'directory'];

    public function createdAt() {
        return Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
    }

    public function updatedAt() {
        return Carbon::parse($this->updated_at)->format('d/m/Y H:i:s');
    }

}