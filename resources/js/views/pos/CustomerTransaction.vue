<template>
    <v-card>
        <v-card-title class="title grey--text">Select customer</v-card-title>
        <v-divider></v-divider>
        <v-card-text>
            <v-chip v-if="!!customer" close @input="removeCustomer">{{customer.name}}</v-chip>
            <v-btn v-else small class="ml-0" @click="createCustomer"><v-icon small left>add</v-icon>New customer</v-btn>

            <h3 class="font-weight-bold grey--text" v-if="!!transaction">{{transaction.job_order}}</h3>

            <form @submit.prevent="filter" v-if="customer == null">
                <v-text-field :loading="loading" v-model="keyword" label="Search" append-icon="search" @input="filter"></v-text-field>
            </form>

            <v-list dense v-if="customer == null">
                <v-list-tile v-for="customer in customers" :key="customer.id" @click="selectCustomer(customer)">
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{customer.name}}
                        </v-list-tile-title>
                        <div class="caption grey--text">{{customer.address}}</div>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-card-text>
        <customer-orders v-if="customer"></customer-orders>

        <div class="text-xs-center">
            <v-progress-circular v-if="selecting" indeterminate></v-progress-circular>
        </div>

        <div class="text-xs-center pa-3" v-if="!!transaction && !transaction.is_saved">
            <v-btn flat small class="primary" @click="save">Confirm</v-btn>
            <v-btn flat small outline v-if="transaction.someSaved" @click="voidTransaction">Void transaction</v-btn>
            <!-- <v-btn flat small outline v-else>Cancel transaction</v-btn> -->
        </div>

        <div class="text-xs-center pa-3" v-else-if="!!transaction && transaction.is_saved && transaction.hasItems">
            <v-btn flat small class="primary" @click="showPayment">Payment</v-btn>
            <v-btn flat small outline @click="voidTransaction">Void transaction</v-btn>
        </div>

        <customer-dialog v-model="openCustomerDialog"></customer-dialog>
        <payment-dialog v-model="openPaymentDialog" @ok="paymentDone"></payment-dialog>
        <void-transaction-dialog v-model="openVoidTransaction" @ok="removeTransaction"></void-transaction-dialog>
        <print-prompt @close="removeCustomer" v-model="openPrintPrompt"></print-prompt>
    </v-card>
</template>

<script>
import PrintPrompt from './PrintPrompt.vue';
import CustomerDialog from '../people/customers/CustomerDialog.vue';
import CustomerOrders from './CustomerOrders.vue';
import PaymentDialog from './PaymentDialog.vue';
import VoidTransactionDialog from './VoidTransactionDialog.vue';

export default {
    components: {
        CustomerDialog,
        CustomerOrders,
        PaymentDialog,
        VoidTransactionDialog,
        PrintPrompt
    },
    data() {
        return {
            keyword: '',
            page: 1,
            loading: false,
            customers: [],
            // selectedCustomer: null,
            openCustomerDialog: false,
            openPaymentDialog: false,
            openVoidTransaction: false,
            openPrintPrompt: false
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.load();
        },
        load() {
            if(this.loading) return;

            this.loading = true;

            axios.get('/api/search/customers/self', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                }
            }).then((res, rej) => {
                this.customers = res.data.result.data;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        selectCustomer(customer) {
            // this.selectedCustomer = customer;
            this.$store.commit('customer/setCustomer', customer);
            this.customers = [];
            this.loadTransaction();
        },
        createCustomer() {
            this.$store.commit('customer/setCustomer', null);
            // this.selectedCustomer = null;
            this.openCustomerDialog = true;
        },
        removeCustomer() {
            this.$store.commit('customer/setCustomer', null);
            this.$store.commit('transaction/clearProducts');
            this.$store.commit('transaction/clearServices');
            this.$store.commit('transaction/clearTransaction');
        },
        // selectCreatedCustomer(customer) {
        //     this.selectedCustomer = customer;
        //     this.loadTransaction();
        // },
        loadTransaction() {
            if(this.customer) {
                this.$store.dispatch('transaction/loadOrders', {
                    customerId: this.customer.id,
                    query: {
                        branchId: this.activeBranchId
                    }
                });
            }
        },
        reset() {
            this.$store.commit('customer/setCustomer', null);
        },
        save() {
            if(this.transaction) {
                this.$store.dispatch('transaction/saveCurrentTransaction', {
                    transactionId: this.transaction.id
                }).then((res, rej) => {
                    this.loadTransaction();
                    this.$store.commit('setFlash', {
                        message: 'Transaction saved!',
                        color: 'primary'
                    });
                });
            }
        },
        showPayment() {
            this.openPaymentDialog = true;
        },
        paymentDone(data) {
            this.openPrintPrompt = true;
            // if(confirm("Do you want to print receipt?")) {
            //     this.$store.dispatch('printer/printReceipt', {
            //         transactionId: data.transactionId
            //     });
            //     console.log(data);
            // }
        },
        voidTransaction() {
            this.openVoidTransaction = true;
        },
        removeTransaction() {
            this.loadTransaction();
        }
    },
    computed: {
        customer() {
            return this.$store.getters['customer/getCustomer'];
        },
        activeBranchId() {
            return this.$store.getters.getActiveBranch ? this.$store.getters.getActiveBranch.id : null
        },
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        },
        selecting() {
            return this.$store.getters['transaction/isLoading'];
        }
    },
    beforeMount() {
        this.reset();
    },
    beforeDestroy() {
        this.reset();
    }
}
</script>
