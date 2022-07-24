<template>
    <v-expand-transition>
        <v-card-text v-if="!!machine && !!washDry">
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Job Order:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{washDry.job_order}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Name:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{washDry.customer.name}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Service name:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{washDry.service_name}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Machine name:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{washDry.dryer_name || washDry.washer_name}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Minutes:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{washDry.minutes}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Price:</div></v-flex>
                <v-flex xs8><div class="ma-1">P {{parseFloat(washDry.price).toFixed(2)}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Time activated:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{moment(washDry.used).format('LLL')}}</div></v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs4><div class="text-xs-right ma-1">Activated by:</div></v-flex>
                <v-flex xs8><div class="ma-1">{{washDry.staff_name}}</div></v-flex>
            </v-layout>
        </v-card-text>
    </v-expand-transition>
</template>

<script>
export default {
    props: [
        'machine'
    ],
    data() {
        return {
            washDry: null
        }
    },
    methods: {
        getWashDry() {
            var action = null;
            var customerWashDryId = null;
            if(this.machine.machine_type == 'td' || this.machine.machine_type == 'rd') {
                // dryers
                action = 'customer-dry';
                customerWashDryId = this.machine.customer_dry_id;
            } else {
                // washers
                action = 'customer-wash';
                customerWashDryId = this.machine.customer_wash_id;
            }
            this.$emit('loadingStarted');
            axios.get(`/api/re-works/${action}/${customerWashDryId}`).then((res, rej) => {
                this.washDry = res.data.result;
                this.$emit('load', this.washDry);
            }).catch(err => {
                this.$emit('load', null);
            });
        },
    },
    watch: {
        machine(val) {
            if(val) {
                this.getWashDry();
            } else {
                this.washDry = null;
            }
        }
    }
}
</script>
