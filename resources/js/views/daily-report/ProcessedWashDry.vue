<template>
    <div>
        <div v-for="(list, key) in items" :key="key">
            <v-expand-transition>
                <div v-if="simplified">
                    {{ key }} - ({{ list.length }} {{ 'load' | pluralize(list.length) }})
                </div>
            </v-expand-transition>
            <v-expand-transition>
                <div v-if="!simplified" class="mt-3">
                    <h3 class="text-xs-center">{{key | uppercase}} - ({{ list.length }} {{ 'load' | pluralize(list.length) }})</h3>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs1>JO#</v-flex>
                        <v-flex xs3>CUSTOMER NAME</v-flex>
                        <v-flex xs3>SERVICE</v-flex>
                        <v-flex xs2 class="text-xs-center">TIME ACTIVATED</v-flex>
                        <v-flex xs3 class="text-xs-center">ACTIVATED BY</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <div v-for="item in list" :key="item.id">
                        <v-layout @click="$emit('openJobOrder', item.service_transaction_item.transaction_id)">
                            <v-flex xs1>{{item.job_order}}</v-flex>
                            <v-flex xs3>
                                <span v-if="item.customer_name">{{ item.customer_name }}</span>
                                <span v-else>{{item.customer?.name}}</span>
                            </v-flex>
                            <v-flex xs3>
                                <v-layout>
                                    <v-flex>{{item.service_name}}</v-flex>
                                    <v-flex class="text-xs-right" v-if="item.minutes">{{item.minutes}} min</v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs2 class="text-xs-center">{{item.used | simpleTime}}</v-flex>
                            <v-flex xs3 class="text-xs-center">{{item.staff_name}}</v-flex>
                        </v-layout>
                        <v-divider />
                    </div>
                </div>
            </v-expand-transition>
        </div>
    </div>
</template>

<script>
export default {
    props: ['items', 'simplified'],
    // methods: {
    //     mapped(list) {
    //         return list.sort((a, b) => (a.used > b.used ? 1 : -1))
    //     }
    // }
}
</script>
