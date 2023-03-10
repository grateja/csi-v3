<template>
    <div v-if="posSummary" class="px-4">
        <v-hover v-slot:default="{ hover }">
            <v-card :elevation="hover ? 3 : 0" class="pointer" @click="openDialog = true">
                <v-layout class="mt-2">
                    <v-flex>
                        <h3 class="title gray--text">JOB ORDERS</h3>
                    </v-flex>
                    <template v-if="simplified">
                        <v-flex xs1 class="text-xs-right title">{{posSummary.pos_transactions.total_jo}}</v-flex>
                        <v-flex xs4 class="text-xs-right title">{{posSummary.pos_transactions.total_sales | peso}}</v-flex>
                    </template>
                </v-layout>
            </v-card>
        </v-hover>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2">
                    <v-layout class="ma-2">
                        <v-flex xs1 class="text-xs-center">{{posSummary.fully_paid.total_jo}}</v-flex>
                        <v-flex xs9>
                            <span>Fully Paid</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{posSummary.fully_paid.total_sales | peso }}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout class="ma-2">
                        <v-flex xs1 class="text-xs-center">{{posSummary.partial_payments.total_jo}}</v-flex>
                        <v-flex xs9>
                            <span>Partially Paid</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{posSummary.partial_payments.total_sales | peso}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout class="ma-2">
                        <v-flex xs1 class="text-xs-center">{{posSummary.unpaid.total_jo}}</v-flex>
                        <v-flex xs9>
                            <span>Unpaid</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{posSummary.unpaid.total_sales | peso }}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs1 class="text-xs-center">{{posSummary.pos_transactions.total_jo}}</v-flex>
                        <v-flex xs9>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{posSummary.pos_transactions.total_sales | peso}}</v-flex>
                    </v-layout>
                </div>
                <!-- <v-divider></v-divider> -->
            </v-card-text>
        </v-expand-transition>
        <job-orders-dialog v-model="openDialog" :date="date" :until="until" />
    </div>
</template>

<script>
import JobOrdersDialog from '../../../shared/summary-preview/JobOrdersDialog.vue';

export default {
    props: [
        'posSummary', 'view', 'date', 'until'
    ],
    components: {
        JobOrdersDialog
    },
    data() {
        return {
            openDialog: false
        }
    },
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
