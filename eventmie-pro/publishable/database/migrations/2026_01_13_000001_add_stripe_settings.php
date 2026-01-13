<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class AddStripeSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = $this->findSetting('apps.stripe_public_key');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Stripe Public Key',
                'value'        => '',
                'details'      => null,
                'type'         => 'text',
                'order'        => 60,
                'group'        => 'Apps',
            ])->save();
        }

        $setting = $this->findSetting('apps.stripe_secret_key');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Stripe Secret Key',
                'value'        => '',
                'details'      => null,
                'type'         => 'password',
                'order'        => 61,
                'group'        => 'Apps',
            ])->save();
        }

        $setting = $this->findSetting('apps.stripe_webhook_secret');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Stripe Webhook Secret',
                'value'        => '',
                'details'      => null,
                'type'         => 'password',
                'order'        => 62,
                'group'        => 'Apps',
            ])->save();
        }

        $setting = $this->findSetting('apps.stripe_mode');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Stripe Mode',
                'value'        => 'sandbox',
                'details'      => json_encode([
                    'default' => 'sandbox',
                    'options' => [
                        'sandbox'    => 'Sandbox',
                        'production' => 'Production',
                    ]
                ]),
                'type'         => 'select_dropdown',
                'order'        => 63,
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
        // Settings are kept to preserve configuration
    }

    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}
