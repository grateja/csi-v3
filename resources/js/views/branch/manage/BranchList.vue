<template>
    <div>
        <h3 class="title grey--text">Manage branches</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="green white--text ml-0" @click="addBranch"><v-icon left>add</v-icon> Clreate new branch</v-btn>

        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" icon="search"></v-text-field>
                </form>
                <v-data-table :items="items" :headers="headers" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>
                            <b v-if="props.item.is_main">{{ props.item.name }}</b>
                            <span v-else>{{ props.item.name }}</span>
                        </td>
                        <td>{{ props.item.address }}</td>
                        <td>{{ props.item.totalMachines }}</td>
                        <td>
                            <v-tooltip top>
                                <v-btn small icon slot="activator" @click="editBranch(props.item)">
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit branch</span>
                            </v-tooltip>
                        </td>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <branch-form-dialog v-model="openBranchDialog" :client="{user_id: 'self'}" :branch="activeBranch" @save="updateBranch"></branch-form-dialog>
    </div>
</template>

<script>
import BranchFormDialog from '../../developer/clients/BranchFormDialog.vue';

export default {
    components: {
        BranchFormDialog
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            loading: false,
            totalPage: 0,
            items: [],
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: 'Number of machines',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ],
            openBranchDialog: false,
            activeBranch: null
        }
    },
    methods: {
        load() {
            if(this.loading) return;

            this.$router.push({
                query: {
                    page: this.page,
                    keyword: this.keyword
                }
            });

            this.loading = true;
            axios.get('/api/search/branches/self', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                }
            }).then((res, rej) => {
                this.loading = false;
                this.items = res.data.result.data;
                this.totalPage = res.data.result.last_page;
            }).catch(err => {
                this.loading = false;
            });
        },
        filter() {
            this.page = 1;
            this.load();
        },
        addBranch() {
            this.activeBranch = null;
            this.openBranchDialog = true;
        },
        editBranch(branch) {
            this.activeBranch = branch;
            this.openBranchDialog = true;
        },
        updateBranch(data) {
            if(data.mode == 'insert') {
                this.items.push(data.branch);
            } else {
                let _branch = this.items.find(b => b.id == data.branch.id);
                _branch.name = data.branch.name;
                _branch.address = data.branch.address;
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
