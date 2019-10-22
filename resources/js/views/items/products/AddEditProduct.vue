<template>
    <v-card>
        <product-form @submit="submit" @cancel="cancel"></product-form>
    </v-card>
</template>

<script>
import ProductForm from './ProductForm.vue';
export default {
    components : {
        ProductForm
    },
    methods: {
        submit(data) {
            this.$store.dispatch(`product/${data.mode}Product`, data).then((res, rej) => {
                this.$router.push('/items/product-list');
                console.log(res.data);
            });
            console.log('data', data);
        },
        cancel() {
            this.$router.push('/items/product-list');
        }
    }
}
</script>
