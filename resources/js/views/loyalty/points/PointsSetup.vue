<template>
    <div>
        <form @submit.prevent="save">
            <v-card max-width="480px">
                <v-card-title class="grey-text title">Loyalty points</v-card-title>
                <v-card-text>
                    <v-text-field :loading="loading" :error-messages="errors.get('amount')" label="Amount in peso for each points" v-model="formData.amount"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" :loading="saving">ok</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            formData: {
                amount: 0
            },
            mode: 'insert',
            loading: false
        }
    },
    methods: {
        get() {
            this.loading = true;
            axios.get('/api/loyalty-points/get').then((res, rej) => {
                this.formData.amount = res.data.loyaltyPoint.amount_in_peso;
                this.mode = 'update';
            }).catch(err => {
                this.formData.amount = 0;
                this.mode = 'insert';
            }).finally(() => {
                this.loading = false;
            });
        },
        save() {
            this.$store.dispatch(`point/${this.mode}Point`, {
                formData: this.formData
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['point/getErrors'];
        },
        saving() {
            return this.$store.getters['point/isSaving'];
        }
    },
    created() {
        this.get();
    }
}
</script>
