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
                clearTokenForm: new Form({}),
                form: new Form({
                    name: this.data.name,
                    address: this.data.address,
                    port: this.data.port,
                    server_type: this.data.server_type,
                    notes: this.data.notes
                }),
                isTokenModalActive: false,
                serverData: this.data,
            };
        },

        computed: {
            tokenHasBeenSet() {
                if (! this.serverData.missing_token && this.serverData.server_type != 'reseller') {
                    return true;
                }

                return false;
            }
        },

        methods: {
            save() {
                this.form.preserveForm().put(`/servers/${this.serverData.id}`)
                    .then(response => {
                        this.serverData = response;

                        this.$toast.open({
                            message: 'Changes saved',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            tokenHasBeenSaved() {
                this.serverData.missing_token = false;

                this.$toast.open({
                    message: 'Token saved',
                    type: 'is-success',
                    duration: 4000
                });
            },

            clearToken() {
                this.$dialog.confirm({
                    message: 'Are you sure you want to <strong>clear</strong> the api token? This action can not be undone.',
                    confirmText: 'Clear Token',
                    type: 'is-danger',
                    onConfirm: () => {
                        this.clearTokenForm.delete(`/servers/${this.serverData.id}/token`)
                            .then(response => {
                                this.serverData.missing_token = true;

                                this.$toast.open({
                                    message: 'Token cleared',
                                    type: 'is-success',
                                    duration: 4000
                                });
                            });
                    }
                })
            }
        }

    }
</script>