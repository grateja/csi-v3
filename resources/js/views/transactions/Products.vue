<template>
    <div>
        <h3 class="white--text mt-5">Products</h3>
        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-layout row wrap v-if="products.length">
            <v-flex v-for="item in products" :key="item.id" xs6 sm4 lg3 xl2>
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer rounded-card translucent" :elevation="hover ? 12 : 2" @click="addProduct(item)">
                        <v-card-text>
                            <v-responsive v-if="item.img_path">
                                <v-img height="100px" :src="item.img_path" max-height="100px" ></v-img>
                            </v-responsive>
                            <div class="text-xs-center ma-1">
                                <div>
                                    {{item.name}}
                                </div>
                                <div class="font-italic grey--text">{{item.description}}</div>
                                <span>{{item.current_stock}} available item(s)</span>
                            </div>
                            <v-progress-linear v-if="item.isAdding" indeterminate height="4" class="my-0"></v-progress-linear>
                            <v-divider v-else class="my-2"></v-divider>
                            <div class="text-xs-center title ma-2">
                                P {{ parseFloat(item.selling_price).toFixed(2)}}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>
        <span v-if="!loading && products.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>
    </div>
</template>
<script>
export default {
    data() {
        return {
            products: [],
            loading: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/pos-transactions/products', {

            }).then((res, rej) => {
                this.products = res.data.products;
            }).finally(() => {
                this.loading = false;
            });
        },
        addProduct(item) {
            if(this.currentCustomer == null) {
                alert('Please specify customer first');
                return;
            }
            if(this.products.find(i => i.isAdding)) {
                return;
            }
            Vue.set(item, 'isAdding', true);
            this.$store.dispatch(`postransaction/addProduct`, {
                itemId: item.id,
                transactionId: this.currentTransaction? this.currentTransaction.id : null,
                customerId: this.currentCustomer? this.currentCustomer.id : null
            }).then((res, rej) => {
                item.current_stock = res.data.product.current_stock;
            }).finally(() => {
                Vue.set(item, 'isAdding', false);
            });
        }
    },
    created() {
        this.load();
    },
    computed: {
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        },
        currentTransaction() {
            return this.$store.getters['postransaction/getCurrentTransaction'];
        }
    }
}
</script>
