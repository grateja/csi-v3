<template>
    <v-dialog :value="value" max-width="480" persistent>
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title grey--text">Preview transaction</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-progress-linear class="my-0" v-if="loading" indeterminate height="1" />
            <v-divider class="my-0" v-else></v-divider>
            <div class="transaction-container">
                <v-card-text v-if="tempTransaction" class="transaction-wrapper">
                    <v-layout>
                        <v-flex xs5><span class="data-term font-weight-bold">Date:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">{{moment(tempTransaction.date).format('LLL')}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term font-weight-bold">Job order #:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">{{tempTransaction.job_order}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term font-weight-bold">Customer name:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">{{tempTransaction.customer_name}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term font-weight-bold">Staff name:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">{{tempTransaction.staff_name}}</span></v-flex>
                    </v-layout>
                    <template>
                        <v-card v-if="tempTransaction.remarks && tempTransaction.remarks.length" class="rounded-card" color="#beb60024">
                            <v-card-text>
                                <h3 class="grey--text font-italic">Remarks</h3>
                                <ul>
                                    <li v-for="remarks in tempTransaction.remarks" :key="remarks.id">
                                        {{remarks.remarks}}
                                    </li>
                                </ul>
                            </v-card-text>
                        </v-card>
                    </template>
                    <v-btn block flat outline @click="viewRemarks" round>
                        <v-icon small left>edit</v-icon>
                        remarks

                    </v-btn>

                    <table class="v-table" v-if="tempTransaction.posServiceItems.length">
                        <tr>
                            <th colspan="4">Services</th>
                        </tr>
                        <tr>
                            <th class="name">NAME</th>
                            <th class="unit-price">UNIT PRICE</th>
                            <th class="quantity">QUANTITY</th>
                            <th class="total">TOTAL</th>
                        </tr>
                        <tr v-for="item in tempTransaction.posServiceItems" :key="item.id">
                            <td class="pl-1">{{item.name}}</td>
                            <td class="text-xs-center">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</td>
                            <td class="text-xs-center">
                                {{item.quantity}}
                            </td>
                            <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                        </tr>
                        <tr class=" font-weight-bold">
                            <td colspan="2" class="pl-1">Total</td>
                            <td class="text-xs-center">{{tempTransaction.posServiceSummary.total_quantity}}</td>
                            <td class="text-xs-center">P {{parseFloat(tempTransaction.posServiceSummary.total_price).toFixed(2)}}</td>
                        </tr>
                    </table>

                    <v-divider class="my-2 transparent"></v-divider>

                    <table class="v-table" v-if="tempTransaction.posScarpaCleaningItems.length">
                        <tr>
                            <th colspan="4">Scarpa</th>
                        </tr>
                        <tr>
                            <th class="name">NAME</th>
                            <th class="unit-price">UNIT PRICE</th>
                            <th class="quantity">QUANTITY</th>
                            <th class="total">TOTAL</th>
                        </tr>
                        <tr v-for="item in tempTransaction.posScarpaCleaningItems" :key="item.id">
                            <td class="pl-1">{{item.name}}</td>
                            <td class="text-xs-center">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</td>
                            <td class="text-xs-center">
                                {{item.quantity}}
                            </td>
                            <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                        </tr>
                        <tr class=" font-weight-bold">
                            <td colspan="2" class="pl-1">Total</td>
                            <td class="text-xs-center">{{tempTransaction.posScarpaCleaningSummary.total_quantity}}</td>
                            <td class="text-xs-center">P {{parseFloat(tempTransaction.posScarpaCleaningSummary.total_price).toFixed(2)}}</td>
                        </tr>
                    </table>

                    <table class="v-table" v-if="tempTransaction.posLagoonItems.length">
                        <tr>
                            <th colspan="4">LAGOON</th>
                        </tr>
                        <tr>
                            <th class="name">NAME</th>
                            <th class="unit-price">UNIT PRICE</th>
                            <th class="quantity">QUANTITY</th>
                            <th class="total">TOTAL</th>
                        </tr>
                        <tr v-for="item in tempTransaction.posLagoonItems" :key="item.id">
                            <td class="pl-1">{{item.name}}</td>
                            <td class="text-xs-center">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</td>
                            <td class="text-xs-center">
                                {{item.quantity}}
                            </td>
                            <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                        </tr>
                        <tr class=" font-weight-bold">
                            <td colspan="2" class="pl-1">Total</td>
                            <td class="text-xs-center">{{tempTransaction.posLagoonSummary.total_quantity}}</td>
                            <td class="text-xs-center">P {{parseFloat(tempTransaction.posLagoonSummary.total_price).toFixed(2)}}</td>
                        </tr>
                    </table>

                    <table class="v-table" v-if="tempTransaction.posProductItems.length">
                        <tr>
                            <th colspan="4">Products</th>
                        </tr>
                        <tr>
                            <th class="name">NAME</th>
                            <th class="unit-price">UNIT PRICE</th>
                            <th class="quantity">QUANTITY</th>
                            <th class="total">TOTAL</th>
                        </tr>
                        <tr v-for="item in tempTransaction.posProductItems" :key="item.id">
                            <td class="pl-1">{{item.name}}</td>
                            <td class="text-xs-center">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</td>
                            <td class="text-xs-center">
                                {{item.quantity}}
                            </td>
                            <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                        </tr>
                        <tr class=" font-weight-bold">
                            <td colspan="2" class="pl-1">Total</td>
                            <td class="text-xs-center">{{tempTransaction.posProductSummary.total_quantity}}</td>
                            <td class="text-xs-center">P {{parseFloat(tempTransaction.posProductSummary.total_price).toFixed(2)}}</td>
                        </tr>
                    </table>
                    <v-divider class="my-1 transparent"></v-divider>
                    <table class="v-table">
                        <tr class="font-weight-bold title">
                            <td>Grand total</td>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td class="text-xs-center">{{tempTransaction.posProductSummary.total_quantity + tempTransaction.posServiceSummary.total_quantity}}</td>
                            <td class="text-xs-center">P {{parseFloat(tempTransaction.total_price).toFixed(2)}}</td>
                        </tr>
                    </table>

                    <v-divider class="my-3 transparent"></v-divider>

                    <v-card v-if="!!tempTransaction.partial_payment && !tempTransaction.date_paid" class="rounded-card">
                        <v-card-title>Partial payment</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-layout v-if="tempTransaction.partial_payment">
                                <v-flex xs5><span class="data-term font-weight-bold">Partial payment:</span></v-flex>
                                <v-flex xs7>
                                    <span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.partial_payment.total_paid).toFixed(2)}}
                                        <v-tooltip top>
                                            <span>View partial<br /> payment</span>
                                            <v-btn slot="activator" icon small class="ma-0" @click="openPartialPayment = true"><v-icon>open_in_new</v-icon></v-btn>
                                        </v-tooltip>
                                    </span>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="tempTransaction.partial_payment">
                                <v-flex xs5><span class="data-term font-weight-bold">Balance:</span></v-flex>
                                <v-flex xs7>
                                    <span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.partial_payment.balance).toFixed(2)}}</span>
                                </v-flex>
                            </v-layout>
                        </v-card-text>
                    </v-card>
                    <v-card v-else-if="!!tempTransaction.date_paid" class="rounded-card">
                        <v-card-title>
                            Payment details
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-layout>
                                <v-flex xs5><span class="data-term font-weight-bold">Date paid:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">{{moment(tempTransaction.date_paid).format('LLL')}}</span></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs5><span class="data-term font-weight-bold">Paid to:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">{{tempTransaction.payment.paid_to}}</span></v-flex>
                            </v-layout>
                            <v-layout v-if="tempTransaction.partial_payment">
                                <v-flex xs5><span class="data-term font-weight-bold">Partial payment:</span></v-flex>
                                <v-flex xs7>
                                    <span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.partial_payment.total_paid).toFixed(2)}}
                                        <v-tooltip top>
                                            <span>View partial<br /> payment</span>
                                            <v-btn slot="activator" icon small class="ma-0" @click="openPartialPayment = true"><v-icon>open_in_new</v-icon></v-btn>
                                        </v-tooltip>
                                    </span>
                                </v-flex>
                            </v-layout>
                            <v-layout v-if="tempTransaction.payment.cash_less_amount">
                                <v-flex xs5><span class="data-term font-weight-bold">{{tempTransaction.payment.cash_less_provider}}</span></v-flex>
                                <v-flex xs7>
                                    <div class="data-value">
                                        <div class="font-weight-bold">P {{parseFloat(tempTransaction.payment.cash_less_amount).toFixed(2)}}</div>
                                        <div> REF#.{{tempTransaction.payment.cash_less_reference_number}}</div>
                                    </div>
                                </v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs5><span class="data-term font-weight-bold">Cash:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.payment.cash).toFixed(2)}}</span></v-flex>
                            </v-layout>
                            <v-layout v-if="tempTransaction.payment.points">
                                <v-flex xs5><span class="data-term font-weight-bold">Points used:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.payment.points_in_peso).toFixed(2)}} ({{tempTransaction.payment.points}} points)</span></v-flex>
                            </v-layout>
                            <v-layout v-if="tempTransaction.payment.discount">
                                <v-flex xs5><span class="data-term font-weight-bold">Discount:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">{{tempTransaction.payment.discount_name}}: P {{parseFloat(tempTransaction.payment.discount_in_peso).toFixed(2)}} ({{tempTransaction.payment.discount}}%)</span></v-flex>
                            </v-layout>
                            <v-layout v-if="tempTransaction.payment.rfid">
                                <v-flex xs5><span class="data-term font-weight-bold">RFID:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.payment.card_load_used).toFixed(2)}} ({{tempTransaction.payment.rfid}})</span></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs5><span class="data-term font-weight-bold">Change:</span></v-flex>
                                <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(tempTransaction.payment.change).toFixed(2)}}</span></v-flex>
                            </v-layout>
                        </v-card-text>
                    </v-card>
                    <v-card v-else class="rounded-card" color="red">
                        <v-card-text class="text-xs-center white--text">
                            Not paid!
                        </v-card-text>
                    </v-card>

                    <!-- <dl v-if="!!tempTransaction.date_paid">
                        <dt class="caption font-weight-bold grey--text">Date paid</dt>
                        <dd class="ml-3">{{moment(tempTransaction.date_paid).format('LLL')}}</dd>

                        <dt class="caption font-weight-bold grey--text">Paid to</dt>
                        <dd class="ml-3">{{tempTransaction.payment.paid_to}}</dd>

                        <dt class="caption font-weight-bold grey--text">Cash</dt>
                        <dd class="ml-3">P {{parseFloat(tempTransaction.payment.cash).toFixed(2)}}</dd>

                        <template v-if="tempTransaction.payment.points">
                            <dt class="caption font-weight-bold grey--text">Points used</dt>
                            <dd class="ml-3">P {{parseFloat(tempTransaction.payment.points_in_peso).toFixed(2)}} ({{tempTransaction.payment.points}} points)</dd>
                        </template>

                        <template v-if="tempTransaction.payment.discount">
                            <dt class="caption font-weight-bold grey--text">Discount</dt>
                            <dd class="ml-3">P {{parseFloat(tempTransaction.payment.discount_in_peso).toFixed(2)}} ({{tempTransaction.payment.discount}}%)</dd>
                        </template>

                        <template v-if="tempTransaction.payment.rfid">
                            <dt class="caption font-weight-bold grey--text">RFID</dt>
                            <dd class="ml-3">P {{parseFloat(tempTransaction.payment.card_load_used).toFixed(2)}} ({{tempTransaction.payment.rfid}})</dd>
                        </template>

                        <template v-if="tempTransaction.payment.change">
                            <dt class="caption font-weight-bold grey--text">Change</dt>
                            <dd class="ml-3">P {{parseFloat(tempTransaction.payment.change).toFixed(2)}}</dd>
                        </template>
                    </dl> -->

                </v-card-text>
            </div>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
                <template v-if="!!tempTransaction && !tempTransaction.deleted_at">
                    <v-tooltip top v-if="isOwner">
                        <v-btn slot="activator" icon @click="deleteTransaction" :loading="isDeleting">
                            <v-icon>delete</v-icon>
                        </v-btn>
                        <span>Delete job order</span>
                    </v-tooltip>
                    <v-spacer></v-spacer>
                    <v-btn class="primary" round @click="viewPayment" v-if="tempTransaction && tempTransaction.payment == null">View payment</v-btn>
                    <v-tooltip top>
                        <v-btn slot="activator" class="primary" round @click="printJobOrder" v-if="tempTransaction && !!tempTransaction.payment" :loading="jobOrderLoading" icon>
                            <v-icon>print</v-icon>
                        </v-btn>
                        <span>Print job order</span>
                    </v-tooltip>
                    <v-tooltip top>
                        <v-btn slot="activator" round @click="printClaimStub" v-if="tempTransaction" :loading="claimStubLoading" icon>
                            <v-icon>print</v-icon>
                        </v-btn>
                        <span>Print claim stub</span>
                    </v-tooltip>
                </template>
            </v-card-actions>
        </v-card>
        <payment-dialog v-model="openPaymentDialog" :transaction="tempTransaction" @save="savePayment" />
        <transaction-remarks-dialog v-model="openRemarksDialog" :transaction="tempTransaction" />
        <partial-payment-dialog v-if="tempTransaction && tempTransaction.partial_payment" v-model="openPartialPayment" :partialPaymentId="tempTransaction.partial_payment.id"></partial-payment-dialog>
    </v-dialog>
</template>

<script>
import PaymentDialog from '../transactions/PaymentDialog.vue';
import TransactionRemarksDialog from '../transactions/TransactionRemarksDialog.vue';
import PartialPaymentDialog from '../transactions/PartialPaymentDialog.vue'

export default {
    components: {
        PaymentDialog,
        TransactionRemarksDialog,
        PartialPaymentDialog
    },
    props: [
        'value', 'transactionId'
    ],
    data() {
        return {
            loading: false,
            tempTransaction: null,
            openPaymentDialog: false,
            openRemarksDialog: false,
            openPartialPayment: false,
            isDeleting: false
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.loading = true;
            axios.get(`/api/transactions/${this.transactionId}`).then((res, rej) => {
                this.tempTransaction = res.data.transaction;
            }).finally(() => {
                this.loading = false;
            });
        },
        viewPayment() {
            this.openPaymentDialog = true;
        },
        savePayment(transaction) {
            this.$emit('input', false);
            this.$emit('savePayment', transaction);
        },
        printClaimStub() {
            this.$store.dispatch('printer/printClaimStub', {
                transactionId: this.transactionId
            });
        },
        printJobOrder() {
            this.$store.dispatch('printer/printJobOrder', this.transactionId);
        },
        deleteTransaction() {
            if(confirm('Delete this transaction?')) {
                this.isDeleting = true;
                this.$store.dispatch('transaction/deleteTransaction', this.transactionId).then((res, rej) => {
                    this.$emit('deleteTransaction', res.data.transaction);
                    this.tempTransaction = null;
                    this.close();
                }).finally(() => {
                    this.isDeleting = false;
                })
            }
        },
        viewRemarks() {
            this.openRemarksDialog = true;
        }
    },
    computed: {
        jobOrderLoading() {
            return this.$store.getters['printer/jobOrderLoading'];
        },
        claimStubLoading() {
            return this.$store.getters['printer/claimStubLoading'];
        },
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            if(user) {
                return user.roles[0] == 'admin'
            }
            return false;
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            } else {
                this.tempTransaction = null;
            }
        }
    }
}
</script>

<style scoped>
    .v-table tr {
        border: 1px solid silver;
    }
    .name {
        width: 40%;
    }
    .unit-price {
        width: 20%;
    }
    .quantity {
        width: 20%;
    }
    .total {
        width: 20%;
    }
    .transaction-container {
        overflow-y: auto;
    }
    .transaction-wrapper {
        max-height: 70vh;
        margin-bottom: 20px;
    }
</style>
