<template>
    <v-dialog :value="value" max-width="720" persistent>
        <v-card>
            <v-card-title class="title grey--text">Preview transaction</v-card-title>
            <v-card-text>
                <pre>{{tempTransaction}}</pre>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close">close</v-btn>
                <v-btn class="primary" @click="viewPayment" v-if="tempTransaction && tempTransaction.payment == null">View payment</v-btn>
            </v-card-actions>
        </v-card>
        <payment-dialog v-model="openPaymentDialog" :transaction="tempTransaction" @save="savePayment" />
    </v-dialog>
</template>

<script>
import PaymentDialog from '../transactions/PaymentDialog.vue';

export default {
    components: {
        PaymentDialog
    },
    props: [
        'value', 'transactionId'
    ],
    data() {
        return {
            loading: false,
            tempTransaction: null,
            openPaymentDialog: false
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
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
