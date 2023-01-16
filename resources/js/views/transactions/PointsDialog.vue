<template>
    <v-dialog :value="value" max-width="450" persistent>
        <v-card class="rounded-card">
            <v-card-title class="title">Use points</v-card-title>
            <v-progress-linear class="my-0" height="1" indeterminate v-if="loading"></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-card-text>
                <v-layout>
                    <v-flex xs5><span class="data-term font-weight-bold">Current points:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">{{customer.earned_points}} pts.</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term font-weight-bold">Total amount in peso:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">P {{parseFloat(amountInPeso).toFixed(2)}}</span></v-flex>
                </v-layout>
                <v-divider class="transparent my-2"></v-divider>
                <!-- <v-text-field :value="customer.earned_points" label="Current points" readonly class="rounded-input" outline></v-text-field>
                <v-text-field :value="amountInPeso" label="Total amount in peso" readonly class="rounded-input" outline></v-text-field> -->
                <v-text-field v-model="amountToUse" label="Amount to use" hint="Amount to use in peso" outline class="round-input"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn class="primary" @click="setPoints" round>ok</v-btn>
                <v-btn @click="close" round>close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'customer', 'value'
    ],
    data() {
        return {
            amountToUse: 0,
            amountInPeso: 0,
            loading: false
        }
    },
    methods: {
        setPoints() {
            if(this.amountToUse > this.amountInPeso) {
                alert('Not enough points.');
                return;
            }
            this.$emit('setPoints', {
                amountToUse: this.amountToUse
            });
            this.close();
        },
        close() {
            this.$emit('input', false);
        },
        checkPoints() {
            this.loading = true;
            axios.get(`/api/customers/${this.customer.id}/check-points`).then((res, rej) => {
                this.amountInPeso = res.data.pointsInPeso;
                this.loading = false;
            }).catch(e => {
                this.loading = false;
            });
        }
    },
    watch: {
        value(val) {
            if(val && this.customer) {
                this.checkPoints();
            } else {

            }
        }
    }
}
</script>
