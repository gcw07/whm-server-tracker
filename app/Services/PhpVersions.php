<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PhpVersions
{
    public static function all(): Collection
    {
        return collect([
            'php54' => [
                'name' => 'PHP 5.4',
                'version' => '5.4',
                'status' => 'ended',
                'releaseDate' => '2012-03-01',
                'endDate' => '2015-12-31',
            ],
            'php55' => [
                'name' => 'PHP 5.5',
                'version' => '5.5',
                'status' => 'ended',
                'releaseDate' => '2013-06-20',
                'endDate' => '2016-12-31',
            ],
            'php56' => [
                'name' => 'PHP 5.6',
                'version' => '5.6',
                'status' => 'ended',
                'releaseDate' => '2014-08-28',
                'endDate' => '2017-12-31',
            ],
            'php70' => [
                'name' => 'PHP 7.0',
                'version' => '7.0',
                'status' => 'ended',
                'releaseDate' => '2015-12-03',
                'endDate' => '2018-12-31',
            ],
            'php71' => [
                'name' => 'PHP 7.1',
                'version' => '7.1',
                'status' => 'ended',
                'releaseDate' => '2016-12-01',
                'endDate' => '2019-12-31',
            ],
            'php72' => [
                'name' => 'PHP 7.2',
                'version' => '7.2',
                'status' => 'ended',
                'releaseDate' => '2017-11-30',
                'endDate' => '2021-12-31',
            ],
            'php73' => [
                'name' => 'PHP 7.3',
                'version' => '7.3',
                'status' => 'ended',
                'releaseDate' => '2018-12-06',
                'endDate' => '2022-12-31',
            ],
            'php74' => [
                'name' => 'PHP 7.4',
                'version' => '7.4',
                'status' => 'ended',
                'releaseDate' => '2019-11-28',
                'endDate' => '2023-12-31',
            ],
            'php80' => [
                'name' => 'PHP 8.0',
                'version' => '8.0',
                'status' => 'ended',
                'releaseDate' => '2020-11-26',
                'endDate' => '2024-12-31',
            ],
            'php81' => [
                'name' => 'PHP 8.1',
                'version' => '8.1',
                'status' => 'security',
                'releaseDate' => '2021-11-25',
                'endDate' => '2025-12-31',
            ],
            'php82' => [
                'name' => 'PHP 8.2',
                'version' => '8.2',
                'status' => 'security',
                'releaseDate' => '2022-12-08',
                'endDate' => '2026-12-31',
            ],
            'php83' => [
                'name' => 'PHP 8.3',
                'version' => '8.3',
                'status' => 'active',
                'releaseDate' => '2023-11-23',
                'endDate' => '2027-12-31',
            ],
            'php84' => [
                'name' => 'PHP 8.4',
                'version' => '8.4',
                'status' => 'active',
                'releaseDate' => '2024-11-21',
                'endDate' => '2028-12-31',
            ],
            'php85' => [
                'name' => 'PHP 8.5',
                'version' => '8.5',
                'status' => 'active',
                'releaseDate' => '2025-11-20',
                'endDate' => '2029-12-31',
            ],
        ]);
    }

    public static function filtered($field): Collection
    {
        return self::all()->map(fn ($version) => $version[$field]);
    }

    public static function active($field = null): Collection
    {
        return self::all()
            ->filter(fn ($version) => $version['status'] === 'active')
            ->when($field, function ($collection) use ($field) {
                return $collection->map(fn ($version) => $version[$field]);
            });
    }

    public static function security($field = null): Collection
    {
        return self::all()
            ->filter(fn ($version) => $version['status'] === 'security')
            ->when($field, function ($collection) use ($field) {
                return $collection->map(fn ($version) => $version[$field]);
            });
    }

    public static function endOfLife($field = null): Collection
    {
        return self::all()
            ->filter(fn ($version) => $version['status'] === 'ended')
            ->when($field, function ($collection) use ($field) {
                return $collection->map(fn ($version) => $version[$field]);
            });
    }

    public static function outdated($field = null): Collection
    {
        return self::all()
            ->filter(fn ($version) => $version['status'] === 'security' || $version['status'] === 'ended')
            ->when($field, function ($collection) use ($field) {
                return $collection->map(fn ($version) => $version[$field]);
            });
    }
}
