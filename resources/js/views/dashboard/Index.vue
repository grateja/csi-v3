<template>
    <v-container>
        <v-card flat class="transparent">
            <v-card-actions class="pa-0 ma-0">
                <h3 class="title grey--text">Dashboard</h3>
                <v-spacer></v-spacer>
                <v-btn right @click="get" class="mr-0"><v-icon left>refresh</v-icon> refresh</v-btn>
            </v-card-actions>
        </v-card>
        <v-divider class="my-3"></v-divider>
        <v-progress-linear v-if="loading" indeterminate></v-progress-linear>

        <v-card v-if="!!result">
            <v-card-title>
                <h3 class="grey--text title">Sales overview</h3>
                <v-spacer></v-spacer>
                <v-chip color="green" class="white--text">
                    <v-icon left>expand_less</v-icon> Income
                </v-chip>
                <v-chip color="blue" class="white--text">
                    <v-icon left>expand_more</v-icon> Expenses
                </v-chip>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <sales-overview :months="months" :maxAmount="maxAmount" v-model="activeMonth"></sales-overview>
            </v-card-text>
        </v-card>

        <v-divider class="my-3"></v-divider>

        <income :month="activeMonth" v-if="!!activeMonth" />

         <v-divider class="my-3"></v-divider>
        <expenses :month="activeMonth" v-if="!!activeMonth" />
    </v-container>
</template>

<script>
import SalesOverview from './SalesOverview.vue';
import Expenses from './Expenses.vue';
import Income from './Income.vue';

export default {
    components: {
        SalesOverview,
        Expenses,
        Income
    },
    data() {
        return {
            loading: false,
            dateFrom: this.$route.query.dateFrom,
            dateTo: this.$route.query.dateTo,
            result: null,
            months: null,
            maxAmount: 0,
            activeMonth: null
        }
    },
    methods: {
        get() {
            this.loading = true;
            this.activeMonth = null;
            axios.get('/api/dashboard/self', {
                params: {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
            }).then((res, rej) => {
                this.result = res.data.result;
                this.months = res.data.result.months;
                this.maxAmount = Math.max(res.data.result.maxExpense, res.data.result.maxIncome, 3) + 1;
            }).finally(() => {
                this.loading = false;
            });
        },
        load() {
            console.log(this.activeMonth);
        }
    },
    created() {
        let currentUser = this.$store.getters.getCurrentUser;
        if(currentUser) {
            if(!currentUser.roles.some(r => r == 'admin')) {
                this.$router.push('/pos/products');
                return;
            }
        }
        this.get();
    },
    watch: {
        activeMonth(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
