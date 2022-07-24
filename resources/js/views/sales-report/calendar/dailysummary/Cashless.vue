<template>
    <div v-if="cashless" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">CASHLESS PAYMENT</h3>
            </v-flex>
            <template v-if="simplified">
                <v-flex xs1 class="text-xs-right title">{{summary.quantity}}</v-flex>
                <v-flex xs4 class="text-xs-right title">P{{parseFloat(summary.total_price || 0).toFixed(2)}}</v-flex>
            </template>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2" v-if="cashless && cashless.length">
                    <template v-for="(item, i) in cashless">
                        <v-layout :key="i">
                            <v-flex xs4>
                                <span>{{item.cash_less_provider}}</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{item.quantity}}</v-flex>
                            <v-flex xs4 class="text-xs-right">P{{parseFloat(item.amount || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <!-- <v-divider :key="i+'keme'"></v-divider> -->
                    </template>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs4>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{summary.quantity}}</v-flex>
                        <v-flex xs4 class="text-xs-right">P{{parseFloat(summary.total_price || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                </div>
                <div v-else class="text-xs-center">
                    <span class="gray--text caption font-italic">No services used</span>
                </div>
            </v-card-text>
        </v-expand-transition>
    </div>
</template>

<script>
export default {
    props: [
        'cashless', 'view'
    ],
    computed: {
        summary() {
            if(this.cashless) {
                return {
                    quantity: this.cashless.reduce(function(prev, item){
                        return prev + item.quantity;
                    }, 0),
                    total_price: this.cashless.reduce(function(prev, item){
                        return prev + item.amount;
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
