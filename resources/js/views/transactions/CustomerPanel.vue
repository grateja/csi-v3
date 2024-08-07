<template>
    <div>
        <!-- <div v-if="!!currentCustomer">
            <v-card class="rounded-card">
                <v-card-actions>
                    <v-layout>
                        <v-flex xs5 class="text-xs-right mr-3">Customer name:</v-flex>
                        <v-flex xs7>{{currentCustomer.name}}</v-flex>
                    </v-layout>
                    <v-btn icon small @click="removeCustomer">
                        <v-icon small>close</v-icon>
                    </v-btn>
                </v-card-actions>
                <v-card-actions v-if="!!currentCustomer && !currentTransaction">
                    <div class="font-italic">No current transaction. Select services or products</div>
                </v-card-actions>
            </v-card>
        </div> -->
        <div>
            <v-btn class="primary mb-3 ml-0" round @click="openCustomerDialog = true">Create new customer</v-btn>
            <v-text-field class="translucent-input round-input" dense :loading="loading" @keyup="filter" v-model="keyword" label="Search customer" append-icon="search" outline></v-text-field>
        </div>
        <v-card v-if="items.length" class="rounded-card pa-2">
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

        <v-card v-if="!currentCustomer && !loading && unsavedTransactions.length" class="my-3 rounded-card">
            <v-card-title class="grey--text">Customers with unsaved transaction</v-card-title>
            <v-divider class="my-1"></v-divider>
            <v-list dense>
                <v-list-tile v-for="customer in unsavedTransactions" :key="customer.id" @click="selectCustomer(customer)">
                    <v-list-tile-action>
                        <div v-if="customer.transactions[0] && customer.transactions[0].job_order">
                            {{customer.transactions[0].job_order}}
                        </div>
                        <div v-else>
                            ######
                        </div>
                    </v-list-tile-action>
                    {{customer.name}}
                </v-list-tile>
            </v-list>
        </v-card>

        <v-card v-if="!currentCustomer && !loading && unpaidTransactions.length" class="my-3 rounded-card">
            <v-card-title class="grey--text">Customers with unpaid transaction</v-card-title>
            <v-divider class="my-1"></v-divider>
            <v-list dense three-line>
                <template v-for="transaction in unpaidTransactions">
                    <v-list-tile :key="transaction.id" @click="selectUnpaidCustomer(transaction)">
                        <v-list-tile-action class="grey--text caption">{{transaction.job_order}}</v-list-tile-action>
                        <v-list-tile-content>
                            <div>{{transaction.customer_name}}</div>
                            <div class="caption grey--text">{{moment(transaction.date).format('LLL')}}</div>
                            <div v-if="transaction.date_paid == null && transaction.partial_payment">
                                <v-chip color="#eeb" small class="ml-0">
                                    Balance: P{{parseFloat(transaction.partial_payment.balance).toFixed(2)}}
                                </v-chip>
                            </div>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            P{{parseFloat(transaction.total_price).toFixed(2)}}
                        </v-list-tile-action>
                    </v-list-tile>
                    <v-divider :key="'div'+transaction.id"></v-divider>
                </template>
            </v-list>
        </v-card>
        <customer-dialog v-model="openCustomerDialog" @save="setCustomer" :initialName="keyword" />
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
            unsavedTransactions: [],
            unpaidTransactions: []
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
        },
        selectUnpaidCustomer(transaction) {
            this.selectCustomer({
                name: transaction.customer_name,
                id: transaction.customer_id
            });
        },
        cancelSearch(){
            if(this.cancelSource){
                this.cancelSource.cancel();
            }
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
        },
        loadUnpaidTransactions() {
            axios.get('/api/transactions/unpaid-transactions').then((res, rej) => {
                this.unpaidTransactions = res.data.result.data;
            });
        }
    },
    computed: {
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        },
        currentTransaction() {
            return this.$store.getters['postransaction/getCurrentTransaction'];
        }
    },
    created() {
        this.loadUnsavedTransactions();
        this.loadUnpaidTransactions();
    },
    watch: {
        currentCustomer(val) {
            if(!!val) {
                this.unpaidTransactions = [];
                this.unsavedTransactions = [];
            } else {
                this.loadUnsavedTransactions();
                this.loadUnpaidTransactions();
            }
        }
    }
}
</script>
