<template>
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
                    <p class="level-item" v-html="filterText('all')" @click="filterBy('all')"></p>
                    <p class="level-item" v-html="filterText('dedicated')" @click="filterBy('dedicated')"></p>
                    <p class="level-item" v-html="filterText('reseller')" @click="filterBy('reseller')"></p>
                    <p class="level-item" v-html="filterText('vps')" @click="filterBy('vps')"></p>
                    <p class="level-item ml-2">
                        <button class="button" @click="isNewServerModalActive = true">
                            <span class="icon is-small">
                                <i class="fa fa-plus"></i>
                            </span>
                            <span>
                                New
                            </span>
                        </button>
                    </p>
                </div>
            </nav>
        </div>

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
                        <span class="tag is-success is-rounded" v-if="item.backup_enabled">Yes</span>
                        <span class="tag is-danger is-rounded" v-if="! item.backup_enabled">No</span>
                    </td>
                    <td>
                        <span v-if="item.server_type != 'reseller'">{{ item.disk_percentage }}%</span>
                        <span v-else>n/a</span>
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

                                    <b-dropdown-item :disabled="!item.can_refresh_data"
                                                     @click="menuAction('refresh.details', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-refresh"></i>
                                        </span>
                                        <span>Refresh Details</span>
                                    </b-dropdown-item>
                                    <b-dropdown-item :disabled="!item.can_refresh_data"
                                                     @click="menuAction('refresh.accounts', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-refresh"></i>
                                        </span>
                                        <span>Refresh Accounts</span>
                                    </b-dropdown-item>
                                    <hr class="dropdown-divider">
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

        <b-modal :active.sync="isNewServerModalActive" has-modal-card>
            <new-server></new-server>
        </b-modal>
    </div>
</template>
<script>
    import NewServer from '../../components/NewServer'
    import Form from '../../forms/form';

    export default {

        components: {
            NewServer
        },

        data() {
            return {
                deleteForm: new Form({}),
                filterSelected: 'all',
                items: false,
                isNewServerModalActive: false,
                filters: null,
            };
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch() {
                if (this.filterSelected != 'all') {
                    this.filters = {type: this.filterSelected};
                } else {
                    this.filters = null;
                }

                axios.get('/api/servers', {params: this.filters})
                    .then(response => this.items = response.data);
            },

            refreshDetails(item) {
                axios.get(`/servers/${item.id}/fetch-details`)
                    .then(response => {
                        item.disk_percentage = response.data.disk_percentage;
                        item.backup_enabled = response.data.backup_enabled;

                        this.$toast.open({
                            message: 'Server Details Refreshed',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            },

            refreshAccounts(item) {
                axios.get(`/servers/${item.id}/fetch-accounts`)
                    .then(response => {
                        item.accounts_count = response.data.accounts_count;

                        this.$toast.open({
                            message: 'Server Accounts Refreshed',
                            type: 'is-success',
                            duration: 4000
                        });
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

            getFilterText(filter) {
                if (filter == 'all') return 'All';
                if (filter == 'dedicated') return 'Dedicated';
                if (filter == 'reseller') return 'Reseller';
                if (filter == 'vps') return 'VPS';
            },

            filterBy(filter) {
                if (filter == this.filterSelected) return;

                this.filterSelected = filter;
                this.fetch();
            },

            filterText(filter) {
                let text = this.getFilterText(filter);
                if (this.filterSelected == filter) {
                    return `<strong>${text}</strong>`;
                }

                return `<a>${text}</a>`;
            },

            serverAccountsUrl(item) {
                return `/accounts/${item.id}`;
            },

            menuAction(action, item) {
                switch (action) {
                    case 'refresh.details':
                        this.refreshDetails(item);
                        break;

                    case 'refresh.accounts':
                        this.refreshAccounts(item);
                        break;

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
            }
        }

    }
</script>