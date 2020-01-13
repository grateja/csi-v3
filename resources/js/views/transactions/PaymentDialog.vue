<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card v-if="transaction">
                <v-card-title>
                    <span class="title grey--text">Payment</span>
                    <v-spacer></v-spacer>
                    <v-btn small icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <dl>
                        <dt>Customer name:</dt>
                        <dd>{{transaction.customer_name}}</dd>
                    </dl>
                        <v-card class="pa-2">
                            <h4 class="grey--text">Total amount :</h4>
                            <v-divider></v-divider>
                            <span class="display-2">P {{parseFloat(transaction.total_amount).toFixed(2)}}</span>
                        </v-card>

                        <v-divider class="my-3 transparent"></v-divider>

                        <template v-if="formData.rfidCardId && formData.rfidCardLoad">
                            <v-text-field v-model="formData.rfidCardLoad" label="RFID Card:" readonly clearable></v-text-field>
                        </template>

                        <template v-if="formData.pointsInPeso > 0">
                            <v-text-field v-model="formData.pointsInPeso" label="Points in peso:" readonly clearable></v-text-field>
                        </template>

                        <template v-if="discount">
                            <v-btn small @click="discount = null" class="ma-0">cancel discount</v-btn>
                            <v-text-field :value="discountInPeso" :label="`Discount: ${discount.name} ${discount.percentage}%`" readonly></v-text-field>
                        </template>

                        <v-text-field outline v-model="formData.cash" label="Cash"></v-text-field>
                        <v-text-field outline :value="change" label="Change" readonly></v-text-field>
                        <v-btn round @click="openDiscountDialog = true" :class="{'primary' : !!discount}">discount</v-btn>
                        <v-btn round @click="selectPoints" :class="{'primary' : !!formData.pointsInPeso}">points</v-btn>
                        <v-btn round @click="selectCard" :class="{'primary' : !!formData.rfidCardLoad}">card</v-btn>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round :loading="saving">Save</v-btn>
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
                this.close();
                this.$store.commit('postransaction/clearTransaction');
                this.$store.commit('postransaction/removeCustomer');
                this.$emit('save', res.data.transaction);
            });
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
        }
    },
    watch: {
        value(val) {
            if(val && this.transaction) {
                this.formData.cash = this.transaction.total_amount;
            }
        }
    }
}
</script>
