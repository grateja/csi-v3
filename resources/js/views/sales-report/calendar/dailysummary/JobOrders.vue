<template>
    <div v-if="posSummary" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">JOB ORDERS</h3>
            </v-flex>
            <template v-if="simplified">
                <v-flex xs1 class="text-xs-right title">{{posSummary.pos_transactions.total_jo}}</v-flex>
                <v-flex xs4 class="text-xs-right title">P{{parseFloat(posSummary.pos_transactions.total_sales || 0).toFixed(2)}}</v-flex>
            </template>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2">
                    <v-layout>
                        <v-flex xs4>
                            <span>Fully Paid</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{posSummary.fully_paid.total_jo}}</v-flex>
                        <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.fully_paid.total_sales || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout>
                        <v-flex xs4>
                            <span>Partially Paid</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{posSummary.partial_payments.total_jo}}</v-flex>
                        <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.partial_payments.total_sales || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout>
                        <v-flex xs4>
                            <span>Unpaid</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{posSummary.unpaid.total_jo}}</v-flex>
                        <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.unpaid.total_sales || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs4>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs4 class="text-xs-center">{{posSummary.pos_transactions.total_jo}}</v-flex>
                        <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.pos_transactions.total_sales || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                </div>
                <!-- <v-divider></v-divider> -->
            </v-card-text>
        </v-expand-transition>
    </div>
</template>

<script>
export default {
    props: [
        'posSummary', 'view'
    ],
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
