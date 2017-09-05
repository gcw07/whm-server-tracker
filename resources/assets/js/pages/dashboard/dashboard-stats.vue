<template>
    <div class="columns">
        <div class="column">
            <div class="box">
                <article class="media">
                    <figure class="media-left">
                        <div class="fa-layers fa-fw" style="font-size: 2em; color: #205081; margin-top: 8px;">
                            <i class="fa fa-circle" data-fa-transform="grow-12"></i>
                            <i class="fa fa-server" style="color: #fff"></i>
                        </div>
                    </figure>
                    <div class="media-content">
                        <p class="title is-5">{{ stats.servers }}</p>
                        <p class="subtitle">Servers</p>
                    </div>
                </article>
            </div>
        </div>
        <div class="column">
            <div class="box">
                <article class="media">
                    <figure class="media-left">
                        <div class="fa-layers fa-fw" style="font-size: 2em; color: #811f00; margin-top: 8px;">
                            <i class="fa fa-circle" data-fa-transform="grow-12"></i>
                            <i class="fa fa-globe" style="color: #fff"></i>
                        </div>
                    </figure>
                    <div class="media-content">
                        <p class="title is-5">{{ stats.accounts }}</p>
                        <p class="subtitle">Accounts</p>
                    </div>
                </article>
            </div>
        </div>
        <div class="column">
            <div class="box">
                <article class="media">
                    <figure class="media-left">
                        <div class="fa-layers fa-fw" style="font-size: 2em; color: #0d8129; margin-top: 8px;">
                            <i class="fa fa-circle" data-fa-transform="grow-12"></i>
                            <i class="fa fa-users" style="color: #fff"></i>
                        </div>
                    </figure>
                    <div class="media-content">
                        <p class="title is-5">{{ stats.users }}</p>
                        <p class="subtitle">Users</p>
                    </div>
                </article>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                stats: false
            };
        },

        mounted() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get('/api/dashboard/stats')
                    .then(response => this.stats = response.data);
            }
        }
    }
</script>