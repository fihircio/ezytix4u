<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class AddFilesystemSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // FILESYSTEM_DRIVER
        $setting = $this->findSetting('filesystem.driver');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Filesystem Driver',
                'value'        => 'local',
                'details'      => json_encode([
                    'default' => 'local',
                    'options' => [
                        'local' => 'Local',
                        's3'    => 'AWS S3'
                    ]
                ]),
                'type'         => 'select_dropdown',
                'order'        => 1,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_ACCESS_KEY_ID
        $setting = $this->findSetting('filesystem.aws_access_key_id');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'AWS Access Key ID',
                'value'        => '',
                'details'      => json_encode([
                    'description' => 'Your AWS IAM user access key ID for S3 access'
                ]),
                'type'         => 'text',
                'order'        => 2,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_SECRET_ACCESS_KEY
        $setting = $this->findSetting('filesystem.aws_secret_access_key');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'AWS Secret Access Key',
                'value'        => '',
                'details'      => json_encode([
                    'description' => 'Your AWS IAM user secret access key for S3 access'
                ]),
                'type'         => 'text',
                'order'        => 3,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_DEFAULT_REGION
        $setting = $this->findSetting('filesystem.aws_default_region');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'AWS Default Region',
                'value'        => 'us-east-1',
                'details'      => json_encode([
                    'description' => 'AWS region where your S3 bucket is located (e.g., us-east-1, eu-west-1)'
                ]),
                'type'         => 'text',
                'order'        => 4,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_BUCKET
        $setting = $this->findSetting('filesystem.aws_bucket');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'AWS Bucket',
                'value'        => '',
                'details'      => json_encode([
                    'description' => 'Name of your S3 bucket'
                ]),
                'type'         => 'text',
                'order'        => 5,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_URL
        $setting = $this->findSetting('filesystem.aws_url');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'AWS URL',
                'value'        => '',
                'details'      => json_encode([
                    'description' => 'Custom URL for S3 bucket (optional, leave empty for default)'
                ]),
                'type'         => 'text',
                'order'        => 6,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_ENDPOINT
        $setting = $this->findSetting('filesystem.aws_endpoint');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'AWS Endpoint',
                'value'        => '',
                'details'      => json_encode([
                    'description' => 'Custom endpoint URL (optional, for S3-compatible services)'
                ]),
                'type'         => 'text',
                'order'        => 7,
                'group'        => 'Filesystem',
            ])->save();
        }

        // AWS_USE_PATH_STYLE_ENDPOINT
        $setting = $this->findSetting('filesystem.aws_use_path_style_endpoint');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Use Path Style Endpoint',
                'value'        => '0',
                'details'      => json_encode([
                    'description' => 'Use path-style S3 URLs instead of virtual-hosted-style'
                ]),
                'type'         => 'checkbox',
                'order'        => 8,
                'group'        => 'Filesystem',
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
        // Remove the settings
        Setting::where('key', 'like', 'filesystem.%')->delete();
    }

    /**
     * Find or create a setting.
     *
     * @param string $key
     * @return \TCG\Voyager\Models\Setting
     */
    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}