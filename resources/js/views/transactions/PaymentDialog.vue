<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card v-if="transaction" class="rounded-card">
                <v-card-title>
                    <span class="title grey--text">Payment</span>
                    <v-spacer></v-spacer>
                    <v-btn small icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-tooltip top v-if="transaction.birthdayToday">
                        <v-icon slot="activator" class="pointer red--text">cake</v-icon>
                        <span>It's customer's birthday today</span>
                    </v-tooltip>
                    <v-spacer></v-spacer>
                </v-card-actions>
                <v-card-text>

                    <v-layout>
                        <v-flex xs5><span class="data-term">Customer name:</span></v-flex>
                        <v-flex xs7><span class="data-value">{{transaction.customer_name}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term">Date:</span></v-flex>
                        <v-flex xs7><span class="data-value">{{moment(transaction.date).format('MMMM DD, YYYY hh:mm A')}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term">Job order #:</span></v-flex>
                        <v-flex xs7><span class="data-value">{{transaction.job_order}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term font-weight-bold">Total amount:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(transaction.total_price).toFixed(2)}}</span></v-flex>
                    </v-layout>

                    <v-layout v-if="cashLess">
                        <v-flex xs5><span class="data-term font-weight-bold">Cashless:</span></v-flex>
                        <v-flex xs7>
                            <span class="data-value">{{cashLess.provider}}: {{`P ${parseFloat(cashLess.amount).toFixed(2)}`}}
                                <v-tooltip top>
                                    <span>Remove cashless</span>
                                    <v-btn slot="activator" outline icon small class="ma-0" @click="cashLess = null"><v-icon>close</v-icon></v-btn>
                                </v-tooltip>
                            </span>
                        </v-flex>
                    </v-layout>

                    <v-layout v-if="discount">
                        <v-flex xs5><span class="data-term font-weight-bold">Discount:</span></v-flex>
                        <v-flex xs7>
                            <span class="data-value">{{discount.name}}: {{`P ${discountInPeso} (${discount.percentage}%)`}}
                                <v-tooltip top>
                                    <span>Remove discount</span>
                                    <v-btn slot="activator" outline icon small class="ma-0" @click="discount = null"><v-icon>close</v-icon></v-btn>
                                </v-tooltip>
                            </span>
                        </v-flex>
                    </v-layout>
                    <v-layout v-if="formData.pointsInPeso > 0">
                        <v-flex xs5><span class="data-term font-weight-bold">Loyalty points:</span></v-flex>
                        <v-flex xs7>
                            <span class="data-value">{{`P ${parseFloat(formData.pointsInPeso).toFixed(2)}`}}
                                <v-tooltip top>
                                    <span>Loyalty points</span>
                                    <v-btn slot="activator" outline icon small class="ma-0" @click="formData.pointsInPeso = 0"><v-icon>close</v-icon></v-btn>
                                </v-tooltip>
                            </span>
                        </v-flex>
                    </v-layout>
                    <v-layout v-if="formData.rfidCardId && formData.rfidCardLoad">
                        <v-flex xs5><span class="data-term font-weight-bold">RFID Card:</span></v-flex>
                        <v-flex xs7>
                            <span class="data-value">{{`P ${parseFloat(formData.rfidCardLoad).toFixed(2)}`}}
                                <v-tooltip top>
                                    <span>Remove RFID Card</span>
                                    <v-btn slot="activator" outline icon small class="ma-0" @click="formData.rfidCardId = null"><v-icon>close</v-icon></v-btn>
                                </v-tooltip>
                            </span>
                        </v-flex>
                    </v-layout>
                    <v-layout v-if="transaction.partial_payment">
                        <v-flex xs5><span class="data-term font-weight-bold">Partial payment:</span></v-flex>
                        <v-flex xs7>
                            <span class="data-value">{{`P ${parseFloat(transaction.partial_payment.total_paid).toFixed(2)}`}}
                                <v-tooltip top>
                                    <span>View partial<br /> payment</span>
                                    <v-btn slot="activator" icon small class="ma-0" @click="openPartialPayment = true"><v-icon>open_in_new</v-icon></v-btn>
                                </v-tooltip>
                            </span>
                        </v-flex>
                    </v-layout>
                    <v-divider class="transparent my-2"></v-divider>
                        <v-card class="pa-4 rounded-card text-xs-center" color="#ff00e2">
                            <!-- <h4 class="grey--text">Total amount:</h4>
                            <v-divider></v-divider>
                            <span class="title">P {{parseFloat(transaction.total_price).toFixed(2)}}</span> -->

                            <h4 class="white--text">Amount to pay:</h4>
                            <v-divider color="red"></v-divider>
                            <span class="display-1">P {{amountToPay.toFixed(2)}}</span>
                        </v-card>
                        <v-text-field class="round-input mt-3 text-xs-center" outline v-model="formData.cash" label="Cash" ref="cash" :error-messages="errors.get('cash')"></v-text-field>
                        <v-text-field class="round-input mt-3 text-xs-center" outline v-model="formData.orNumber" label="OR number" ref="orNumber" :error-messages="errors.get('orNumber')"></v-text-field>
                        <!-- <template v-if="formData.rfidCardId && formData.rfidCardLoad">
                            <v-text-field v-model="formData.rfidCardLoad" label="RFID Card:" readonly clearable></v-text-field>
                        </template> -->

                        <!-- <template v-if="formData.pointsInPeso > 0">
                            <v-text-field v-model="formData.pointsInPeso" label="Points in peso:" readonly clearable></v-text-field>
                        </template> -->

                        <!-- <template v-if="discount">
                            <v-card class="pa-2 my-2" elevation-4>
                                <v-btn small @click="discount = null" class="ma-0">cancel discount</v-btn>
                                <v-text-field :value="discountInPeso" :label="`Discount: ${discount.name} ${discount.percentage}%`" readonly></v-text-field>
                            </v-card>
                        </template> -->
                    <v-layout v-if="change > 0">
                        <v-flex xs5><span class="data-term font-weight-bold">Change:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">P {{change}}</span></v-flex>
                    </v-layout>
                    <v-layout v-if="balance > 0">
                        <v-flex xs5><span class="data-term font-weight-bold">Balance:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">P {{balance}}</span>
                        </v-flex>
                    </v-layout>
                </v-card-text>
                <v-card-text>
                        <v-btn round @click="openDiscountDialog = true" :class="{'primary' : !!discount}">discount</v-btn>
                        <v-btn round @click="selectPoints" :class="{'primary' : !!formData.pointsInPeso}">points</v-btn>
                        <v-btn round @click="selectCard" :class="{'primary' : !!formData.rfidCardLoad}">card</v-btn>
                        <v-btn round @click="selectCashLess" :class="{'primary' : !!cashLess}">{{ !!cashLess ? cashLess.provider + " P " + parseFloat(cashLess.amount).toFixed(2) : "Cash Less" }}</v-btn>
                </v-card-text>
                <!-- <v-card-actions>
                    <v-checkbox v-model="printJobOrder" label="Print job order"></v-checkbox>
                </v-card-actions> -->
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round :loading="saving && !printJobOrder">{{balance > 0 ? 'Partial payment' : 'Save'}}</v-btn>
                    <!-- <v-btn v-if="balance <= 0" round :loading="saving && printJobOrder" @click="saveAndPrint">Save and print</v-btn> -->
                </v-card-actions>
            </v-card>
        </form>
        <rfid-card-dialog v-model="openRfidCardDialog" :customerId="transaction.customer_id" v-if="transaction" @setCard="setCard" />
        <points-dialog v-model="openPointsDialog" :customer="transaction.customer" @setPoints="setPoints"  v-if="transaction" />
        <discount-dialog v-model="openDiscountDialog" @setDiscount="selectDiscount" />
        <partial-payment-dialog v-if="transaction && transaction.partial_payment" v-model="openPartialPayment" :partialPaymentId="transaction.partial_payment.id"></partial-payment-dialog>
        <cash-less-dialog v-model="openCashLessDialog" :cashLess="cashLess" @confirm="setCashLess" :amountToPay="amountToPay" />
    </v-dialog>
</template>

<script>
import RfidCardDialog from './RfidCardDialog.vue';
import PointsDialog from './PointsDialog.vue';
import DiscountDialog from './DiscountDialog.vue';
import PartialPaymentDialog from './PartialPaymentDialog.vue';
import CashLessDialog from './CashLessDialog.vue'

export default {
    components: {
        RfidCardDialog,
        PointsDialog,
        DiscountDialog,
        PartialPaymentDialog,
        CashLessDialog
    },
    props: [
        'transaction', 'value'
    ],
    data() {
        return {
            openRfidCardDialog: false,
            openPointsDialog: false,
            openDiscountDialog: false,
            openPartialPayment: false,
            openCashLessDialog: false,
            cashLess: null,
            discount: null,
            printJobOrder: false,
            formData: {
                cash: 0,
                orNumber: null,
                discountId: null,
                rfidCardId: null,
                rfidCardLoad: 0,
                pointsInPeso: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            let method = 'full';
            if(this.balance > 0){
                if(!confirm("Proceed with partial payment?")) return;
                method = 'partial';
            }

            this.formData.discountId = this.discount ? this.discount.id : null;
            if(this.cashLess) {
                this.formData.cashlessAmount = this.cashLess.amount
                this.formData.cashlessProvider = this.cashLess.provider
                this.formData.cashlessReferenceNumber = this.cashLess.referenceNumber
            }
            this.$store.dispatch('payment/proceedToPayment', {
                transactionId: this.transaction.id,
                formData: this.formData,
                method
            }).then((res, rej) => {
                if(this.printJobOrder) {
                    this.$store.dispatch('printer/printJobOrder', res.data.transaction.id);
                }
                this.close();
                this.$store.commit('postransaction/clearTransaction');
                this.$store.commit('postransaction/removeCustomer');
                this.$emit('save', res.data.transaction);
            });
        },
        saveAndPrint() {
            this.printJobOrder = true;
            this.submit();
        },
        selectCard() {
            this.openRfidCardDialog = true;
        },
        selectCashLess() {
            this.openCashLessDialog = true
        },
        setCard(data) {
            console.log(data);
            this.formData.rfidCardId = data.cardId;
            this.formData.rfidCardLoad = data.amount;
        },
        selectPoints() {
            this.openPointsDialog = true;
        },
        setPoints(data) {
            this.formData.pointsInPeso = parseFloat(data.amountToUse);
            this.formData.cash = this.amountToPay
        },
        selectDiscount(data) {
            this.discount = data;
        },
        setCashLess(data) {
            this.cashLess = data
            this.formData.cash = this.amountToPay
        }
    },
    computed: {
        change() {
            let cash = parseFloat(this.formData.cash) || 0;
            let discount = parseFloat(this.discountInPeso) || 0;
            let points = parseFloat(this.formData.pointsInPeso) || 0;
            let load = parseFloat(this.formData.rfidCardLoad) || 0;
            let total_price = parseFloat(this.transaction.total_price) || 0;
            let change = this.partialPayment + cash + discount + points + load - total_price + this.cashLessAmount;
            console.log("cash less")
            console.log(this.cashLessAmount)
            if(change <= 0) {
                return 0.00;
            }
            return parseFloat(change).toFixed(2);
        },
        saving() {
            return this.$store.getters['payment/isSaving'];
        },
        discountInPeso() {
            if(this.transaction && this.discount) {
                return  parseFloat(this.transaction.total_price * this.discount.percentage / 100).toFixed(2);
            }
            return 0;
        },
        errors() {
            return this.$store.getters['payment/getErrors'];
        },
        cashLessAmount() {
            if(this.cashLess && this.cashLess.amount) {
                return parseFloat(this.cashLess.amount) || 0
            }
            return 0
        },
        balance() {
            let cash = parseFloat(this.formData.cash) || 0;
            let discount = parseFloat(this.discountInPeso) || 0;
            let points = parseFloat(this.formData.pointsInPeso) || 0;
            let load = parseFloat(this.formData.rfidCardLoad) || 0;
            let total_price = parseFloat(this.transaction.total_price) || 0;
            let bal = total_price - cash - discount - points - load - this.partialPayment - this.cashLessAmount;
            if(bal >= 0) {
                return parseFloat(bal).toFixed(2);
            }
            return 0.00;
        },
        partialPayment() {
            if(this.transaction.partial_payment) {
                return this.transaction.partial_payment.total_paid;
            }
            return 0;
        },
        amountToPay() {
            if(this.transaction) {
                return parseFloat(this.transaction.total_price - this.discountInPeso - this.formData.rfidCardLoad - this.formData.pointsInPeso - this.partialPayment - this.cashLessAmount);
            }
        }
    },
    watch: {
        value(val) {
            if(val && this.transaction) {
                this.formData.cash = this.amountToPay;
                setTimeout(() => {
                    this.$refs.cash.$el.querySelector('input').select();
                }, 500);
            }
        }
    }
}
</script>
