<template>
    <div class="column is-one-third">
        <div class="box">
            <p class="title is-5">Servers</p>

            <table class="table is-fullwidth is-narrow">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Dedicated</td>
                        <td>{{ servers.dedicated }}</td>
                    </tr>
                    <tr>
                        <td>Reseller</td>
                        <td>{{ servers.reseller }}</td>
                    </tr>
                    <tr>
                        <td>VPS</td>
                        <td>{{ servers.vps }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                servers: false
            };
        },

        mounted() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get('/api/dashboard/servers')
                    .then(response => this.servers = response.data);
            }
        }
    }
</script>