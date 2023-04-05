<template>
    <v-card v-if="Object.keys(items).length" class="translucent rounded-card mt-2">
        <v-card-title>
            <h4 class="font-weight-bold title">LAGOON SERVICES</h4>
            <v-divider></v-divider>
            <v-btn round small @click="simplified = !simplified">{{simplified ? 'EXPAND' : "SIMPLIFIED"}}</v-btn>
        </v-card-title>
        <v-card-text>
            <div v-for="(list, key) in items" :key="key">
                <v-expand-transition>
                    <div v-if="simplified">
                        {{ key }} - {{ total(list).count }} {{ list[0]?.lagoon_per_kilo_id != null ? 'kilo' : 'pc' | pluralize(total(list).count) }}
                    </div>
                </v-expand-transition>
                <v-expand-transition>
                    <div v-if="!simplified">
                        <v-card class="pa-2 transparent" flat></v-card>
                        <v-layout class="font-weight-bold">
                            <v-flex xs1>JO#</v-flex>
                            <v-flex xs3>CUSTOMER NAME</v-flex>
                            <v-flex xs3>SERVICE</v-flex>
                            <v-flex xs1>QTY</v-flex>
                            <v-flex xs2>PRICE/pc</v-flex>
                            <v-flex xs2>PRICE/sum</v-flex>
                        </v-layout>
                        <v-divider />
                        <div v-for="item in list" :key="item.transaction.created_at" @click="$emit('openJobOrder', item.transaction.id)">
                            <v-layout>
                                <v-flex xs1>{{item.transaction.job_order}}</v-flex>
                                <v-flex xs3>{{item.transaction.customer_name}}</v-flex>
                                <v-flex xs3>{{item.name}}</v-flex>
                                <v-flex xs1>{{item.quantity}} {{ item.lagoon_per_kilo_id != null ? 'kilo' : 'pc' | pluralize(item.quantity) }}</v-flex>
                                <v-flex xs2>{{item.price | peso}}</v-flex>
                                <v-flex xs2>{{item.total | peso}}</v-flex>
                            </v-layout>
                            <v-divider />
                        </div>
                        <v-divider />
                        <v-layout class="font-weight-bold" v-if="total(list)">
                            <v-flex xs7>TOTAL</v-flex>
                            <v-flex xs3>{{total(list).count}} {{  list[0]?.lagoon_per_kilo_id != null ? 'kilo' : 'pc' | pluralize(total(list).count) }}</v-flex>
                            <v-flex xs2>{{total(list).amount | peso}}</v-flex>
                        </v-layout>
                    </div>
                </v-expand-transition>
            </div>
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
        total(list) {
            if(list) {
                var count = list.reduce((sum, item) => sum + item.quantity, 0);
                var amount = list.reduce((sum, item) => sum + item.total, 0);
                return {
                    count,
                    amount
                }
            }
        }
    }
}
</script>
