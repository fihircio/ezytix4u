<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class AddChipinSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = $this->findSetting('apps.chipin_brand_id');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Chipin Brand ID',
                'value'        => '',
                'details'      => null,
                'type'         => 'text',
                'order'        => 50,
                'group'        => 'Apps',
            ])->save();
        }

        $setting = $this->findSetting('apps.chipin_api_key');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Chipin API Key',
                'value'        => '',
                'details'      => null,
                'type'         => 'password',
                'order'        => 51,
                'group'        => 'Apps',
            ])->save();
        }

        $setting = $this->findSetting('apps.chipin_environment');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Chipin Environment',
                'value'        => 'sandbox',
                'details'      => json_encode([
                    'default' => 'sandbox',
                    'options' => [
                        'sandbox'    => 'Sandbox',
                        'production' => 'Production',
                    ]
                ]),
                'type'         => 'select_dropdown',
                'order'        => 52,
                'group'        => 'Apps',
            ])->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optional: Remove settings if rolling back
        // But usually settings are kept even after rollback to preserve configuration
    }

    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}
