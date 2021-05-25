<template>
    <div v-if="expenses" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">EXPENSES</h3>
            </v-flex>
            <v-flex v-if="simplified" xs4 class="text-xs-right title">P{{parseFloat(expenses.total || 0).toFixed(2)}}</v-flex>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2">
                    <v-layout>
                        <v-flex xs6>
                            <span>Product purchases</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{expenses.productPurchases.total_count}}</v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(expenses.productPurchases.total_cost || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout>
                        <v-flex xs6>
                            <span>Other</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{expenses.otherExpenses.total_count}}</v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(expenses.otherExpenses.total_expense || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs6>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{expenses.total}}</v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(expenses.total || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                </div>
            </v-card-text>
        </v-expand-transition>
    </div>
</template>

<script>
export default {
    props: [
        'expenses', 'view'
    ],
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
