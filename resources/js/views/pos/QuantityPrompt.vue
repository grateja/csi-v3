<template>
    <v-dialog :value="value" persistent max-width="480px">
        <form @submit.prevent="ok">
            <v-card>
                <v-card-title class="title grey--text">Enter quantity</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-text-field type="number" label="Quantity" v-model="quantity" autoselect :error-messages="errors.get('quantity')"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" :loading="loading">Ok</v-btn>
                    <v-btn @click="cancel">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'loading'
    ],
    data() {
        return {
            quantity: 1
        }
    },
    methods: {
        ok() {
            this.$emit('ok', this.quantity);
        },
        cancel() {
            this.$emit('input', false);
            this.$store.commit('transaction/clearErrors');
        }
    },
    computed: {
        errors() {
            return this.$store.getters['transaction/getErrors'];
        }
    }
}
</script>
