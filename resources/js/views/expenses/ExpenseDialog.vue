<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Expense info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.date" :error-messages="errors.get('date')" outline label="Date" type="date"></v-text-field>
                    <v-text-field v-model="formData.remarks" :error-messages="errors.get('remarks')" outline label="Remarks" ref="remarks"></v-text-field>
                    <v-text-field v-model="formData.amount" :error-messages="errors.get('amount')" outline label="Amount"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'expense'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                date: moment().format('YYYY-MM-DD'),
                remarks: null,
                amount: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`expense/${this.mode}Expense`, {
                expenseId: this.expense ? this.expense.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    expense: res.data.expense,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['expense/getErrors'];
        },
        saving() {
            return this.$store.getters['expense/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.expense) {
                this.mode = 'update';
                this.formData.date = moment(this.expense.date).format('YYYY-MM-DD');
                this.formData.amount = this.expense.amount;
                this.formData.remarks = this.expense.remarks;
            } else {
                this.mode = 'insert';
                this.formData.date = moment().format('YYYY-MM-DD');
                this.formData.remarks = null;
                this.formData.amount = 0;
            }
            setTimeout(() => {
                this.$refs.remarks.$el.querySelector('input').select();
            }, 500);
        },
        expense(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
