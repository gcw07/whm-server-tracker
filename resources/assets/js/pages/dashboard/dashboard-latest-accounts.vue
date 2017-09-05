<template>
    <div class="column">
        <div class="box">
            <p class="title is-5">Latest Accounts</p>

            <table class="table is-fullwidth is-narrow">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items">
                        <td>{{ item.domain }}</td>
                        <td>{{ item.created_at | relative }}</td>
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
                items: false
            };
        },

        mounted() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get('/api/dashboard/latest-accounts')
                    .then(response => this.items = response.data);
            }
        }
    }
</script>