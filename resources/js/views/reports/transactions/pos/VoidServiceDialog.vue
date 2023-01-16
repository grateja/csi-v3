<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="title grey--text">Void service item</v-card-title>
                <v-card-text>
                    <dl v-if="!!serviceTransaction">
                        <dt class="grey--text caption">Job order</dt>
                        <dd class="ml-3">{{serviceTransaction.jobOrder}}</dd>
                        <dt class="grey--text caption">Customer</dt>
                        <dd class="ml-3">{{serviceTransaction.customerName}}</dd>
                        <dt class="grey--text caption">Service name</dt>
                        <dd class="ml-3">{{serviceTransaction.serviceName}}</dd>
                        <dt class="grey--text caption">Price</dt>
                        <dd class="ml-3">{{serviceTransaction.unitPrice}}</dd>
                        <template v-if="serviceTransaction.datePaid">
                            <dt class="grey--text caption">Date paid</dt>
                            <dd class="ml-3">{{serviceTransaction.datePaid}}</dd>
                            <dt class="grey--text caption">Paid to</dt>
                            <dd class="ml-3">P {{serviceTransaction.paidTo}}</dd>
                        </template>
                    </dl>
                    <v-textarea label="Remarks" :error-messages="errors.get('remarks')" v-model="formData.remarks"></v-textarea>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn type="submit" class="primary" :loading="saving">ok</v-btn>
                    <v-btn @click="cancel">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value',
        'serviceTransaction'
    ],
    data() {
        return {
            formData: {
                remarks: ''
            }
        }
    },
    methods: {
        save() {
            this.$store.dispatch('voidtransaction/voidService', {
                completedServiceTransactionId: this.serviceTransaction.id,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('ok', res.data.id);
                this.$emit('input', false);
            })
        },
        cancel() {
            this.$emit('input', false);
            this.$store.commit('voidtransaction/clearErrors');
        }
    },
    computed: {
        errors() {
            return this.$store.getters['voidtransaction/getErrors'];
        },
        saving() {
            return this.$store.getters['voidtransaction/isSaving'];
        }
    }
}
</script>
