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
                    <p class="level-item"><strong>All</strong></p>
                    <p class="level-item"><a>Dedicated</a></p>
                    <p class="level-item"><a>Reseller</a></p>
                    <p class="level-item"><a>VPS</a></p>
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
            <tfoot>
                <tr class="no-hover">
                    <td colspan="6">
                        <!-- Pagination -->
                        <nav class="pagination mt-1">
                            <a class="pagination-previous" title="This is the first page" disabled>Previous</a>
                            <a class="pagination-next">Next page</a>
                            <ul class="pagination-list">
                                <li>
                                    <a class="pagination-link is-current">1</a>
                                </li>
                                <li>
                                    <a class="pagination-link">2</a>
                                </li>
                                <li>
                                    <a class="pagination-link">3</a>
                                </li>
                            </ul>
                        </nav>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <tr v-for="item in items">
                    <td>
                        <a href="#">{{ item.name }}</a>
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
                                        <span>View</span>
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

    export default {

        components: {
            NewServer
        },

        data() {
            return {
                items: false,
                isNewServerModalActive: false,
            };
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get('/api/servers')
                    .then(response => this.items = response.data);
            },

            menuAction(action, item) {
                switch (action) {
                    case 'refresh.details':
                        alert('refresh details')
                        break;

                    case 'refresh.accounts':
                        break;

                    case 'view':
                        window.location.href = `/servers/${item.id}`;
                        break;

                    case 'edit':
                        window.location.href = `/servers/${item.id}/edit`;
                        break;

                    case 'remove':
                        break;

                    default:
                        break;
                }
            }
        }

    }
</script>