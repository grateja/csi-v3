<template>
    <div v-if="usedProducts" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">PRODUCTS</h3>
            </v-flex>
            <template v-if="simplified">
                <v-flex xs1 class="text-xs-right title">{{summary.quantity}}</v-flex>
                <v-flex xs4 class="text-xs-right title">{{summary.total_price | peso }}</v-flex>
            </template>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2" v-if="usedProducts && usedProducts.length">
                    <template v-for="(product, i) in usedProducts">
                        <v-layout :key="i" class="ma-2">
                            <v-flex xs1 class="text-xs-center">{{product.quantity}}</v-flex>
                            <v-flex xs9>
                                <span>{{product.name}}</span>
                            </v-flex>
                            <v-flex xs2 class="text-xs-right">{{product.total_price | peso }}</v-flex>
                        </v-layout>
                        <!-- <v-divider :key="i+'keme'"></v-divider> -->
                    </template>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs1 class="text-xs-center">{{summary.quantity}}</v-flex>
                        <v-flex xs9>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{summary.total_price | peso}}</v-flex>
                    </v-layout>
                </div>
                <div v-else class="text-xs-center">
                    <span class="gray--text caption font-italic">No products used</span>
                </div>
            </v-card-text>
        </v-expand-transition>
    </div>
</template>

<script>
export default {
    props: [
        'usedProducts', 'view'
    ],
    computed: {
        summary() {
            if(this.usedProducts) {
                return {
                    quantity: this.usedProducts.reduce(function(prev, item){
                        return prev + item.quantity;
                    }, 0),
                    total_price: this.usedProducts.reduce(function(prev, item){
                        return prev + item.total_price;
                    },0)
                };
            } else {
                return {
                    quantity: 0,
                    total_price: 0
                }
            }
        },
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
