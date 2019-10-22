<template>
    <v-card>
        <v-card-text>
            <form @submit.prevent="filter">
                <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
            </form>
            <v-data-table :items="items" :loading="isLoading" hide-actions :headers="headers">
                <template v-slot:items="props">
                    <td>{{props.item.user.fullname}}</td>
                    <td>{{props.item.user.contact_number}}</td>
                    <td>{{props.item.user.email}}</td>
                    <td>{{props.item.user.address}}</td>
                    <td>{{props.item.branches_count}}</td>
                    <td>
                        <v-tooltip top>
                            <v-btn icon small @click="edit(props.item)" slot="activator">
                                <v-icon small>edit</v-icon>
                            </v-btn>
                            <span>Edit client details</span>
                        </v-tooltip>

                        <v-tooltip top>
                            <v-btn icon small @click="addBranch(props.item)" slot="activator">
                                <v-icon small>add</v-icon>
                            </v-btn>
                            <span>Add new branch</span>
                        </v-tooltip>

                        <v-tooltip top>
                            <v-btn icon small @click="deleteClient(props.item)" slot="activator" :loading="props.item.isDeleting">
                                <v-icon small>delete</v-icon>
                            </v-btn>
                            <span>Delete client</span>
                        </v-tooltip>

                        <v-tooltip top>
                            <v-btn icon small :to="`/developer/clients/by-branches?clientId=${props.item.user_id}`" slot="activator">
                                <v-icon small>timeline</v-icon>
                            </v-btn>
                            <span>View all branches</span>
                        </v-tooltip>
                    </td>
                </template>
            </v-data-table>
        </v-card-text>
        <client-form-dialog v-model="openClientFormDialog" :client="activeItem" @save="update"></client-form-dialog>
        <branch-form-dialog v-model="openBranchDialog" :client="activeItem" @save="addBranchContinue"></branch-form-dialog>
    </v-card>
</template>

<script>
import ClientFormDialog from './ClientFormDialog.vue';
import BranchFormDialog from './BranchFormDialog.vue';

export default {
    components: {
        ClientFormDialog,
        BranchFormDialog
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            isLoading: false,
            items: [],
            openClientFormDialog: false,
            openBranchDialog: false,
            activeItem: null,
            headers: [
                {
                    text: 'Owner name',
                    sortable: false
                },
                {
                    text: 'Contact number',
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
                    text: 'Branches',
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
        load() {
            if(this.isLoading) return;

            this.$router.push({
                path: '/developer/clients/by-clients',
                query:{
                    keyword: this.keyword,
                    page: this.page
                }
            });


            this.isLoading = true;
            axios.get('/api/search/clients/by-clients', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                }
            }).then((res, rej) => {
                console.log(res);
                this.isLoading = false;
                this.items = res.data.result.data;
            }).catch(err => {
                this.isLoading = false;
            })
        },
        filter() {
            this.page = 1;
            this.load();
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        edit(item) {
            this.activeItem = item;
            this.openClientFormDialog = true;
        },
        update(item) {
            let _item = this.items.find(i => i.user_id == item.id);
            _item.user.fullname = item.lastname + ' ' + item.firstname;
            _item.user.contact_number = item.contact_number;
            _item.user.email = item.email;
            _item.user.address = item.address;
            _item.user.barangay = item.barangay;
            _item.user.city_municipality = item.city_municipality;
        },
        addBranch(item) {
            this.activeItem = item;
            this.openBranchDialog = true;
        },
        addBranchContinue(item) {
            let _item = this.items.find(i => i.user_id == item.branch.client_id);

            console.log('item', _item);

            _item.branches_count += 1;
        },
        deleteClient(client) {
            console.log(client);
            if(confirm('Delete this client?')) {
                Vue.set(this.items.find(c => c.user_id == client.user_id), 'isDeleting', true);
                this.$store.dispatch('client/deleteClient', client.user_id).then((res, rej) => {
                    this.items = this.items.filter(c => c.user_id != client.user_id);
                });
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
