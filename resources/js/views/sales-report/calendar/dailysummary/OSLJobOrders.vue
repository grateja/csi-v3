<template>
    <div v-if="osl && oslJobOrders && oslJobOrders.length" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">OSL</h3>
            </v-flex>
            <template v-if="simplified">
                <v-flex xs5 class="text-xs-right title">{{osl | peso }}</v-flex>
            </template>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div v-for="account in oslJobOrders">
                    <h4>
                        <v-layout>
                            <v-flex grow>
                                {{account.company_name}}
                            </v-flex>
                            <v-flex class="text-xs-right">
                                {{ account.total | peso }}
                            </v-flex>
                        </v-layout>

                    </h4>
                    <template v-for="(jobOrder, i) in account.job_orders">
                        <v-layout :key="i" class="ma-2">
                            <v-flex xs1 class="text-xs-center">{{jobOrder.total_quantity}}</v-flex>
                            <v-flex xs9>
                                <span>{{jobOrder.name}} ({{ jobOrder.degree_of_soil }})</span>
                            </v-flex>
                            <v-flex xs2 class="text-xs-right">{{jobOrder.amount | peso }}</v-flex>
                        </v-layout>
                        <!-- <v-divider :key="i+'keme'"></v-divider> -->
                    </template>
                </div>
            </v-card-text>
        </v-expand-transition>
    </div>

</template>

<script>
export default {
    props: [
        'osl', 'view', 'oslJobOrders'
    ],
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}

</script>