<template>
    <div>
        <div class="box" v-if="server">
            <!-- Main container -->

            <h1 class="title is-4">{{ server.name }}</h1>

            <hr>

            <nav class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Accounts</p>
                        <p class="title is-4">{{ accountsCount }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Server Type</p>
                        <p class="title is-4">{{ server.formatted_server_type }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Disk Usage</p>
                        <p v-if="server.disk_percentage" class="title is-4">{{ server.disk_percentage }}%</p>
                        <p v-else class="title is-4">Unknown</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Backups Kept</p>
                        <p v-if="server.backup_retention" class="title is-4">{{ server.backup_retention }}</p>
                        <p v-else class="title is-4">Unknown</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Backup Days</p>
                        <p class="title is-4">{{ server.formatted_backup_days }}</p>
                    </div>
                </div>
            </nav>

        </div>
        <div class="box">
            <!-- Main container -->
            <div>
                <nav class="level mb-3">
                    <!-- Left side -->
                    <div class="level-left">
                        <div class="level-item">

                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="level-right">
                        <p class="level-item"></p>
                        <p class="level-item"></p>
                        <p class="level-item"></p>
                        <p class="level-item"></p>
                    </div>
                </nav>
            </div>

            <table class="table is-narrow is-fullwidth is-aligned-center">
                <thead>
                    <tr class="no-hover">
                        <th>Domain</th>
                        <th v-if="!server">Server</th>
                        <th>Username</th>
                        <th>Backups</th>
                        <th v-if="server">Plan</th>
                        <th><abbr title="Disk Used">Used</abbr> / <abbr title="Disk Limit">Limit</abbr></th>
                        <th><abbr title="Disk Usage">Usage</abbr></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items">
                        <td>
                            <a href="#">{{ item.domain }}</a>
                        </td>
                        <td v-if="!server">
                            <a :href="serverAccountsUrl(item)">{{ item.server.name }}</a>
                        </td>
                        <td>{{ item.user }}</td>
                        <td>
                            <span class="tag is-success is-rounded" v-if="item.backup">Yes</span>
                            <span class="tag is-danger is-rounded" v-if="! item.backup">No</span>
                        </td>
                        <td v-if="server">{{ item.plan }}</td>
                        <td>{{ item.disk_used }} / {{ item.disk_limit }}</td>
                        <td>{{ item.disk_usage }}</td>
                        <td>
                            <div class="field is-grouped is-pulled-right">
                                <p class="control">
                                    <b-dropdown position="is-bottom-left">
                                        <button class="button" slot="trigger">
                                            <span class="icon">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </span>
                                        </button>

                                        <b-dropdown-item has-link>
                                            <a :href="item.domain_url" target="_blank">
                                                <span class="icon is-small">
                                                    <i class="fa fa-globe"></i>
                                                </span>
                                                <span>View Site</span>
                                            </a>
                                        </b-dropdown-item>

                                        <hr class="dropdown-divider">

                                        <b-dropdown-item has-link>
                                            <a :href="item.cpanel_url" target="_blank">
                                                <span class="icon is-small">
                                                    <i class="fa fa-link"></i>
                                                </span>
                                                <span>Access cPanel</span>
                                            </a>
                                        </b-dropdown-item>

                                        <b-dropdown-item has-link>
                                            <a :href="item.whm_url" target="_blank">
                                                <span class="icon is-small">
                                                    <i class="fa fa-link"></i>
                                                </span>
                                                <span>Access Server WHM</span>
                                            </a>
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['data'],

        data() {
            return {
                items: false,
                server: this.data,
            };
        },

        computed: {
            fetchUrl() {
                if (this.server) {
                    return `/api/accounts/${this.server.id}`;
                }

                return '/api/accounts';
            },

            accountsCount() {
                return this.items.length;
            }
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get(this.fetchUrl)
                    .then(response => this.items = response.data);
            },

            serverAccountsUrl(item) {
                return `/accounts/${item.server_id}`;
            }
        }

    }
</script>