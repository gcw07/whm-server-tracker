<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;

class ProcessWhmVersion
{
    public function execute(Server $server, $data)
    {
        $version = $data['data']['version'];

        $server->settings->put('whm_version', $version);

        $server->save();
    }
}
