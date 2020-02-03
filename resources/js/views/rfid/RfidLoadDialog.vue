<template>
    <v-dialog :value="value" max-width="400" persistent>
        <form @submit.prevent="submit" v-if="rfidCard">
            <v-card>
                <v-card-title class="title grey--text">RFID Load transaction</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <dl>
                        <dt class="caption grey--text font-weigth-bold">Customer name:</dt>
                        <dd class="ml-3">{{rfidCard.fullname}}</dd>

                        <dt class="caption grey--text font-weigth-bold">RFID:</dt>
                        <dd class="ml-3">{{rfidCard.rfid}}</dd>

                        <dt class="caption grey--text font-weigth-bold">Current balance:</dt>
                        <dd class="ml-3">{{rfidCard.balance}}</dd>
                    </dl>

                    <v-divider class="my-3"></v-divider>

                    <v-text-field outline v-model="formData.amount" label="Amount" :error-messages="errors.get('amount')" hint="Amount in peso to be loaded." type="number" @input="updateCash"></v-text-field>
                    <v-text-field outline v-model="formData.cash" label="Cash" :error-messages="errors.get('cash')" hint="Cash given by the customer." type="number"></v-text-field>
                    <v-text-field outline :value="change" label="Change" readonly></v-text-field>
                    <v-textarea outline v-model="formData.remarks" label="Remarks" rows="2"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round :loading="saving">save</v-btn>
                    <v-btn @click="close" round>close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'rfidCard'
    ],
    data() {
        return {
            formData: {
                amount: 0,
                cash: 0,
                remarks: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch('rfidcard/topUp', {
                rfidCardId: this.rfidCard.rfid_card_id,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    mode: 'load',
                    rfidCard: res.data.rfidCard
                });
            });
        },
        updateCash() {
            this.formData.cash = this.formData.amount;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['rfidcard/getErrors'];
        },
        saving() {
            return this.$store.getters['rfidcard/isSaving'];
        },
        change() {
            let change = parseFloat(this.formData.cash) - parseFloat(this.formData.amount);
            if(change > 0) {
                return change;
            }
            return 0;
        }
    },
    watch: {
        value(val) {
            if(!val) {
                this.formData.amount = 0;
                this.formData.cash = 0;
                this.formData.remarks = null;
            }
        }
    }
}
</script>
