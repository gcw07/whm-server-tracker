<template>
    <div class="box">
        <h1 class="title is-4">Accounts</h1>

        <hr class="mb-2">

        <table class="table is-narrow is-fullwidth is-aligned-center">
            <thead>
                <tr class="no-hover">
                    <th>Domain</th>
                    <th>Server</th>
                    <th>Username</th>
                    <th>Backups</th>
                    <th><abbr title="Disk Used">Used</abbr> / <abbr title="Disk Limit">Limit</abbr></th>
                    <th><abbr title="Disk Usage">Usage</abbr></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :class="{'suspended': item.suspended}">
                    <td>
                        <a href="#">{{ item.domain }}</a>
                    </td>
                    <td>
                        <a :href="serverAccountsUrl(item)">{{ item.server.name }}</a>
                    </td>
                    <td>{{ item.user }}</td>
                    <td>
                        <span class="tag is-success is-rounded" v-if="item.backup">Yes</span>
                        <span class="tag is-danger is-rounded" v-if="! item.backup">No</span>
                    </td>
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

        <div v-if="items.length === 0" class="has-text-centered">
            There are no matching entries
        </div>
    </div>
</template>
<script>
    export default {
        props: ['data'],

        data() {
            return {
                items: this.data,
            };
        },

        methods: {
            serverAccountsUrl(item) {
                return `/accounts/${item.server_id}`;
            }
        }

    }
</script>