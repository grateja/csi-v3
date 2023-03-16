<template>
    <v-container>
        <h3 class="title white--text my-3">Daily Report</h3>
        <v-progress-linear v-if="loading" indeterminate class="my-3"></v-progress-linear>
        <v-divider v-else class="my-3"></v-divider>
        <v-card class="translucent rounded-card">
            <v-card-text>
                <h4 class="white--text">NEW CUSTOMERS</h4>

                <h4 class="white--text">JOB ORDERS</h4>
                <v-layout>
                    <v-flex xs1>JO#</v-flex>
                    <v-flex xs3>CUSTOMER</v-flex>
                    <v-flex x1>AMOUNT</v-flex>
                    <v-flex x4>ITEMS</v-flex>
                    <v-flex x3>PAYMENT</v-flex>
                </v-layout>
                <h4 class="white--text">INVENTORY</h4>
                <v-card-text><pre>{{ result }}</pre></v-card-text>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
import moment from 'moment';

export default {
    data() {
        return {
            date: moment().format('YYYY-MM-DD'),
            result: null,
            loading: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/daily-sales', {
                params: {
                    date: this.date,
                }
            }).then((res, rej) => {
                this.result = res.data;
            }).finally(() => {
                this.loading = false;
            })
        }
    },
    created() {
        this.load();
    }
}
</script>