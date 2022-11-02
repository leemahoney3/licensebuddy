<?php

namespace LicenseBuddy\Addon\Models;

use WHMCS\Carbon;

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

class Block extends \WHMCS\Model\AbstractModel {

    protected $table = 'mod_licensebuddy_blocks';

    protected $fillable = ['domain_ip', 'reason'];

    public function createdAt() {
        return Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
    }

    public function updatedAt() {
        return Carbon::parse($this->updated_at)->format('d/m/Y H:i:s');
    }

}