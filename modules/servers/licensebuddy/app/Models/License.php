<?php

namespace LicenseBuddy\Server\Models;

use WHMCS\Carbon;
use WHMCS\CustomField;
use WHMCS\Service\Addon;
use WHMCS\Service\Service;
use WHMCS\Database\Capsule;
use LicenseBuddy\Server\Config\Config;

use LicenseBuddy\Server\Helpers\Trial;
use WHMCS\CustomField\CustomFieldValue;

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

class License extends \WHMCS\Model\AbstractModel {

    protected $table = 'mod_licensebuddy_licenses';

    protected $fillable = ['license_key', 'allowed_domains', 'allowed_directory', 'allowed_ip_address', 'reissue_count', 'status', 'service_id', 'last_accessed'];

    public static function add($licenseKey, $serviceId) {

        self::create([
            'license_key'           => $licenseKey,
            'allowed_domains'       => '',
            'allowed_directory'     => '',
            'allowed_ip_address'    => '',
            'reissue_count'         => '0',
            'status'                => 'Reissued',
            'service_id'            => $serviceId,
        ]);

    }

    public function lastAccessedAt() {
        return ($this->lastAccessed == '0000-00-00 00:00:00') ? 'Never' : Carbon::parse($this->lastAccessed)->format('d/m/Y H:i:s');
    }

    public static function checkExists($serviceId) {
        return self::where('service_id', $serviceId)->count();
    }

    public static function grab($serviceId) {
        return self::where('service_id', $serviceId)->first();
    }

    public static function suspend($serviceId) {
        return self::where('service_id', $serviceId)->update([
            'status' => 'Suspended',
        ]);
    }

    public static function unsuspend($serviceId) {
        return self::where('service_id', $serviceId)->update([
            'status' => 'Active',
        ]);
    }

    public static function terminate($serviceId) {
        return self::where('service_id', $serviceId)->update([
            'status' => 'Expired',
        ]);
    }

    public static function reissue($id) {
        return self::where('id', $id)->increment('reissue_count', 1, ['status' => 'Reissued']);
    }

    public static function resetReissues($id) {
        return self::where('id', $id)->update(['reissue_count' => 0]);
    }

    public static function modifyLicense($serviceId, $data) {
        return self::where('service_id', $serviceId)->update($data);
    }

    public static function setLastAccessed($id) {
        return self::where('id', $id)->update([
            'last_accessed' => date('Y-m-d H:i:s')
        ]);
    }

    public function service() {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public static function getData($id, $token = '') {

        $license = self::where('id', $id)->first();

        $model = Service::with('product', 'client')->find($license->service_id);

        $addons = [];

        $fetchAddons = Addon::where('hostingid', $license->service_id)->get();

        foreach ($fetchAddons as $addon) {
            $addons[] = [
                'id'                    => $addon->addonid,
                'name'                  => $addon->productAddon->name,
                'description'           => $addon->productAddon->description,
                'quantity'              => $addon->qty,
                'firstPaymentAmount'    => $addon->firstpaymentamount,
                'setupFee'              => $addon->setupfee,
                'recurringFee'          => $addon->recurring,
                'billingCycle'          => $addon->billingCycle,
                'isTaxed'               => $addon->tax ? 'true' : 'false',
                'status'                => $addon->status,
                'regDate'               => $addon->regdate,
                'nextDueDate'           => $addon->nextduedate,
                'nextInvoiceDate'       => $addon->nextinvoicedate,
            ];
        }

        $customFields = [];

        $fetchCustomFields = CustomField::where('relid', $model->product->id)->get();

        foreach ($fetchCustomFields as $customField) {
            $customFields[] = [
                'name'          => $customField->fieldname,
                'value'         => CustomFieldValue::where('fieldid', $customField->id)->where('relid', $license->service->id)->value('value'),
            ];
        }

        $configOptions = [];

        $fetchConfigOptions = Capsule::table('tblhostingconfigoptions')->where('relid', $license->service_id)->get();

        foreach ($fetchConfigOptions as $configOption) {

            $configOptions[] = [
                'name'  => Capsule::table('tblproductconfigoptions')->where('id', $configOption->configid)->value('optionname'),
                'value' => Capsule::table('tblproductconfigoptionssub')->where('id', $configOption->optionid)->value('optionname'),
            ];

        }

        $applicationKey = Config::get($license->service_id, 'applicationKey');

        $data = [
            'registeredName'        => $model->client->firstName . ' ' . $model->client->lastName,
            'companyName'           => $model->client->companyName,
            'email'                 => $model->client->email,
            'serviceId'             => $license->service_id,
            'productId'             => $model->packageId,
            'productName'           => $model->product->name,
            'registeredDate'        => $model->registrationDate,
            'nextDueDate'           => $model->nextDueDate,
            'billingCycle'          => $model->billingCycle,
            'allowedDomains'        => $license->allowed_domains,
            'allowedIPAddress'      => $license->allowed_ip_address,
            'allowedDirectory'      => $license->allowed_directory,
            'addons'                => $addons,
            'customFields'          => $customFields,
            'configOptions'         => $configOptions,
            'hash'                  => isset($token) ? hash('sha256', $applicationKey . $token) : '',
            'isTrial'               => Trial::isTrial($license->service_id) == 'Yes' ? 'yes' : 'no',
        ];

        if (Trial::isTrial($license->service_id) == 'Yes') {
            $data['trialExpiry'] = Trial::expiryDate($license->service_id);
        }

        return $data;

    }

}