<template>
    <div v-if="rfidLoad" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">LOAD TOP UP</h3>
            </v-flex>
            <template v-if="simplified">
                <v-flex xs1 class="text-xs-right title">{{rfidLoad.total_count}}</v-flex>
                <v-flex xs4 class="text-xs-right title">{{rfidLoad.total_price | peso}}</v-flex>
            </template>
        </v-layout>
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2">
                    <v-layout class="ma-2">
                        <v-flex xs1 class="text-xs-center">{{rfidLoad.total_count}}</v-flex>
                        <v-flex xs9>
                            <span>Customer card</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{rfidLoad.total_price | peso}}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs1 class="text-xs-center">{{rfidLoad.total_count}}</v-flex>
                        <v-flex xs9>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs2 class="text-xs-right">{{rfidLoad.total_price | peso}}</v-flex>
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
        'rfidLoad', 'view'
    ],
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
