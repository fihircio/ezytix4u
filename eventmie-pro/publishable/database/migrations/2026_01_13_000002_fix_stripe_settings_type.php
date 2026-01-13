<?php

use Illuminate\Database\Migrations\Migration;
use TCG\Voyager\Models\Setting;

class FixStripeSettingsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = Setting::whereIn('key', ['apps.stripe_secret_key', 'apps.stripe_webhook_secret'])->get();

        foreach ($settings as $setting) {
            $setting->type = 'text';
            $setting->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $settings = Setting::whereIn('key', ['apps.stripe_secret_key', 'apps.stripe_webhook_secret'])->get();

        foreach ($settings as $setting) {
            $setting->type = 'password';
            $setting->save();
        }
    }
}
