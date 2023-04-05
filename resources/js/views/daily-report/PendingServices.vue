<template>
    <div>
        <div v-if="items && items.length">
            <v-layout class="font-weight-bold">
                <!-- <v-flex xs1></v-flex> -->
                <v-flex xs2>DATE</v-flex>
                <v-flex xs1>JO#</v-flex>
                <v-flex xs3>CUSTOMER</v-flex>
                <v-flex xs3>AVAILABLE WASHES</v-flex>
                <v-flex xs3>AVAILABLE DRIES</v-flex>
            </v-layout>
            <v-divider />
            <div v-for="(item, count) in items" :key="item.id">
                <v-layout @click="$emit('openJobOrder', item.id)">
                    <!-- <v-flex>{{ count + 1 }}</v-flex> -->
                    <v-flex xs2>{{item.date | simpleDate}}</v-flex>
                    <v-flex xs1>{{item.job_order}}</v-flex>
                    <v-flex xs3>{{item.customer_name}}</v-flex>
                    <v-flex xs3>{{item.customer_washes_count}} load(s)</v-flex>
                    <v-flex xs3>{{item.customer_dries_count}} load(s)</v-flex>
                </v-layout>
                <v-divider />
            </div>
            <div v-if="sum">
                <v-layout class="font-weight-bold">
                    <!-- <v-flex xs1>{{item.date | simpleDate}}</v-flex> -->
                    <!-- <v-flex xs1>{{item.job_order}}</v-flex> -->
                    <v-flex xs6>Total</v-flex>
                    <v-flex xs3>{{sum.totalWash}} load(s)</v-flex>
                    <v-flex xs3>{{sum.totalDry}} load(s)</v-flex>
                </v-layout>
                <v-divider />
            </div>
        </div>
        <div v-else>
            NO DATA AVAILABLE
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'items',
    ],
    data() {
        return {

        }
    },
    computed: {
        sum() {
            if(this.items) {
                return {
                    totalWash: this.items.reduce((sum, item) => sum + parseFloat(item.customer_washes_count), 0),
                    totalDry: this.items.reduce((sum, item) => sum + parseFloat(item.customer_dries_count), 0)
                }
            }
        }
    }
}
</script>
