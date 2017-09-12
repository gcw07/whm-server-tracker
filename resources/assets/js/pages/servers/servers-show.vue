<template>
    <div>
        <div class="box">
            <!-- Main container -->

            <nav class="level">
                <div class="level-left">
                    <div class="level-item">
                        <h1 class="title is-4">{{ serverData.name }}</h1>
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

                            <b-dropdown-item :disabled="!serverData.can_refresh_data"
                                             @click="menuAction('refresh.details')">
                                <span class="icon is-small">
                                    <i class="fa fa-sync"></i>
                                </span>
                                <span>Refresh Details</span>
                            </b-dropdown-item>
                            <b-dropdown-item :disabled="!serverData.can_refresh_data"
                                             @click="menuAction('refresh.accounts')">
                                <span class="icon is-small">
                                    <i class="fa fa-sync"></i>
                                </span>
                                <span>Refresh Accounts</span>
                            </b-dropdown-item>

                            <hr class="dropdown-divider">

                            <b-dropdown-item @click="menuAction('edit')">
                                <span class="icon is-small">
                                    <i class="fa fa-pencil"></i>
                                </span>
                                <span>Edit</span>
                            </b-dropdown-item>
                            <b-dropdown-item @click="menuAction('remove')">
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

            <b-notification type="is-warning" :active="serverData.missing_token" :closable="false" has-icon>
                This server is missing an API token. Please set an API token to fetch the server's data.
            </b-notification>

            <nav class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Accounts</p>
                        <p class="title is-4">{{ serverData.accounts_count }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Server Type</p>
                        <p class="title is-4">{{ serverData.formatted_server_type }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">PHP Version</p>
                        <p class="title is-4">{{ serverData.formatted_php_version }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Address</p>
                        <p class="title is-4">{{ serverData.address }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Port</p>
                        <p class="title is-4">{{ serverData.port }}</p>
                    </div>
                </div>
            </nav>

            <h3 class="title section-title has-text-centered mt-5 is-uppercase">Disk Details</h3>

            <nav class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Disk Used</p>
                        <p class="title is-4">{{ serverData.formatted_disk_used }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Disk Available</p>
                        <p class="title is-4">{{ serverData.formatted_disk_available }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Disk Total</p>
                        <p class="title is-4">{{ serverData.formatted_disk_total }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Disk Usage</p>
                        <p v-if="serverData.settings.disk_percentage" class="title is-4">{{ serverData.settings.disk_percentage }}%</p>
                        <p v-else="" class="title is-4">Unknown</p>
                    </div>
                </div>
            </nav>

            <h3 class="title section-title has-text-centered mt-5 is-uppercase">Backup Details</h3>

            <nav class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Backups</p>
                        <p v-if="serverData.settings.backup_enabled" class="title is-4">Yes</p>
                        <p v-else="" class="title is-4">No</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Backups Kept</p>
                        <p v-if="serverData.settings.backup_retention" class="title is-4">{{ serverData.settings.backup_retention }}</p>
                        <p v-else="" class="title is-4">Unknown</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Backup Days</p>
                        <p class="title is-4">{{ serverData.formatted_backup_days }}</p>
                    </div>
                </div>
            </nav>

            <h3 class="title section-title has-text-centered mt-5 is-uppercase">Updated On</h3>

            <nav class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Details Last Updated</p>
                        <p v-if="serverData.details_last_updated" class="title is-4">
                            {{ serverData.details_last_updated | relative }}
                        </p>
                        <p v-else="" class="title is-4">Unknown</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Accounts Last Updated</p>
                        <p v-if="serverData.accounts_last_updated" class="title is-4">
                            {{ serverData.accounts_last_updated | relative }}
                        </p>
                        <p v-else="" class="title is-4">Unknown</p>
                    </div>
                </div>
            </nav>
        </div>
        <div class="box" v-if="serverData.notes">
            <div class="columns">
                <div class="column is-two-thirds">
                    <div class="field">
                        <label class="label">Notes</label>
                        <div class="control">
                            {{ serverData.notes }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Form from '../../forms/form';

    export default {
        props: ['data'],

        data() {
            return {
                deleteForm: new Form({}),
                serverData: this.data,
            };
        },

        methods: {
            refreshDetails() {
                axios.get(`/servers/${this.serverData.id}/fetch-details`)
                    .then(response => {
                        this.$toast.open({
                            message: response.data.message,
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            refreshAccounts() {
                axios.get(`/servers/${this.serverData.id}/fetch-accounts`)
                    .then(response => {
                        this.$toast.open({
                            message: response.data.message,
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            deleteServer() {
                this.$dialog.confirm({
                    message: 'Are you sure you want to <strong>remove</strong> this server? All accounts associated will also be removed. This action can not be undone.',
                    title: this.serverData.name,
                    confirmText: 'Remove Server',
                    type: 'is-danger',
                    onConfirm: () => {
                        this.deleteForm.delete(`/servers/${this.serverData.id}`)
                            .then(response => {
                                this.$toast.open({
                                    message: 'Server Removed Successfully',
                                    type: 'is-success',
                                    duration: 4000
                                });

                                window.location.href = '/servers';
                            });
                    }
                })
            },

            menuAction(action) {
                switch (action) {
                    case 'refresh.details':
                        this.refreshDetails();
                        break;

                    case 'refresh.accounts':
                        this.refreshAccounts();
                        break;

                    case 'edit':
                        window.location.href = `/servers/${this.serverData.id}/edit`;
                        break;

                    case 'remove':
                        this.deleteServer();
                        break;

                    default:
                        break;
                }
            }
        }

    }
</script>