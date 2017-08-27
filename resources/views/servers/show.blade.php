@extends('layouts.master', ['menu' => 'servers'])

@section('title', 'Servers')

@section('content')

    <div>
        <div class="breadcrumb has-arrow-separator" aria-label="breadcrumbs">
            <ul>
                <li><a href="{{ route('servers.index') }}">Servers</a></li>
                <li class="is-active">
                    <a href="#">{{ $server->name }}</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="box">
        <!-- Main container -->

        <h1 class="title is-4">{{ $server->name }}</h1>

        <hr>

        <nav class="level">
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Accounts</p>
                    <p class="title is-4">{{ $server->accounts_count }}</p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Server Type</p>
                    <p class="title is-4">{{ $server->formatted_server_type }}</p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Address</p>
                    <p class="title is-4">{{ $server->address }}</p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Port</p>
                    <p class="title is-4">{{ $server->port }}</p>
                </div>
            </div>
        </nav>

        <h3 class="title is-5 has-text-centered mt-5 is-uppercase" style="color: #656565;">Disk Details</h3>

        <hr class="mb-2">

        <nav class="level">
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Used</p>
                    @if ($server->settings()->disk_used)
                        <p class="title is-4">{{ $server->settings()->disk_used }}</p>
                    @else
                        <p class="title is-4">Unknown</p>
                    @endif
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Available</p>
                    @if ($server->settings()->disk_available)
                        <p class="title is-4">{{ $server->settings()->disk_available }}</p>
                    @else
                        <p class="title is-4">Unknown</p>
                    @endif
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Total</p>
                    @if ($server->settings()->disk_total)
                        <p class="title is-4">{{ $server->settings()->disk_total }}</p>
                    @else
                        <p class="title is-4">Unknown</p>
                    @endif
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Usage</p>
                    @if ($server->settings()->disk_percentage)
                        <p class="title is-4">{{ $server->settings()->disk_percentage }}%</p>
                    @else
                        <p class="title is-4">Unknown</p>
                    @endif
                </div>
            </div>
        </nav>

        <h3 class="title is-5 has-text-centered mt-5 is-uppercase" style="color: #656565;">Backup Details</h3>

        <hr class="mb-2">

        <nav class="level">
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Backups</p>
                    @if ($server->settings()->backup_enabled)
                        <p class="title is-4">Yes</p>
                    @else
                        <p class="title is-4">No</p>
                    @endif
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Backups Kept</p>
                    @if ($server->settings()->backup_retention)
                        <p class="title is-4">{{ $server->settings()->backup_retention }}</p>
                    @else
                        <p class="title is-4">Unknown</p>
                    @endif
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Backup Days</p>
                    <p class="title is-4">{{ $server->formatted_backup_days }}</p>
                </div>
            </div>
        </nav>
    </div>
    <div class="box">
        <!-- Main container -->
        <div class="columns">
            <div class="column is-two-thirds">
                <form>
                    <div class="field" v-show="tokenHasBeenSet">
                        <label class="label">API Token</label>
                        <div class="control">
                            <span style="line-height: 27px;">
                                &bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="notes">Notes</label>
                        <div class="control">
                            {{ $server->notes }}
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection