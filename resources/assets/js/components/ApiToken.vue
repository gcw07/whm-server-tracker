<template>
    <form>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">API Token</p>
            </header>
            <section class="modal-card-body">
                <div class="field">
                    <label class="label" for="token">API Token</label>
                    <div class="control">
                        <input id="token"
                               type="text"
                               class="input"
                               name="token"
                               v-model="form.token"
                               v-focus
                               required>
                    </div>
                    <p class="help">
                       This is a WHM API Token. Once set this will not be visible again.
                    </p>
                    <p class="help is-danger" v-show="form.errors.has('token')">
                        {{ form.errors.get('token') }}
                    </p>
                </div>
            </section>
            <footer class="modal-card-foot modal-card-foot-right">
                <div class="field is-grouped">
                    <p class="control">
                        <button class="button is-light" type="button" @click="$parent.close()">Cancel</button>
                    </p>
                    <p class="control">
                        <button type="submit" class="button is-primary" :class="{'is-loading': form.busy}" @click.prevent="save" :disabled="form.busy">
                            Save Changes
                        </button>
                    </p>
                </div>
            </footer>
        </div>
    </form>
</template>
<script>
    import Form from '../forms/form';

    export default {
        props: ['id'],

        data() {
            return {
                form: new Form({
                    token: ''
                }),

                serverId: this.id,
            };
        },

        methods: {
            save() {
                this.form.put(`/servers/${this.serverId}/token`)
                    .then(response => {
                        this.$parent.close();
                        this.$emit('updated', true);
                    });
            },
        },
    }
</script>