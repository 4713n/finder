<?php

namespace Link000\Finder\Drivers;

use Illuminate\Support\Collection;

class DriverRegistry {
    protected $drivers;

    public function __construct() {
        $this->drivers = new Collection();
    }

    public function registerDriver(string $driverName, callable $driver) {
        $this->drivers->put($driverName, $driver);
    }

    public function getDriver(string $driverName) {
        return $this->drivers->get($driverName);
    }

    public function getRegisteredDrivers() {
        return $this->drivers->keys();
    }
}