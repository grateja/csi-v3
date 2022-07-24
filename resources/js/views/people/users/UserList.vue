<template>
    <div>
        <h4 class="title grey--text">Users</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn class="green ml-0 white--text" to="/people/users/add">
            <v-icon small left>add</v-icon>
            Create new user
        </v-btn>
        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>

                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{ props.item.fullname }}</td>
                        <td>{{ props.item.contact_number }}</td>
                        <td>{{ props.item.email }}</td>
                        <td>{{ props.item.address }}</td>
                        <td>{{ props.item.roles[0] }}</td>
                        <td>
                            <template v-if="isAdmin">
                                <v-tooltip top v-if="props.item.roles[0] != 'admin'">
                                    <v-btn small icon slot="activator" @click="assignRole(props.item)">
                                        <v-icon small>assignment_ind</v-icon>
                                    </v-btn>
                                    <span>Assign role</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <v-btn small icon slot="activator" :to="`/people/users/${props.item.id}/edit`">
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <span>Edit details</span>
                                </v-tooltip>
                            </template>
                        </td>
                    </template>
                </v-data-table>

                <v-divider class="my-2"></v-divider>

                <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
            </v-card-text>
        </v-card>
        <assign-role-dialog v-model="openAssignRole" :user="activeUser" @save="assignContinue"></assign-role-dialog>
    </div>
</template>

<script>
import AssignRoleDialog from './AssignRoleDialog.vue';

export default {
    components: {
        AssignRoleDialog
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeUser: null,
            openAssignRole: false,
            headers: [
                {
                    text: 'Full name',
                    sortable: false
                },
                {
                    text: 'Contact No.',
                    sortable: false
                },
                {
                    text: 'Email',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: 'Role',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.load();
        },
        load() {
            if(this.loading) return;

            this.$router.push({
                query: {
                    keyword: this.keyword,
                    page: this.page
                }
            });

            this.loading = true;

            axios.get('/api/search/users/self', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        assignRole(user) {
            this.activeUser = user;
            this.openAssignRole = true;
        },
        assignContinue(data) {
            console.log('data', data);
            let user = this.items.find(u => u.id == data.user.id);
            user.roles = data.user.roles;
            user.branches = data.user.branches;
        }
    },
    computed: {
        isAdmin() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
