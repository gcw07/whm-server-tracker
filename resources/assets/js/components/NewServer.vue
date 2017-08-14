<template>
    <form method="POST">
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">New Server</p>
            </header>
            <section class="modal-card-body">
                {{ form }}<br>
                {{ form.errors.hasErrors() }}<br>
                {{ form.errors.has('name') }}
                <div class="field">
                    <label class="label" for="name">Name</label>
                    <p class="control">
                        <input id="name"
                               type="text"
                               class="input"
                               :class="{'is-danger': form.errors.has('name')}"
                               name="name"
                               placeholder="Server Name"
                               v-model="form.name"
                               required>
                    <p class="help is-danger" v-show="form.errors.has('name')">
                        {{ form.errors.get('name') }}
                    </p>
                </div>
            </section>
            <footer class="modal-card-foot">
                <button class="button" type="button" @click="$parent.close()">Close</button>
                <button type="submit" class="button is-primary" :class="{'is-loading': form.busy}" @click.prevent="save" :disabled="form.busy">
                    Save Changes
                </button>
            </footer>
        </div>
    </form>
</template>
<script>
    export default {
        data() {
            return {
                form: new LaravelForm({
                    name: '',
                    address: '',
                })
            };
        },

        mounted() {
            console.log(this.form);
        },

        methods: {
            save() {
                console.log('save');
                Laravel.post('/servers', this.form)
                    .then(response => {
                        console.log('compete');
                    });
            },

        },
    }
</script>