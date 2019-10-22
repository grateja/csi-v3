<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="grey--text title">Edit price</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.price" label="Price" :error-messages="errors.get('price')"></v-text-field>
                </v-card-text>
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
        'product'
    ],
    data() {
        return {
            formData: {
                price: 0
            }
        }
    },
    methods: {
        save() {
            this.$store.dispatch('product/updatePrice', {
                productId: this.product.id,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('ok', res.data.product);
                this.$emit('input', false);
            });
        },
        cancel() {
            this.$emit('input', false);
        }
    },
    computed: {
        errors() {
            return this.$store.getters['product/getErrors'];
        },
        saving() {
            return this.$store.getters['product/isSaving'];
        }
    },
    watch: {
        product(val) {
            if(val) {
                this.formData.price = this.product.price;
            } else {
                this.formData.price = 0;
            }
        }
    }
}
</script>
