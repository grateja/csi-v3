<template>
    <v-container>
        <h3 class="title white--text">Out source SOA</h3>
        <v-divider class="my-3"></v-divider>

        <v-layout justify-center>
            <v-flex style="max-width: 500px">
                <v-text-field class="ml-1 translucent-input round-input" label="Search soa number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
            </v-flex>
        </v-layout>

        <v-btn class="success ml-0 my-3" round @click="generateSOA"><v-icon left>add</v-icon> Generate SOA</v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.index + 1}}</td>
                        <td>{{ moment(props.item.created_at).format('LL') }}</td>
                        <td>{{ moment(props.item.updated_at).format('LL') }}</td>
                        <td>
                            {{ props.item.soa_number }}
                        </td>
                        <td v-if="props.item.total_amount == null">(No items)</td>
                        <td v-else>P {{ parseFloat(props.item.total_amount).toFixed(2) }}</td>
                        <td class="text-xs-right" v-if="isOwner">
                            <v-btn icon small @click="editSOA(props.item)" outline>
                                <v-icon small>edit</v-icon>
                            </v-btn>
                            <v-btn icon small @click="deleteSOA(props.item)" outline :loading="props.item.isDeleting">
                                <v-icon small>delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
                <template slot="footer">
                    <tr>
                        <td colspan="10">
                            <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{total}}</span> result(s)</div>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
    </v-container>
</template>
<script>

export default {
    data() {
        return {
            cancelSource: null,
            keyword: null,
            openSOA: false,
            soa: null,
            total: 0,
            page: 1,
            reset: false,
            items: [],
            loading: false,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Date created',
                    sortable: false
                },
                {
                    text: 'Date revised',
                    sortable: false
                },
                {
                    text: 'SOA number',
                    sortable: false
                },
                {
                    text: 'Total Amount',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.reset = true;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get(`/api/out-source/soa/${this.$route.params.outSourceId}`, {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.reset = false;
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                }
                this.total = res.data.result.total;
            }).finally(() => {
                this.loading = false;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.load();
        },
        editSOA(soa) {
            this.$router.push(`/out-source/soa/${this.$route.params.outSourceId}/${soa.id}`);
        },
        generateSOA() {
            this.$router.push(`/out-source/soa/${this.$route.params.outSourceId}/new`);
        },
        previewSOA(item) {
            this.soa = item
            this.openSOA = true;
        },
        // save(data) {
        //     if(data.mode == 'insert') {
        //         this.items.push(data.soa);
        //         this.activeJobOrder = data.soa;
        //     } else {
        //         this.activeJobOrder.date = data.soa.date;
        //         this.activeJobOrder.remarks = data.soa.remarks;
        //         this.activeJobOrder.amount = data.soa.amount;
        //         this.activeJobOrder.staff_name = data.soa.staff_name;
        //     }
        // },
        deleteSOA($event, item) {
            if(confirm('Delete this SOA?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('outsourcesoa/deleteSOA', item.id).then((res, rej) => {
                    this.items = this.items.filter(i => i.id != item.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        }
    },
    created() {
        this.load();
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    }
}
</script>
