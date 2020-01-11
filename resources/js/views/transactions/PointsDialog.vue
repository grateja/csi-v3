<template>
    <v-dialog :value="value" max-width="450" persistent>
        <v-card>
            <v-card-title class="title">Use points</v-card-title>
            <v-card-text>
                <v-text-field :value="customer.earned_points" label="Current points" readonly></v-text-field>
                <v-text-field :value="amountInPeso" label="Total amount in peso" readonly></v-text-field>
                <v-text-field v-model="amountToUse" label="Amount to use" hint="Amount to use in peso" outline></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn class="primary" @click="setPoints">ok</v-btn>
                <v-btn @click="close">close</v-btn>
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
            amountInPeso: 0
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
            axios.get(`/api/customers/${this.customer.id}/check-points`).then((res, rej) => {
                this.amountInPeso = res.data.pointsInPeso;
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
