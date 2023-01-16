<template>
    <v-dialog :value="value" persistent max-width="580">
        <v-card>
            <v-card-title class="title grey--text">Card details</v-card-title>
            <v-progress-linear class="my-0" height="3" v-if="loading" indeterminate></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-card-text v-if="data">
                <v-layout>
                    <v-flex xs5><span class="data-term font-weight-bold">RFID:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">{{data.rfid}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term font-weight-bold">Balance:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">{{data.balance | peso}}</span></v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5><span class="data-term font-weight-bold">Card owner:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">{{data.owner_name}}</span></v-flex>
                </v-layout>
                <v-layout v-if="data.customer">
                    <v-flex xs5><span class="data-term font-weight-bold">Organization:</span></v-flex>
                    <v-flex xs7><span class="data-value font-weight-bold">{{data.customer.organization ? data.customer.organization : 'N/A'}}</span></v-flex>
                </v-layout>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn round @click="close">close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import StoreHoursDialog from '../shop-preferences/StoreHoursDialog.vue';

export default {
    components: {
        StoreHoursDialog
    },
    props: [
        'value', 'rfid'
    ],
    data() {
        return {
            loading: false,
            data: null
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get(`/api/rfid-cards/details/${this.rfid}`).then((res, rej) => {
                this.data = res.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        close() {
            this.$emit('input', false);
        },
    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            } else {
                this.data = null;
            }
        }
    }
}
</script>
