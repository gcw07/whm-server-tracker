<template>
    <div class="box">
        <!-- Main container -->
        <div>
            <nav class="level mb-3">
                <!-- Left side -->
                <div class="level-left">
                    <div class="level-item">

                    </div>
                </div>

                <!-- Right side -->
                <div class="level-right">
                    <p class="level-item">
                        <button class="button" @click="isNewUserModalActive = true">
                            <span class="icon is-small">
                                <i class="fa fa-plus"></i>
                            </span>
                            <span>
                                New
                            </span>
                        </button>
                    </p>
                </div>
            </nav>
        </div>

        <table class="table is-narrow is-fullwidth is-aligned-center">
            <thead>
                <tr class="no-hover">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Last Login</th>
                    <th>Last IP Address</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items">
                    <td>{{ item.name }}</td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.last_login_at | relative | placeholder('Never') }}</td>
                    <td>{{ item.last_login_ip_address }}</td>
                    <td>
                        <div class="field is-grouped is-pulled-right">
                            <p class="control">
                                <b-dropdown position="is-bottom-left">
                                    <button class="button" slot="trigger">
                                        <span class="icon">
                                            <i class="fa fa-ellipsis-h"></i>
                                        </span>
                                    </button>

                                    <b-dropdown-item @click="menuAction('password', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                        <span>Change Password</span>
                                    </b-dropdown-item>
                                    <b-dropdown-item @click="menuAction('edit', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-pencil"></i>
                                        </span>
                                        <span>Edit</span>
                                    </b-dropdown-item>
                                    <b-dropdown-item @click="menuAction('remove', item)">
                                        <span class="icon is-small">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                        <span>Remove</span>
                                    </b-dropdown-item>
                                </b-dropdown>
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <b-modal :active.sync="isNewUserModalActive" :canCancel="false" has-modal-card>
            <new-user @added="addUser"></new-user>
        </b-modal>

        <b-modal :active.sync="isPasswordModalActive" :canCancel="false" @close="closePasswordModal" has-modal-card>
            <change-password :data="currentUser" @updated="passwordSaved"></change-password>
        </b-modal>
    </div>
</template>
<script>
    import NewUser from '../../components/NewUser'
    import ChangePassword from '../../components/ChangePassword'
    import Form from '../../forms/form';

    export default {
        components: {
            NewUser,
            ChangePassword
        },

        data() {
            return {
                items: false,
                isNewUserModalActive: false,
                isPasswordModalActive: false,
                currentUser: null,
                deleteForm: new Form({}),
            };
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch() {
                axios.get('/api/users')
                    .then(response => this.items = response.data);
            },

            addUser(item) {
                this.items.push(item);
            },

            changePassword(item) {
                this.currentUser = item;
                this.isPasswordModalActive = true;
            },

            closePasswordModal() {
                this.currentUser = null;
            },

            passwordSaved() {
                this.$toast.open({
                    message: 'Password Changed',
                    type: 'is-success',
                    duration: 4000
                });
            },

            deleteUser(item) {
                this.$dialog.confirm({
                    message: 'Are you sure you want to <strong>remove</strong> this user? This action can not be undone.',
                    title: item.name,
                    confirmText: 'Remove User',
                    type: 'is-danger',
                    onConfirm: () => {
                        this.deleteForm.delete(`/users/${item.id}`)
                            .then(response => {
                                this.items.splice(this.items.indexOf(item), 1);

                                this.$toast.open({
                                    message: 'User Removed Successfully',
                                    type: 'is-success',
                                    duration: 4000
                                });
                            })
                            .catch(error => {
                                this.$toast.open({
                                    message: error.message,
                                    type: 'is-danger',
                                    duration: 6000
                                });
                            });
                    }
                })
            },

            menuAction(action, item) {
                switch (action) {
                    case 'password':
                        this.changePassword(item);
                        break;

                    case 'edit':
                        window.location.href = `/users/${item.id}/edit`;
                        break;

                    case 'remove':
                        this.deleteUser(item);
                        break;

                    default:
                        break;
                }
            },
        }

    }
</script>