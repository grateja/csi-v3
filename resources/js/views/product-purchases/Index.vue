<template>
    <v-container>
        <h3 class="title grey--text">Product purchases</h3>
        <v-divider class="my-3"></v-divider>

        <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search products" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['product_name', 'date', 'remarks', 'receipt']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>

        <v-btn class="success ml-0 my-3" round @click="addNewPurchase"><v-icon left>add</v-icon> Add new purchase</v-btn>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{props.index + 1}}</td>
                <td>{{ moment(props.item.date).format('LL') }}</td>
                <td>{{ props.item.product_name }}</td>
                <td>{{ props.item.quantity }}</td>
                <td>P{{ parseFloat(props.item.unit_cost * props.item.quantity).toFixed(2) }}</td>
                <td>{{ props.item.remarks }}</td>
                <td>{{ props.item.staff_name }}</td>
                <td v-if="isOwner">
                    <v-btn small icon @click="deletePurchase(props.item)" :loading="props.item.isDeleting">
                        <v-icon>delete</v-icon>
                    </v-btn>
                </td>
            </template>
        </v-data-table>
        <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
        <purchase-dialog v-model="openPurchaseDialog" :productPurchase="activeProductPurchase" @save="save" />
    </v-container>
</template>
<script>
import PurchaseDialog from './PurchaseDialog.vue';

export default {
    components: {
        PurchaseDialog
    },
    data() {
        return {
            activeProductPurchase: null,
            openPurchaseDialog: false,
            cancelSource: null,
            keyword: null,
            sortBy: 'date',
            orderBy: 'desc',
            date: null,
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
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Product name',
                    sortable: false
                },
                {
                    text: 'Quantity',
                    sortable: false
                },
                {
                    text: 'Total cost',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'Staff name',
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
            axios.get('/api/product-purchases', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    date: this.date,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.reset = false;
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
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
        addNewPurchase() {
            this.activeProductPurchase = null;
            this.openPurchaseDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.productPurchase);
                this.activeProductPurchase = data.productPurchase;
            } else {
                this.activeProductPurchase.date = data.productPurchase.date;
                this.activeProductPurchase.product_name = data.productPurchase.product_name;
                this.activeProductPurchase.quantity = data.productPurchase.quantity;
                this.activeProductPurchase.unit_cost = data.productPurchase.unit_cost;
                this.activeProductPurchase.receipt = data.productPurchase.receipt;
                this.activeProductPurchase.remarks = data.productPurchase.remarks;
            }
        },
        deletePurchase(item) {
            if(confirm('Delete this purchase?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('productpurchase/deleteProductPurchase', item.id).then((res, rej) => {
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
