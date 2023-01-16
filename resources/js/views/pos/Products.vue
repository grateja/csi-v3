<template>
    <div>
        <v-divider class="my-3"></v-divider>
        <v-layout row wrap>
            <v-flex sm6 md4 lg3 xl2 v-for="product in products" :key="product.id">
                <v-card @click="selectProduct(product)" class="ma-1">
                    <v-card-text>
                        <h3 class="subtitle text-no-wrap text-truncate">{{product.name}}</h3>
                        <v-divider></v-divider>
                        <span class="caption">{{product.description}}</span>
                    </v-card-text>
                    <v-card-actions>
                        <span>P{{product.price}}</span>
                        <v-spacer></v-spacer>
                        <span>{{product.available}} available</span>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
        <quantity-prompt v-model="openQuantityPrompt" @ok="ok" :loading="addingItem"></quantity-prompt>
    </div>
</template>

<script>
import QuantityPrompt from './QuantityPrompt.vue';

export default {
    components: {
        QuantityPrompt
    },
    data() {
        return {
            openQuantityPrompt: false,
            selectedProduct: null
        }
    },
    computed: {
        products() {
            return this.$store.getters['product/getProducts'];
        },
        addingItem() {
            return this.$store.getters['transaction/addingItem'];
        },
        customer() {
            return this.$store.getters['customer/getCustomer'];
        },
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        }
    },
    methods: {
        selectProduct(product) {
            if(product.available <= 0) {
                alert('No available stock');
                return;
            }

            if(this.customer == null) {
                alert('Select customer first.');
                return;
            }

            this.selectedProduct = product;
            this.openQuantityPrompt = true;
        },
        ok(quantity) {
            this.$store.dispatch('transaction/addProduct', {
                transactionId: this.transaction ? this.transaction.id : null,
                formData: {
                    customerId: this.customer.id,
                    productId: this.selectedProduct.id,
                    quantity
                }
            }).then((res, rej) => {
                this.openQuantityPrompt = false;
                this.$store.commit('product/deductProduct', {
                    productId: this.selectedProduct.id,
                    quantity
                });
            });
        }
    }
}
</script>
