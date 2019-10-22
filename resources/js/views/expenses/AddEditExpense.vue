<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="grey--text title">Expense details</v-card-title>
                <v-card-text>
                    <v-text-field type="date" label="Date" v-model="formData.date" :error-messages="errors.get('date')"></v-text-field>
                    <vuetify-autocomplete url="/api/autocomplete/expense-types" v-model="formData.expenseType" label="Expense type"></vuetify-autocomplete>
                    <v-text-field label="Amount" v-model="formData.amount" :error-messages="errors.get('amount')"></v-text-field>
                    <v-textarea label="Remarks" v-model="formData.remarks" :error-messages="errors.get('remarks')"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn class="primary" type="submit" :loading="saving">Save</v-btn>
                    <v-btn @click="close">Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'expense'
    ],
    data() {
        return {
            formData: {
                date: new Date().toISOString().substring(0, 10),
                expenseType: null,
                amount: 0,
                remarks: ''
            },
            mode: 'insert'
        }
    },
    methods: {
        submit() {
            this.$store.dispatch(`expense/${this.mode}Expense`, {
                formData: this.formData,
                expenseId: this.expense ? this.expense.id : null
            }).then((res, rej) => {
                this.$emit('ok', {
                    expense: res.data.expense,
                    mode: this.mode
                });
                this.close();
            });
        },
        close() {
            this.$emit('input', false);
            this.formData.date = new Date().toISOString().substring(0, 10);
            this.formData.expenseType = null;
            this.formData.amount = 0;
            this.formData.remarks = null;
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
        expense(val) {
            if(val) {
                this.mode = 'update';
                this.formData.date = val.date;
                this.formData.amount = val.amount;
                this.formData.remarks = val.remarks;
            } else {
                this.mode = 'insert';
                this.formData.date = null;
                this.formData.amount = null;
                this.formData.remarks = null;
            }
        }
    }
}
</script>
