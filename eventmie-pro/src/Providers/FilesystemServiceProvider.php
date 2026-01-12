<?php

namespace Classiebit\Eventmie\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Override filesystem config with admin settings
        if (Schema::hasTable('settings')) {
            // Set default filesystem disk
            $driver = Setting::where('key', 'filesystem.driver')->value('value') ?: 'local';
            config(['filesystems.default' => $driver]);
            config(['voyager.storage.disk' => $driver]);

            // Override S3 config
            if ($driver === 's3') {
                $s3Config = [
                    'filesystems.disks.s3.key' => Setting::where('key', 'filesystem.aws_access_key_id')->value('value'),
                    'filesystems.disks.s3.secret' => Setting::where('key', 'filesystem.aws_secret_access_key')->value('value'),
                    'filesystems.disks.s3.region' => Setting::where('key', 'filesystem.aws_default_region')->value('value') ?: 'us-east-1',
                    'filesystems.disks.s3.bucket' => Setting::where('key', 'filesystem.aws_bucket')->value('value'),
                    'filesystems.disks.s3.url' => Setting::where('key', 'filesystem.aws_url')->value('value'),
                    'filesystems.disks.s3.endpoint' => Setting::where('key', 'filesystem.aws_endpoint')->value('value'),
                    'filesystems.disks.s3.use_path_style_endpoint' => (bool) Setting::where('key', 'filesystem.aws_use_path_style_endpoint')->value('value'),
                    'filesystems.disks.s3.acl' => null, // Disable ACL since bucket has ACLs disabled
                ];

                // Apply each config setting individually
                foreach ($s3Config as $key => $value) {
                    config([$key => $value]);
                }

                // Debug: Log that we're setting ACL to null
                \Log::info('FilesystemServiceProvider: Set S3 ACL to null');
            }
        }
    }
}