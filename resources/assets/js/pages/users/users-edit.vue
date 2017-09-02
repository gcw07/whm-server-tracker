<template>
    <div class="box">
        <!-- Main container -->

        <h1 class="title is-4">{{ userData.name }}</h1>

        <hr>

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
                                   placeholder="Full Name"
                                   v-model="form.name"
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

    </div>
</template>
<script>
    import Form from '../../forms/form';

    export default {
        props: ['data'],

        data() {
            return {
                form: new Form({
                    name: this.data.name,
                    email: this.data.email
                }),
                userData: this.data,
            };
        },

        methods: {
            save() {
                this.form.preserveForm().put(`/users/${this.userData.id}`)
                    .then(response => {
                        this.userData = response;

                        this.$toast.open({
                            message: 'Changes saved',
                            type: 'is-success',
                            duration: 4000
                        });
                    });
            }
        }

    }
</script>