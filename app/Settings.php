<?php

namespace App;

use Exception;

class Settings
{
    /**
     * The Server instance.
     *
     * @var Server
     */
    protected $server;

    /**
     * The list of settings.
     *
     * @var array
     */
    protected $settings = [];

    /**
     * The allowed list of settings
     *
     * @var array
     */
    protected $allowed = [
        'disk_used', 'disk_available', 'disk_total', 'disk_percentage',
        'backup_enabled', 'backup_days', 'backup_retention'
    ];

    /**
     * Create a new settings instance.
     *
     * @param $settings
     * @param Server $server
     */
    public function __construct(array $settings, Server $server)
    {
        $this->settings = $settings;
        $this->server = $server;
    }

    /**
     * Retrieve the given setting.
     *
     * @param  string $key
     * @return string
     */
    public function get($key)
    {
        return array_get($this->settings, $key);
    }

    /**
     * Create and persist a new setting.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->settings[$key] = $value;

        $this->persist();
    }

    /**
     * Forget the given setting.
     *
     * @param string $key
     * @return mixed
     */
    public function forget($key)
    {
        array_forget($this->settings, $key);

        return $this->persist();
    }

    /**
     * Forget all settings.
     *
     * @return mixed
     */
    public function forgetAll()
    {
        $this->settings = [];

        return $this->persist();
    }

    /**
     * Determine if the given setting exists.
     *
     * @param  string $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->settings);
    }

    /**
     * Retrieve an array of all settings.
     *
     * @return array
     */
    public function all()
    {
        return $this->settings;
    }

    /**
     * Merge the given attributes with the current settings.
     * But do not assign any new settings.
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function merge(array $attributes)
    {
        $this->settings = array_merge(
            $this->settings,
            array_only($attributes, $this->allowed)
        );

        return $this->persist();
    }

    /**
     * Persist the settings.
     *
     * @return mixed
     */
    protected function persist()
    {
        return $this->server->update(['settings' => $this->settings]);
    }

    /**
     * Magic property access for settings.
     *
     * @param string $key
     * @return null|string
     */
    public function __get($key)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        return null;
    }
}
