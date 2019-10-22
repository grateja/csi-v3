<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Top up</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="rfidCard">
                    <!-- <h4 class="grey--text caption">Customer name</h4>
                    <p></p>{{rfidCard.owner_name}} -->

                    <dl>
                        <dt class="caption grey--text">Customer name</dt>
                        <dd class="font-weight-bold">{{rfidCard.owner_name}}</dd>
                        <dt class="caption grey--text">RFID</dt>
                        <dd class="font-weight-bold">{{rfidCard.rfid}}</dd>
                    </dl>

                    <VDivider class="my-4"/>

                    <v-text-field v-model="rfidCard.balance" label="Remaining balance" readonly></v-text-field>
                    <v-text-field v-model="formData.amount" label="Amount" :error-messages="errors.get('amount')"></v-text-field>
                    <v-text-field v-model="formData.cash" label="Cash" :error-messages="errors.get('cash')"></v-text-field>
                    <v-text-field v-model="change" label="Change" readonly></v-text-field>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn type="submit" class="primary" :loading="saving">ok</v-btn>
                    <v-btn @click="cancel">cancel</v-btn>
                </v-card-actions>
            </v-card>
        </form>
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
            formData: {
                amount: 0,
                cash: 0
            }
        }
    },
    computed: {
        change() {
            let change = this.formData.cash - this.formData.amount;
            if(change >= 0) {
                return change;
            } else if(this.formData.cash > 0){
                return 'Cash not enough.';
            }
        },
        errors() {
            return this.$store.getters['cardtransaction/getErrors'];
        },
        saving() {
            return this.$store.getters['cardtransaction/isSaving'];
        }
    },
    methods: {
        cancel() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch('cardtransaction/topUp', {
                rfidCardId: this.rfidCard.id,
                formData: this.formData
            }).then((res, rej) => {
                this.formData.amount = 0;
                this.formData.cash = 0;
                this.$emit('save', res.data.rfidCard);
                this.$emit('input', false);
            });
        }
    }
}
</script>
