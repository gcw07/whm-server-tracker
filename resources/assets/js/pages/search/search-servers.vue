<template>
    <div class="box">
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <h1 class="title is-4">Servers</h1>
                </div>
            </div>

            <div class="level-right">
                <div class="level-item">
                    <h2 class="subtitle is-6" v-if="items.length > 0">{{ items.length }} results</h2>
                </div>
            </div>
        </nav>

        <hr class="mb-2">

        <table class="table is-narrow is-fullwidth is-aligned-center">
            <thead>
                <tr class="no-hover">
                    <th>Server</th>
                    <th>Type</th>
                    <th>Accounts</th>
                    <th>Backups</th>
                    <th><abbr title="Disk Usage">Usage</abbr></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items">
                    <td>
                        <a :href="serverAccountsUrl(item)">{{ item.name }}</a>
                        <span class="tag is-warning" v-if="item.missing_token">
                            <span class="icon is-small">
                                <i class="fa fa-exclamation"></i>
                            </span>
                            <span>No Token</span>
                        </span>
                    </td>
                    <td>{{ item.formatted_server_type }}</td>
                    <td>{{ item.accounts_count }}</td>
                    <td>
                        <span v-if="item.settings.backup_enabled" class="tag is-success is-rounded">Yes</span>
                        <span v-else="" class="tag is-danger is-rounded">No</span>
                    </td>
                    <td>
                        <span v-if="item.server_type != 'reseller'">{{ item.settings.disk_percentage }}%</span>
                        <span v-else="">n/a</span>
                    </td>
                    <td>
                        <div class="field is-grouped is-pulled-right">
                            <p class="control">
                                <b-tooltip label="WHM Link">
                                    <a :href="item.whm_url" class="button" target="_blank">
                                        <span class="icon"><i class="fa fa-external-link"></i></span>
                                    </a>
                                </b-tooltip>
                            </p>
                            <p class="control">
                                <b-dropdown position="is-bottom-left">
                                    <button class="button" slot="trigger">
                                        <span class="icon">
                                            <i class="fa fa-ellipsis-h"></i>
                                        </span>
                                    </button>

                                    <b-dropdown-item @click="menuAction('view', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                        <span>View Details</span>
                                    </b-dropdown-item>
                                    <b-dropdown-item @click="menuAction('edit', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-pencil"></i>
                                        </span>
                                        <span>Edit</span>
                                    </b-dropdown-item>
                                    <b-dropdown-item @click="menuAction('remove', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                        <span>Remove</span>
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
    import Form from '../../forms/form';

    export default {
        props: ['data'],

        data() {
            return {
                items: this.data,
                deleteForm: new Form({}),
            };
        },

        mounted() {
            this.listen();
        },

        methods: {
            listen() {
                Echo.private('server-update')
                    .listen('FetchedServerDetails', (e) => {
                        this.updateServerDetails(e.server);
                    })
                    .listen('FetchedServerAccounts', (e) => {
                        this.updateServerAccounts(e.server);
                    });
            },

            deleteServer(item) {
                this.$dialog.confirm({
                    message: 'Are you sure you want to <strong>remove</strong> this server? All accounts associated will also be removed. This action can not be undone.',
                    title: item.name,
                    confirmText: 'Remove Server',
                    type: 'is-danger',
                    onConfirm: () => {
                        this.deleteForm.delete(`/servers/${item.id}`)
                            .then(response => {
                                this.items.splice(this.items.indexOf(item), 1);

                                this.$toast.open({
                                    message: 'Server Removed Successfully',
                                    type: 'is-success',
                                    duration: 4000
                                });
                            });
                    }
                })
            },

            serverAccountsUrl(item) {
                return `/accounts/${item.id}`;
            },

            menuAction(action, item) {
                switch (action) {
                    case 'view':
                        window.location.href = `/servers/${item.id}`;
                        break;

                    case 'edit':
                        window.location.href = `/servers/${item.id}/edit`;
                        break;

                    case 'remove':
                        this.deleteServer(item);
                        break;

                    default:
                        break;
                }
            },

            updateServerAccounts(server) {
                let i = this.items.map(item => item.id).indexOf(server.id);
                let currentItem = this.items[i];

                if (currentItem === undefined) {
                    return;
                }

                currentItem.accounts_count = server.accounts_count;
            },

            updateServerDetails(server) {
                let i = this.items.map(item => item.id).indexOf(server.id);
                let currentItem = this.items[i];

                if (currentItem === undefined) {
                    return;
                }

                if (currentItem.settings.length === 0) {
                    currentItem.settings = {
                        disk_percentage: server.settings.disk_percentage,
                        backup_enabled: server.settings.backup_enabled,
                    };
                } else {
                    currentItem.settings.disk_percentage = server.settings.disk_percentage;
                    currentItem.settings.backup_enabled = server.settings.backup_enabled;
                }
            },
        }

    }
</script>