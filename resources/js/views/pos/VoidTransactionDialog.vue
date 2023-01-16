<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="title grey--text">Void transaction</v-card-title>
                <v-progress-linear indeterminate v-if="loading" height="3"></v-progress-linear>
                <v-divider v-else></v-divider>
                <v-card-text>
                    <dl v-if="!!transaction">
                        <dt class="grey--text caption">Job order</dt>
                        <dd class="ml-3">{{transaction.job_order}}</dd>
                        <dt class="grey--text caption">Customer</dt>
                        <dd class="ml-3">{{transaction.customerName}}</dd>
                        <dt class="grey--text caption">Date</dt>
                        <dd class="ml-3">{{date(transaction.date)}}</dd>
                        <template v-if="transaction.datePaid">
                            <dt class="grey--text caption">Date paid</dt>
                            <dd class="ml-3">{{ date(transaction.datePaid)}}</dd>
                            <dt class="grey--text caption">Paid to</dt>
                            <dd class="ml-3">P {{transaction.paidTo}}</dd>
                        </template>
                    </dl>
                    <v-textarea label="Remarks" :error-messages="errors.get('remarks')" v-model="formData.remarks"></v-textarea>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn type="submit" class="primary" :loading="saving">ok</v-btn>
                    <v-btn @click="close">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>
<script>
import moment from 'moment';
export default {
    props: [
        'value',
        'transactionId'
    ],
    data() {
        return {
            formData: {
                remarks: ''
            },
            loading: false
        }
    },
    methods: {
        save() {
            this.$store.dispatch('voidtransaction/voidTransaction', {
                transactionId: this.transaction.id,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('ok', res.data.id);
                this.close();
            })
        },
        close() {
            this.$emit('input', false);
            this.$store.commit('voidtransaction/clearErrors');
            this.$store.commit('transaction/clearTransaction');
        },
        date(date) {
            return moment(date).format('LLL')
        },
        get() {
            this.loading = true;
            axios.get(`/api/void-transaction/${this.transactionId}`).then((res, rej) => {
                this.$store.commit('transaction/setCurrentTransaction', res.data.transaction);
                console.log(res.data);
            }).finally(() => {
                this.loading = false;
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['voidtransaction/getErrors'];
        },
        saving() {
            return this.$store.getters['voidtransaction/isSaving'];
        },
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        },
    },
    watch: {
        value(val) {
            if(val && this.transactionId && !this.transaction) {
                this.get();
            }
        }
    }
}
</script>
