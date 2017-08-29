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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Last Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items">
                    <td>{{ item.name }}</td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.updated_at | date }}</td>
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
</template>
<script>
    export default {
        data() {
            return {
                items: false,
            };
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get('/api/users')
                    .then(response => this.items = response.data);
            }
        }

    }
</script>