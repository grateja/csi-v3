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
                        <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(transaction.total_amount).toFixed(2)}}</span></v-flex>
                    </v-layout>
                    <v-layout v-if="discount">
                        <v-flex xs5><span class="data-term font-weight-bold">Discount:</span></v-flex>
                        <v-flex xs7>
                            <span class="data-value">{{`P ${discountInPeso} (${discount.percentage}%)`}}
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
                    <v-divider class="transparent my-2"></v-divider>
                        <v-card class="pa-4 rounded-card text-xs-center" color="#ff00e2">
                            <!-- <h4 class="grey--text">Total amount:</h4>
                            <v-divider></v-divider>
                            <span class="title">P {{parseFloat(transaction.total_amount).toFixed(2)}}</span> -->

                            <h4 class="white--text">Amount to pay:</h4>
                            <v-divider color="red"></v-divider>
                            <span class="display-1">P {{parseFloat(transaction.total_amount - discountInPeso - formData.rfidCardLoad - formData.pointsInPeso).toFixed(2)}}</span>
                        </v-card>
                        <v-text-field class="round-input mt-3 text-xs-center" outline v-model="formData.cash" label="Cash" ref="cash" :error-messages="errors.get('cash')"></v-text-field>
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
                    <v-layout>
                        <v-flex xs5><span class="data-term font-weight-bold">Change:</span></v-flex>
                        <v-flex xs7><span class="data-value font-weight-bold">P {{change}}</span></v-flex>
                    </v-layout>
                </v-card-text>
                <v-card-text>
                        <v-btn round @click="openDiscountDialog = true" :class="{'primary' : !!discount}">discount</v-btn>
                        <v-btn round @click="selectPoints" :class="{'primary' : !!formData.pointsInPeso}">points</v-btn>
                        <v-btn round @click="selectCard" :class="{'primary' : !!formData.rfidCardLoad}">card</v-btn>
                </v-card-text>
                <!-- <v-card-actions>
                    <v-checkbox v-model="printJobOrder" label="Print job order"></v-checkbox>
                </v-card-actions> -->
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round :loading="saving">Save</v-btn>
                    <v-btn round :loading="saving" @click="saveAndPrint">Save and print</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn @click="close" round>close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <rfid-card-dialog v-model="openRfidCardDialog" :customerId="transaction.customer_id" v-if="transaction" @setCard="setCard" />
        <points-dialog v-model="openPointsDialog" :customer="transaction.customer" @setPoints="setPoints"  v-if="transaction" />
        <discount-dialog v-model="openDiscountDialog" @setDiscount="selectDiscount" />
    </v-dialog>
</template>

<script>
import RfidCardDialog from './RfidCardDialog.vue';
import PointsDialog from './PointsDialog.vue';
import DiscountDialog from './DiscountDialog.vue';

export default {
    components: {
        RfidCardDialog,
        PointsDialog,
        DiscountDialog
    },
    props: [
        'transaction', 'value'
    ],
    data() {
        return {
            openRfidCardDialog: false,
            openPointsDialog: false,
            openDiscountDialog: false,
            discount: null,
            printJobOrder: false,
            formData: {
                cash: 0,
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
            this.formData.discountId = this.discount ? this.discount.id : null;
            this.$store.dispatch('payment/proceedToPayment', {
                transactionId: this.transaction.id,
                formData: this.formData
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
        },
        selectDiscount(data) {
            this.discount = data;
        }
    },
    computed: {
        change() {
            let cash = parseFloat(this.formData.cash) || 0;
            let discount = parseFloat(this.discountInPeso) || 0;
            let points = parseFloat(this.formData.pointsInPeso) || 0;
            let load = parseFloat(this.formData.rfidCardLoad) || 0;
            let total_amount = parseFloat(this.transaction.total_amount) || 0;
            let change = cash + discount + points + load - total_amount;
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
                return  parseFloat(this.transaction.total_amount * this.discount.percentage / 100).toFixed(2);
            }
            return 0;
        },
        errors() {
            return this.$store.getters['payment/getErrors'];
        }
    },
    watch: {
        value(val) {
            if(val && this.transaction) {
                this.formData.cash = this.transaction.total_amount;
                setTimeout(() => {
                    this.$refs.cash.$el.querySelector('input').select();
                }, 500);
            }
        }
    }
}
</script>
