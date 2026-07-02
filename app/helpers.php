<?php

if (!function_exists('email_logo_url')) {
    /**
     * Get the public URL for the email logo
     * Logo is hosted on DigitalOcean Spaces (S3) for email client compatibility
     */
    function email_logo_url(): string
    {
        return 'https://statutoria-monitoring-bucket.sgp1.digitaloceanspaces.com/assets/bki-logo.png';
    }
}

if (!function_exists('file_disk')) {
    /**
     * Get the configured filesystem disk for permanent file storage.
     * Uses Laravel's default filesystem disk (FILESYSTEM_DISK in .env).
     */
    function file_disk(): string
    {
        return config('filesystems.default', 'local');
    }
}
