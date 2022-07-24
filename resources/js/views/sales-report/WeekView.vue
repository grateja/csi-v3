<template>
    <div>
        <v-divider class="my-2 transparent"></v-divider>
        <v-card class="rounded-card translucent py-3">
            <v-card v-for="draft in result" :key="draft.label" class="my-4" flat color="transparent">
                <div class="grey--text ma-1 text-xs-center">{{month}} {{draft.label}}, {{year}}</div>
                <v-divider></v-divider>
                <v-layout row wrap>
                    <v-flex xs2>
                        <div class="grey--text text-xs-center">
                            <v-icon>people</v-icon> New customers</div>
                        <v-divider class="my-1"></v-divider>
                        <div class="title text-xs-center">{{draft.newCustomers}}</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="grey--text text-xs-center">
                            <v-icon>bookmarks</v-icon> Paid Job Orders</div>
                        <v-divider class="my-1"></v-divider>
                        <div class="title text-xs-center">{{draft.paid_jo}}/{{draft.total_jo}}</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="grey--text text-xs-center">
                            <v-icon>collections_bookmark</v-icon> Sales</div>
                        <v-divider class="my-1"></v-divider>
                        <div class="title text-xs-center">P {{parseFloat(draft.amount).toLocaleString()}}</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="grey--text text-xs-center">
                            <v-icon>move_to_inbox</v-icon> Collections</div>
                        <v-divider class="my-1"></v-divider>
                        <div class="title text-xs-center">P {{parseFloat(draft.collections).toLocaleString()}}</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="grey--text text-xs-center">
                            <v-icon>save</v-icon> Expenses</div>
                        <v-divider class="my-1"></v-divider>
                        <div class="title text-xs-center">P {{parseFloat(draft.expenses).toLocaleString()}}</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="grey--text text-xs-center">
                            <v-icon>account_balance_wallet</v-icon> Total Deposit</div>
                        <v-divider class="my-1"></v-divider>
                        <div class="title text-xs-center">P {{parseFloat(draft.totalDeposit).toLocaleString()}}</div>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-card>
        <v-card v-if="!!summary" class="rounded-card translucent py-3">
            <div class="grey--text ma-2 text-xs-center font-weight-bold">Summary</div>
            <v-divider></v-divider>
            <v-layout row wrap>
                <v-flex xs2>
                    <div class="grey--text text-xs-center">
                        <v-icon>people</v-icon> New customers</div>
                    <v-divider class="my-1"></v-divider>
                    <div class="title text-xs-center">{{summary.totalNewCustomers}}</div>
                </v-flex>
                <v-flex xs2>
                    <div class="grey--text text-xs-center">
                        <v-icon>bookmarks</v-icon> Paid Job Orders</div>
                    <v-divider class="my-1"></v-divider>
                    <div class="title text-xs-center">{{summary.paid_jo}}/{{summary.total_jo}}</div>
                </v-flex>
                <v-flex xs2>
                    <div class="grey--text text-xs-center">
                        <v-icon>collections_bookmark</v-icon> Sales</div>
                    <v-divider class="my-1"></v-divider>
                    <div class="title text-xs-center">P {{parseFloat(summary.totalSales).toLocaleString()}}</div>
                </v-flex>
                <v-flex xs2>
                    <div class="grey--text text-xs-center">
                        <v-icon>move_to_inbox</v-icon> Collections</div>
                    <v-divider class="my-1"></v-divider>
                    <div class="title text-xs-center">P {{parseFloat(summary.totalCollections).toLocaleString()}}</div>
                </v-flex>
                <v-flex xs2>
                    <div class="grey--text text-xs-center">
                        <v-icon>save</v-icon> Expenses</div>
                    <v-divider class="my-1"></v-divider>
                    <div class="title text-xs-center">P {{parseFloat(summary.expenses).toLocaleString()}}</div>
                </v-flex>
                <v-flex xs2>
                    <div class="grey--text text-xs-center">
                        <v-icon>account_balance_wallet</v-icon> Total Deposit</div>
                    <v-divider class="my-1"></v-divider>
                    <div class="title text-xs-center">P {{parseFloat(summary.totalDeposit).toLocaleString()}}</div>
                </v-flex>
            </v-layout>
        </v-card>
    </div>
</template>

<script>
import MonthSelector from './MonthSelector.vue';
export default {
    components: {
        MonthSelector
    },
    data(){
        return {
            openMonthSelector: false,
            openYearSelector: false,
            loading: false,
            result: [],
            summary: null,
            headers: [
                {
                    text: 'Days (Mon. to Fri.)',
                    sortable:false
                },
                {
                    text: 'Paid JO',
                    sortable:false
                },
                {
                    text: 'Total JO',
                    sortable:false
                },
                {
                    text: 'Sales',
                    sortable:false
                },
                {
                    text: 'Expenses',
                    sortable:false
                },
                {
                    text: 'New customers',
                    sortable:false
                }
            ]
        }
    },
    methods: {
        selectMonth(monthIndex) {
            this.dateContext = moment(this.dateContext).set('month',monthIndex);
            this.emitMonth();
        },
        selectYear(year) {
            this.dateContext = moment(this.dateContext).set('year', year);
            this.emitMonth();
        },
        load() {
            this.loading = true;
            axios.get(`/api/sales-report/${this.monthIndex}/${this.year}/week-view`).then((res, rej) => {
                this.result = res.data.result;
                this.summary = res.data.summary;
            }).finally(() => {
                this.loading = false;
            })
        },
    },
    computed: {
        dateContext() {
            let currentDate = moment();
            let currentMonth = this.$route.query.monthIndex || currentDate.format('M');
            let currentYear = this.$route.query.year || currentDate.format('YYYY');
            return moment().set('month', currentMonth - 1).set('year', currentYear);
        },
        year() {
            return moment(this.dateContext).format('YYYY')
        },
        monthIndex() {
            return moment(this.dateContext).format('M')
        },
        month() {
            return moment(this.dateContext).format('MMMM')
        }
    },
    watch: {
        dateContext: {
            deep: true,
            immediate: true,
            handler(val) {
                if(val) {
                    this.load();
                }
            }
        }
    }
}
</script>
