<template>
    <div>
        <div v-if="!!currentCustomer">
            <v-card>
                <v-card-actions>
                    <h4 class="title grey--text">CUSTOMER: </h4>
                    <h1 class="title">{{currentCustomer.name}}</h1>
                    <v-spacer></v-spacer>
                    <v-btn icon small @click="removeCustomer">
                        <v-icon small>close</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </div>
        <div v-else>
            <v-text-field dense :loading="loading" @keyup="filter" v-model="keyword" label="Search customer" append-icon="search" outline></v-text-field>
            <v-btn v-if="items.length == 0" class="primary ma-0" round @click="openCustomerDialog = true">Create new customer</v-btn>
        </div>
        <v-card v-if="items.length">
            <v-list dense>
                <v-list-tile v-for="item in items" :key="item.id" @click="selectCustomer(item)">
                    <v-list-tile-content>
                        <v-list-tile-title>
                            <h3>{{item.name}}</h3>
                        </v-list-tile-title>
                        <span class="grey--text">{{item.address}}</span>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-card>
        <v-card v-if="!currentCustomer && !loading && unsavedTransactions.length" class="my-3">
            <v-card-title class="grey--text">Customers with unsaved transaction</v-card-title>
            <v-divider class="my-1"></v-divider>
            <v-list dense>
                <v-list-tile v-for="customer in unsavedTransactions" :key="customer.id" @click="selectCustomer(customer)">
                    {{customer.name}}
                </v-list-tile>
            </v-list>
        </v-card>
        <customer-dialog v-model="openCustomerDialog" @save="setCustomer" />
    </div>
</template>

<script>
import CustomerDialog from '../customers/CustomerDialog.vue';

export default {
    components: {
        CustomerDialog
    },
    data() {
        return {
            openCustomerDialog: false,
            loading: false,
            cancelSource: null,
            keyword: null,
            items: [],
            customerName: null,
            unsavedTransactions: []
        }
    },
    methods: {
        filter() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            if(!this.keyword) {
                this.items = [];
                return;
            }

            this.loading = true;
            axios.get('/api/autocomplete/customers', {
                params: {
                    keyword: this.keyword
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.items = res.data.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        selectCustomer(customer) {
            this.$emit('selectCustomer', customer);
            this.items = [];
            this.unsavedTransactions = [];
        },
        cancelSearch(){
            if(this.cancelSource){
                this.cancelSource.cancel();
            }
        },
        removeCustomer() {
            this.$store.commit('postransaction/removeCustomer');
            this.loadUnsavedTransactions();
        },
        createCustomer() {
            this.removeCustomer();
            this.openCustomerDialog = true;
        },
        setCustomer(data) {
            this.selectCustomer(data.customer);
        },
        loadUnsavedTransactions() {
            axios.get('/api/transactions/unsaved-transactions').then((res, rej) => {
                this.unsavedTransactions = res.data.result;
            });
        }
    },
    computed: {
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        }
    },
    created() {
        this.loadUnsavedTransactions();
    }
}
</script>
