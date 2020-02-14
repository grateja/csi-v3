<template>
    <v-dialog :value="value" max-width="480" persistent>
        <v-card>
            <v-card-title class="title grey--text">Preview transaction</v-card-title>
            <v-progress-linear class="my-0" v-if="loading" indeterminate height="1" />
            <v-divider class="my-0" v-else></v-divider>
            <v-card-text v-if="tempTransaction">
                <dl>
                    <dt class="caption font-weight-bold grey--text">Date</dt>
                    <dd class="ml-3">{{moment(tempTransaction.date).format('LLL')}}</dd>

                    <dt class="caption font-weight-bold grey--text">Job Order</dt>
                    <dd class="ml-3">{{tempTransaction.job_order}}</dd>

                    <dt class="caption font-weight-bold grey--text">Customer Name</dt>
                    <dd class="ml-3">{{tempTransaction.customer_name}}</dd>

                    <dt class="caption font-weight-bold grey--text">Staff Name</dt>
                    <dd class="ml-3">{{tempTransaction.staff_name}}</dd>
                </dl>


                <template>
                    <v-card v-if="tempTransaction.remarks && tempTransaction.remarks.length">
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
                <v-btn block flat outline @click="viewRemarks">
                    <v-icon small left>edit</v-icon>
                    remarks

                </v-btn>

                <table class="v-table" v-if="tempTransaction.posServiceItems.length">
                    <tr>
                        <th colspan="4">Services</th>
                    </tr>
                    <tr>
                        <th>NAME</th>
                        <th>UNIT PRICE</th>
                        <th>QUANTITY</th>
                        <th>TOTAL</th>
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

                <v-divider class="my-2"></v-divider>

                <table class="v-table" v-if="tempTransaction.posProductItems.length">
                    <tr>
                        <th colspan="4">Products</th>
                    </tr>
                    <tr>
                        <th>NAME</th>
                        <th>UNIT PRICE</th>
                        <th>QUANTITY</th>
                        <th>TOTAL</th>
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
                <v-divider class="my-1"></v-divider>
                <table class="v-table">
                    <tr class="font-weight-bold title">
                        <td>Grand total</td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td class="text-xs-center">{{tempTransaction.posProductSummary.total_quantity + tempTransaction.posServiceSummary.total_quantity}}</td>
                        <td class="text-xs-center">P {{parseFloat(tempTransaction.total_price).toFixed(2)}}</td>
                    </tr>
                </table>

                <v-divider class="my-3 transparent"></v-divider>

                <dl v-if="!!tempTransaction.date_paid">
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
                </dl>

            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
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
            </v-card-actions>
        </v-card>
        <payment-dialog v-model="openPaymentDialog" :transaction="tempTransaction" @save="savePayment" />
        <transaction-remarks-dialog v-model="openRemarksDialog" :transaction="tempTransaction" />
    </v-dialog>
</template>

<script>
import PaymentDialog from '../transactions/PaymentDialog.vue';
import TransactionRemarksDialog from '../transactions/TransactionRemarksDialog.vue';

export default {
    components: {
        PaymentDialog,
        TransactionRemarksDialog
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
