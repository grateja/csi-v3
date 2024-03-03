<template>
    <v-card v-if="Object.keys(items).length" class="translucent rounded-card mt-2">
        <v-card-title>
            <h4 class="font-weight-bold title">EXPENSES</h4>
            <v-divider></v-divider>
            <!-- <v-btn round small @click="simplified = !simplified">{{simplified ? 'EXPAND' : "SIMPLIFIED"}}</v-btn> -->
        </v-card-title>
        <v-card-text>
            <!-- <div v-for="(items, key) in items" :key="key"> -->
                <!-- <v-expand-transition>
                    <div v-if="simplified">
                        {{ key }} - {{ total(items).count }} {{ 'pc' | pluralize(total(items).count) }}
                    </div>
                </v-expand-transition> -->
                <!-- <v-expand-transition> -->
                    <!-- <div v-if="!simplified"> -->
                        <v-card class="pa-2 transparent" flat></v-card>
                        <v-layout class="font-weight-bold">
                            <v-flex xs3>ADDED BY</v-flex>
                            <v-flex xs3>DATE</v-flex>
                            <v-flex xs3>REMARKS</v-flex>
                            <v-flex xs3>AMOUNT</v-flex>
                        </v-layout>
                        <v-divider />
                        <div v-for="item in items" :key="item.created_at">
                            <v-layout>
                                <v-flex xs3>{{item.staff_name}}</v-flex>
                                <v-flex xs3>{{item.date | simpleDate}}</v-flex>
                                <v-flex xs3>{{item.remarks}}</v-flex>
                                <v-flex xs3>{{item.amount | peso}}</v-flex>
                            </v-layout>
                            <v-divider />
                        </div>
                        <v-divider />
                        <v-layout class="font-weight-bold" v-if="total(items)">
                            <v-flex xs9>TOTAL</v-flex>
                            <!-- <v-flex xs3>{{total(items).count}} {{ 'pc' | pluralize(total(items).count) }}</v-flex> -->
                            <v-flex xs3>{{total(items).amount | peso}}</v-flex>
                        </v-layout>
                    <!-- </div> -->
                <!-- </v-expand-transition> -->
            <!-- </div> -->
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: ['items'],
    data() {
        return {
            simplified: true
        }
    },
    methods: {
        total(items) {
            if(items) {
                // var _items = Object.entries(items);
                var count = items.length;
                var amount = items.reduce((sum, item) => sum + item.amount, 0);
                return {
                    count,
                    amount
                }
            }
        }
    }
}
</script>
