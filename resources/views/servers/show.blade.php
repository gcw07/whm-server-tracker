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

        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <h1 class="title is-4">{{ $server->name }}</h1>
                </div>
            </div>
            <div class="level-right">
                <div class="level-item">
                    <b-dropdown position="is-bottom-left">
                        <button class="button" slot="trigger">
                            <span class="icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                        </button>

                        <b-dropdown-item>
                            <span class="icon is-small">
                                <i class="fa fa-refresh"></i>
                            </span>
                            <span>Refresh Details</span>
                        </b-dropdown-item>
                        <b-dropdown-item>
                            <span class="icon is-small">
                                <i class="fa fa-refresh"></i>
                            </span>
                            <span>Refresh Accounts</span>
                        </b-dropdown-item>
                        <hr class="dropdown-divider">
                        <b-dropdown-item>
                            <span class="icon is-small">
                                <i class="fa fa-pencil"></i>
                            </span>
                            <span>Edit</span>
                        </b-dropdown-item>
                        <b-dropdown-item>
                            <span class="icon is-small">
                                <i class="fa fa-trash"></i>
                            </span>
                            <span>Remove</span>
                        </b-dropdown-item>
                    </b-dropdown>
                </div>
            </div>
        </nav>

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

        <h3 class="title section-title has-text-centered mt-5 is-uppercase">Disk Details</h3>

        <nav class="level">
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Used</p>
                    <p class="title is-4">{{ $server->formatted_disk_used }}</p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Available</p>
                    <p class="title is-4">{{ $server->formatted_disk_available }}</p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">Disk Total</p>
                    <p class="title is-4">{{ $server->formatted_disk_total }}</p>
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

        <h3 class="title section-title has-text-centered mt-5 is-uppercase">Backup Details</h3>

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
    @if ($server->notes)
    <div class="box">
        <div class="columns">
            <div class="column is-two-thirds">
                <div class="field">
                    <label class="label" for="notes">Notes</label>
                    <div class="control">
                        {{ $server->notes }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection