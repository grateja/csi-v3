<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card>
            <v-card-title class="title grey--text">Use card balance</v-card-title>
            <v-card-text v-if="!!rfidCard">
                <dl>
                    <dt class="grey--text caption">RFID</dt>
                    <dd class="ml-3">{{rfidCard.rfid}}</dd>
                    <dt class="grey--text caption">Current balance</dt>
                    <dd class="ml-3">{{rfidCard.balance}}</dd>
                </dl>
                <v-text-field label="Amount" v-model="amount" :error-messages="errors"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="ok" class="primary">ok</v-btn>
                <v-btn @click="cancel">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value',
        'rfidCard'
    ],
    data() {
        return {
            amount: 0,
            errors: null
        }
    },
    methods: {
        ok() {
            if(this.amount > this.rfidCard.balance) {
                this.errors = 'Not enough balance';
                return false;
            }
            this.$emit('ok', this.amount);
            this.$emit('input', false);
            this.amount = 0;
        },
        cancel() {
            this.$emit('input', false);
            this.amount = 0;
        }
    }
}
</script>
