<template>
    <form>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Change Password for {{ userData.name }}</p>
            </header>
            <section class="modal-card-body">
                <div class="field">
                    <label class="label" for="password">New Password</label>
                    <div class="control">
                        <input id="password"
                               type="password"
                               class="input"
                               :class="{'is-danger': form.errors.has('password')}"
                               name="password"
                               v-model="form.password"
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('password')">
                        {{ form.errors.get('password') }}
                    </p>
                </div>
                <div class="field">
                    <label class="label" for="password_confirmation">Confirm New Password</label>
                    <div class="control">
                        <input id="password_confirmation"
                               type="password"
                               class="input"
                               :class="{'is-danger': form.errors.has('password_confirmation')}"
                               name="password_confirmation"
                               v-model="form.password_confirmation"
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('password_confirmation')">
                        {{ form.errors.get('password_confirmation') }}
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
        props: ['data'],

        data() {
            return {
                form: new Form({
                    password: '',
                    password_confirmation: ''
                }),

                userData: this.data,
            };
        },

        methods: {
            save() {
                this.form.put(`/users/${this.userData.id}/change-password`)
                    .then(response => {
                        this.$parent.close();
                        this.$emit('updated', true);
                    });
            },
        },
    }
</script>