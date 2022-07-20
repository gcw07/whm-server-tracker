<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;

class ProcessPhpInstalledVersions
{
    public function execute(Server $server, $data)
    {
        $versions = $data['data']['versions'];

        $server->settings->put('php_installed_versions', $versions);

        $server->save();
    }
}
