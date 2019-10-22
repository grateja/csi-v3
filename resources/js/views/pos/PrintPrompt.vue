<template>
    <v-dialog :value="value" max-width="480" persistent>
        <v-card>
            <v-card-text>
                Transaction saved successfully!<br>
                Do you want to print receipt?
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="ok" class="primary" :loading="printing">Yes</v-btn>
                <v-btn @click="close">No</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value'
    ],
    methods: {
        ok() {
            this.$store.dispatch('printer/printReceipt', {
                transactionId: this.transaction.id
            }).then((re, rej) => {
                this.close();
            });
        },
        close() {
            this.$emit('input', false);
            this.$emit('close');
        },
        cancel() {
            this.close();
        }
    },
    computed: {
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        },
        printing() {
            return this.$store.getters['printer/isLoading'];
        }
    }
}
</script>
