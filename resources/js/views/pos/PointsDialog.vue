<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card>
            <v-card-title class="title grey--text">Use customer points</v-card-title>
            <v-card-text>
                <dl>
                    <dt class="grey--text caption">Current points</dt>
                    <dd class="ml-3">{{currentPoints}}</dd>
                    <dt class="grey--text caption">Amount in peso</dt>
                    <dd class="ml-3">{{pointsInPeso}}</dd>
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
        'pointsInPeso',
        'currentPoints'
    ],
    data() {
        return {
            amount: 0,
            errors: null
        }
    },
    methods: {
        ok() {
            if(this.amount > this.pointsInPeso) {
                this.errors = 'Not enough points';
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
