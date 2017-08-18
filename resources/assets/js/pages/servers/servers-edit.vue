<template>
    <div class="box">
        <!-- Main container -->

        <h1 class="title is-5">{{ serverData.name }}</h1>

        <hr>

        <b-notification type="is-warning" :active="serverData.missing_token" :closable="false" has-icon>
            This server is missing an API token. Please set an API token to fetch the server's data.
            <p class="mt-1">
                <button class="button" @click="isTokenModalActive = true">
                    Set API Token
                </button>
            </p>
        </b-notification>

        <div class="columns">
            <div class="column is-two-thirds">

                <form>
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
                    <div class="field" v-show="tokenHasBeenSet">
                        <label class="label">API Token</label>
                        <div class="control">
                            <span style="line-height: 27px;">Token Set</span>
                            <button class="button is-small is-warning ml-1">Clear Token</button>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="notes">Notes</label>
                        <div class="control">
                            <textarea id="notes"
                                      class="textarea"
                                      :class="{'is-danger': form.errors.has('notes')}"
                                      name="notes"
                                      v-model="form.notes"
                                      required>
                              </textarea>
                        </div>
                        <p class="help is-danger" v-show="form.errors.has('notes')">
                            {{ form.errors.get('notes') }}
                        </p>
                    </div>
                    <div class="field is-grouped is-grouped-right mt-2">
                        <div class="control">
                            <button type="submit" class="button is-primary" :class="{'is-loading': form.busy}" @click.prevent="save" :disabled="form.busy">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <b-modal :active.sync="isTokenModalActive" has-modal-card>
            <api-token :id="serverData.id" @updated="savedToken"></api-token>
        </b-modal>

    </div>
</template>
<script>
    import Form from '../../forms/form';
    import ApiToken from '../../components/ApiToken'

    export default {
        props: ['data'],

        components: {
            ApiToken
        },

        data() {
            return {
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
                            duration: 5000
                        });
                    });
            },

            savedToken() {
                this.serverData.missing_token = false;

                this.$toast.open({
                    message: 'Token saved',
                    type: 'is-success',
                    duration: 5000
                });
            }
        }

    }
</script>