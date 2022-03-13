<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;

class ProcessDiskUsage
{
    public function execute(Server $server, $data)
    {
        $diskUsage = $this->findPrimaryPartition($data['data']['partition']);

        $server->settings->merge([
            'disk_used' => $diskUsage['used'],
            'disk_available' => $diskUsage['available'],
            'disk_total' => $diskUsage['total'],
            'disk_percentage' => $diskUsage['percentage'],
        ]);

        $server->save();
    }

    private function findPrimaryPartition($partitions): array
    {
        if (sizeof($partitions) > 1) {
            return collect($partitions)->firstWhere('mount', '/');
        }

        return $partitions[0];
    }
}
