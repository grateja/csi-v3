<template>
    <div v-if="collections" class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">COLLECTIONS
                    <v-tooltip top>
                        <v-icon slot="activator" color="primary" class="pointer">info</v-icon>
                        <span>Including Job Orders from previous days paid to date</span>
                    </v-tooltip>
                </h3>
            </v-flex>
            <v-flex v-if="simplified" xs4 class="text-xs-right title"> P{{parseFloat(collections.total || 0).toFixed(2)}}</v-flex>
        </v-layout>
        <!-- <span class="ml-3 font-italic grey--text caption">Job orders paid to date including previous transactions</span> -->
        <v-divider v-if="!simplified"></v-divider>
        <v-expand-transition>
            <v-card-text v-if="!simplified">
                <div class="px-2 mx-2">
                    <v-layout>
                        <v-flex xs6>
                            <span>Fully paid Job Orders</span>
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.fullyPaid || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout>
                        <v-flex xs6>
                            <span>Partially paid Job Orders</span>
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.partiallyPaid || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout>
                        <v-flex xs6>
                            <span>Master cards</span>
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.rfidTap || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <!-- <v-divider></v-divider> -->
                    <v-layout>
                        <v-flex xs6>
                            <span>Load transactions</span>
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.rfidLoad || 0).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-divider></v-divider>
                    <v-layout class="font-weight-bold">
                        <v-flex xs6>
                            <span>Total</span>
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.total || 0).toFixed(2)}}</v-flex>
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
        'collections', 'view'
    ],
    computed: {
        simplified() {
            return this.view == 'simplified';
        }
    }
}
</script>
