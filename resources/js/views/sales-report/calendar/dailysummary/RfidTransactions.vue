<template>
    <div v-if="rfidCard" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">TAP CARD</h3>
            </v-flex>
            <template v-if="simplified">
                <v-flex class="text-xs-right title">{{rfidCard.cycle_count}}</v-flex>
                <v-flex xs4 class="text-xs-right title">{{rfidCard.total_price | peso}}</v-flex>
            </template>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="rfidCard && !simplified">
                <div class="px-2 mx-2">
                    <v-layout class="ma-2">
                        <v-flex xs10>
                            <span>Master card</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{rfidCard.users_card | peso }}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout>
                        <v-flex xs10>
                            <span>Customer card</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{rfidCard.customers_card | peso}}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs10>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{rfidCard.total_price | peso}}</v-flex>
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
        'rfidCard', 'view'
    ],
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
