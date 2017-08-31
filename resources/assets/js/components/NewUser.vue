<template>
    <form>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">New User</p>
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
                               placeholder="Full Name"
                               v-model="form.name"
                               v-focus
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('name')">
                        {{ form.errors.get('name') }}
                    </p>
                </div>
                <div class="field">
                    <label class="label" for="email">Email</label>
                    <div class="control">
                        <input id="email"
                               type="email"
                               class="input"
                               :class="{'is-danger': form.errors.has('email')}"
                               name="email"
                               placeholder="john@example.com"
                               v-model="form.email"
                               required>
                    </div>
                    <p class="help is-danger" v-show="form.errors.has('email')">
                        {{ form.errors.get('email') }}
                    </p>
                </div>
                <div class="field">
                    <label class="label" for="password">Password</label>
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
                    <label class="label" for="password_confirmation">Confirm Password</label>
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
        data() {
            return {
                form: new Form({
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                })
            };
        },

        methods: {
            save() {
                this.form.post('/users')
                    .then(response => {
                        this.$emit('added', response);
                        this.$parent.close();
                    });
            },
        },
    }
</script>