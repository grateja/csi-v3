<template>
    <v-dialog persistent :value="value" max-width="480px">
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title grey--text">Partial payment</span>
                <v-spacer></v-spacer>
                <v-btn icon small @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text v-if="!!partialPayment">
                <!-- <pre>{{partialPayment}}</pre> -->
                <v-layout>
                    <v-flex xs5><span class="data-term">Date:</span></v-flex>
                    <v-flex xs7><span class="data-value">{{moment(partialPayment.date).format('LLL')}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term">Total amount:</span></v-flex>
                    <v-flex xs7><span class="data-value">P {{parseFloat(partialPayment.total_amount).toLocaleString(2)}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term">Cash:</span></v-flex>
                    <v-flex xs7><span class="data-value">P {{parseFloat(partialPayment.cash).toLocaleString(2)}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term font-weight-bold">OR Number:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">{{partialPayment.or_number}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term">Balance:</span></v-flex>
                    <v-flex xs7><span class="data-value">P {{parseFloat(partialPayment.balance).toLocaleString(2)}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term">Paid to:</span></v-flex>
                    <v-flex xs7><span class="data-value">{{partialPayment.paid_to}}</span></v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'partialPaymentId'
    ],
    data() {
        return {
            partialPayment: null
        }
    },
    methods: {
        load() {
            axios.get(`/api/payments/partial-payments/${this.partialPaymentId}`).then((res, rej) => {
                this.partialPayment = res.data.partialPayment;
            });
        },
        close() {
            this.partialPayment = null;
            this.$emit('input', false);
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
