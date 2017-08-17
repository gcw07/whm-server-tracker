<template>
    <form>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">New Server</p>
            </header>
            <section class="modal-card-body">
                <div class="field">
                    <label class="label" for="name">Name</label>
                    <div class="control">
                        <input id="name"
                               type="text"
                               class="input"
                               :class="{'is-danger': form.errors.has('name')}"
                               name="name"
                               placeholder="Server Name"
                               v-model="form.name"
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('name')">
                        {{ form.errors.get('name') }}
                    </p>
                </div>
                <div class="field">
                    <label class="label" for="address">Address</label>
                    <div class="control">
                        <input id="address"
                               type="text"
                               class="input"
                               :class="{'is-danger': form.errors.has('address')}"
                               name="address"
                               placeholder="ex. 192.100.1.10"
                               v-model="form.address"
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('address')">
                        {{ form.errors.get('address') }}
                    </p>
                </div>
                <div class="field">
                    <label class="label" for="port">Port</label>
                    <div class="control">
                        <input id="port"
                               type="text"
                               class="input"
                               :class="{'is-danger': form.errors.has('port')}"
                               name="port"
                               placeholder="ex. 2087"
                               v-model="form.port"
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('port')">
                        {{ form.errors.get('port') }}
                    </p>
                </div>
                <div class="field">
                    <label class="label" for="server_type">Type</label>
                    <div class="control">
                        <div class="select" :class="{'is-danger': form.errors.has('server_type')}">
                            <select id="server_type" name="server_type" v-model="form.server_type">
                                <option value="">Select Type</option>
                                <option value=""></option>
                                <option value="dedicated">Dedicated</option>
                                <option value="reseller">Reseller</option>
                                <option value="vps">VPS</option>
                            </select>
                        </div>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('server_type')">
                        {{ form.errors.get('server_type') }}
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
        data() {
            return {
                form: new Form({
                    name: '',
                    address: '',
                    port: '',
                    server_type: ''
                })
            };
        },

        methods: {
            save() {
                this.form.post('/servers')
                    .then(response => {
                        this.$parent.close();
                        window.location.href = `/servers/${response.id}/edit`;
                    });
            },

        },
    }
</script>