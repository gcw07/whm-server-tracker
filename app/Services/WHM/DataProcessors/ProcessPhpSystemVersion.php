<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;

class ProcessPhpSystemVersion
{
    public function execute(Server $server, $data)
    {
        $version = $data['data']['version'];

        $server->settings->put('php_system_version', $version);

        $server->save();
    }
}
