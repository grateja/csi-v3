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
            <v-card-text>
                <pre>{{partialPayment}}</pre>
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
