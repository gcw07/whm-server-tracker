<?php

namespace Tests\Factories;

use App\Services\WHM\WhmAccountDetails;

class WhmAccountDetailsFake extends WhmAccountDetails
{
    public function fetch(): void
    {
        $this->requestSucceeded('sslVhosts', $this->getSslVhostsData());
        $this->requestSucceeded('phpVhostVersions', $this->getPhpVhostVersionsData());
    }

    protected function getPhpVhostVersionsData(): array
    {
        return [
            'data' => [
                'versions' => [
                    ['vhost' => 'my-site.com', 'version' => 'ea-php81'],
                    ['vhost' => 'super-system.com', 'version' => 'ea-php82'],
                ],
            ],
        ];
    }

    protected function getSslVhostsData(): array
    {
        return [
            'data' => [
                'vhosts' => [
                    [
                        'user' => 'mysite',
                        'servername' => 'my-site.com',
                        'type' => 'main',
                        'domains' => ['my-site.com', 'www.my-site.com'],
                        'crt' => [
                            'not_after' => 1893456000,
                            'domains' => ['my-site.com', 'www.my-site.com'],
                            'issuer.organizationName' => "Let's Encrypt",
                        ],
                    ],
                    [
                        'user' => 'mysite',
                        'servername' => 'sub.my-site.com',
                        'type' => 'sub',
                        'domains' => ['sub.my-site.com'],
                        'crt' => [
                            'not_after' => 1893456000,
                            'domains' => ['sub.my-site.com'],
                            'issuer.organizationName' => "Let's Encrypt",
                        ],
                    ],
                    [
                        'user' => 'super',
                        'servername' => 'super-system.com',
                        'type' => 'main',
                        'domains' => ['super-system.com', 'www.super-system.com'],
                        'crt' => [
                            'not_after' => 1893456000,
                            'domains' => ['super-system.com'],
                            'issuer.organizationName' => "Let's Encrypt",
                        ],
                    ],
                ],
            ],
        ];
    }
}
