<?php

namespace LicenseBuddy\Addon\Models;

use WHMCS\Carbon;
use WHMCS\Service\Service;

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

class License extends \WHMCS\Model\AbstractModel {

    protected $table = 'mod_licensebuddy_licenses';

    public function service() {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function getClient() {
        return $this->service->client;
    }

    public function lastAccessedAt() {
        return $this->last_accessed == '0000-00-00 00:00:00' ? 'Never' : Carbon::parse($this->last_accessed)->format('d/m/Y H:i:s');
    }

    public function createdAt() {
        return Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
    }

    public function updatedAt() {
        return Carbon::parse($this->updated_at)->format('d/m/Y H:i:s');
    }

    public function statusLabels() {

        switch ($this->status) {

            case 'Active':
                return '<span class="label label-success">Active</span>';
                break;

            case 'Reissued':
                return '<span class="label label-info">Reissued</span>';
                break;

            case 'Suspended':
                return '<span class="label label-warning">Suspended</span>';
                break;

            case 'Expired':
                return '<span class="label label-danger">Expired</span>';
                break;

        }

    }

}