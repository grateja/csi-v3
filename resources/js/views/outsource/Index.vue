<template>
    <v-container>
        <h3 class="title white--text">Out-source accounts</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 primary" @click="addOutSource" round v-if="isOwner">
            <v-icon left>add</v-icon> add account
        </v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.item.abbr}}</td>
                        <td>{{props.item.company_name}}</td>
                        <td>{{props.item.address}}</td>
                        <td>
                            <template v-if="isOwner">
                                <v-btn small @click="edit(props.item)" class="mx-0" round outline>
                                    <v-icon left small>edit</v-icon> edit
                                </v-btn>
                                <v-btn small @click="deleteOutSource(props.item)" class="mx-0" outline :loading="props.item.isDeleting" round>
                                    <v-icon left small>delete</v-icon> delete
                                </v-btn>
                                <v-btn small @click="viewItems(props.item)" class="mx-0" outline round>
                                    view items
                                </v-btn>
                            </template>
                            <v-btn small @click="viewJobOrders(props.item)" class="mx-0" outline round>
                                view job orders
                            </v-btn>
                            <v-btn small @click="viewSOA(props.item)" class="mx-0" outline round>
                                view SOA
                            </v-btn>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <out-source-dialog :outSource="activeOutSource" @save="save" v-model="openOutSourceDialog" />
    </v-container>
</template>

<script>
import OutSourceDialog from './OutSourceDialog.vue';

export default {
    components: {
        OutSourceDialog
    },
    data() {
        return {
            loading: false,
            openOutSourceDialog: false,
            activeOutSource: null,
            items: [],
            headers: [
                {
                    text: 'Abbr',
                    sortable: false
                },
                {
                    text: 'Company name',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    },
    methods: {
        addOutSource() {
            this.activeOutSource = null;
            this.openOutSourceDialog = true;
        },
        edit(item) {
            this.activeOutSource = item;
            this.openOutSourceDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.outSource)
                this.activeOutSource = data.outSource;
            } else {
                this.activeOutSource.abbr = data.outSource.abbr;
                this.activeOutSource.company_name = data.outSource.company_name;
                this.activeOutSource.address = data.outSource.address;
            }
        },
        load() {
            this.loading = true;
            axios.get('/api/out-source').then((res, rej) => {
                console.log("result", res.data.result.data);
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        viewItems(item) {
            this.$router.push(`/out-source/${item.id}/linens`)
        },
        viewJobOrders(item) {
            this.$router.push(`/out-source/${item.id}/job-orders`)
        },
        viewSOA(item) {
            this.$router.push(`/out-source/${item.id}/soa`)
        },
        deleteOutSource(item) {
            if(confirm('Do you really want to delete this account?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('outsource/deleteOutSource', item.id).then((res, rej) => {
                    this.items = this.items.filter(p => p.id != res.data.outSource.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                })
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
