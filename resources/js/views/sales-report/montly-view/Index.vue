<template>
    <v-card class="transparent" flat>
        <!-- <pre>{{drafts}}</pre> -->
        <!-- <pre>{{result}}</pre> -->
        <v-card-title class="px-0">
            <v-btn round class="ml-0 translucent" to="/sales-report/yearly-view">
                <v-icon left>
                    chevron_left
                </v-icon>
                Years {{yearsFrom}} to {{yearsUntil}}
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn to="/sales-report/custom-date" class="ma-1 translucent" round>Custom date range</v-btn>
        </v-card-title>
        <navigator @next="next" @prev="prev" :loading="loading" :action="action" :text="year" @browse="browseYear">
            <v-btn icon slot="extraButton" outline class="ml-3" @click="viewYearSummary">
                <v-icon>open_in_new</v-icon>
            </v-btn>
        </navigator>
        <!-- <v-card class="rounded-card translucent">
            <v-card-actions>
                <v-btn icon @click="prev" :loading="loading && action == 'prev'">
                    <v-icon>chevron_left</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn round @click="browseYear">
                    <span class="title grey--text">{{year}}</span>
                    <v-icon right>
                        open_in_new
                    </v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn icon @click="next" :loading="loading && action == 'next'">
                    <v-icon>chevron_right</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card> -->
        <v-divider class="my-4"></v-divider>
        <v-layout row wrap>
            <v-flex xs6 sm4 lg3 xl2 v-for="(draft, i) in drafts" :key="i">
                <v-hover v-slot:default="{ hover }" v-if="draft.active">
                    <v-card :elevation="hover ? 12 : 2" @click="setMonth(draft.monthIndex)" class="pointer ma-1 rounded-card" :class="{'active': draft.current}">
                        <v-card-title class="px-3 py-2 teal white--text font-weight-bold">
                            <span>{{draft.text}}</span>
                            <v-spacer></v-spacer>
                            <v-btn small icon class="ma-0" @click="preview(draft.monthIndex, $event)">
                                <v-icon>open_in_new</v-icon>
                            </v-btn>
                        </v-card-title>
                        <!-- <pre>{{draft}}</pre> -->
                        <!-- <v-card-text> -->
                        <template v-if="draft.active">
                            <v-tooltip top>
                                <v-layout slot="activator" class="caption ma-1">
                                    <v-flex xs2 class="text-xs-right pr-1">
                                        <v-icon small>people</v-icon>
                                    </v-flex>
                                    <v-flex xs10 class="pl-1">
                                        <span class="font-weight-bold">{{draft.newCustomers}}</span>
                                    </v-flex>
                                </v-layout>
                                <span>{{draft.newCustomers}} new customer(s)</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-layout slot="activator" class="caption ma-1">
                                    <v-flex xs2 class="text-xs-right pr-1">
                                        <v-icon small :color="draft.jo_color">bookmark</v-icon>
                                    </v-flex>
                                    <v-flex xs10 class="pl-1">
                                        <div :class="draft.jo_color+ '--text font-weight-bold'">{{draft.jo_count}}</div>
                                    </v-flex>
                                </v-layout>
                                <span>Total number of Job Orders</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-layout slot="activator" class="caption ma-1">
                                    <v-flex xs2 class="text-xs-right pr-1">
                                        <v-icon small>collections_bookmark</v-icon>
                                    </v-flex>
                                    <v-flex xs6>
                                        Total Sales
                                    </v-flex>
                                    <v-flex xs4 class="pl-1">
                                        <span class="font-weight-bold">{{draft.amount}}</span>
                                    </v-flex>
                                </v-layout>
                                <span>Total sales</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-layout slot="activator" class="caption ma-1 red--text">
                                    <v-flex xs2 class="text-xs-right pr-1">
                                        <v-icon small>save</v-icon>
                                    </v-flex>
                                    <v-flex xs6>
                                        <span>Expenses</span>
                                    </v-flex>
                                    <v-flex xs4 class="pl-1">
                                        <span class="font-weight-bold">
                                            {{draft.expenses}}
                                        </span>
                                    </v-flex>
                                </v-layout>
                                <span>Total expenses</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-layout slot="activator" class="caption ma-1">
                                    <v-flex xs2 class="text-xs-right pr-1">
                                        <v-icon small>move_to_inbox</v-icon>
                                    </v-flex>
                                    <v-flex xs6>
                                        <span>Collections</span>
                                    </v-flex>
                                    <v-flex xs4 class="pl-1">
                                        <span class="font-weight-bold">
                                        {{draft.collection}}
                                        </span>
                                    </v-flex>
                                </v-layout>
                                <span>Total collections</span>
                            </v-tooltip>
                            <v-divider></v-divider>
                            <v-tooltip top>
                                <v-layout slot="activator" class="font-weight-bold ma-1 pb-1">
                                    <v-flex xs2 class="text-xs-right pr-1">
                                        <v-icon small>account_balance_wallet</v-icon>
                                    </v-flex>
                                    <v-flex xs6>
                                        <span>Total Deposit</span>
                                    </v-flex>
                                    <v-flex xs4 class="pl-1">
                                        <span>{{draft.totalDeposit}}</span>
                                    </v-flex>
                                </v-layout>
                                <span>Total Deposit</span>
                            </v-tooltip>
                        </template>
                        <!-- </v-card-text> -->
                    </v-card>
                </v-hover>
                <v-card v-else class="ma-1 disabled" flat style="height: 189px">
                    <v-card-title class="px-3 py-2 grey--text font-weight-bold">{{draft.text}}</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="grey--text font-italic">
                        <span v-if="loading">
                            [Loading...]
                        </span>
                        <span v-else>
                            [No data available]
                        </span>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <!-- <custom-range-dialog v-model="openCustomRange" :dateFrom="dateFrom" :dateTo="dateTo" :origin="origin"></custom-range-dialog> -->
        <daily-summary v-model="openCustomRange" :date="dateFrom" :until="dateTo" />
    </v-card>
</template>

<script>
import Navigator from '../Navigator.vue';
import DailySummary from '../calendar/DailySummary.vue';
// import CustomRangeDialog from '../date-range/CustomRangeDialog.vue';
export default {
    components: {
        Navigator,
        DailySummary
        // CustomRangeDialog
    },
    data() {
        return {
            result: null,
            loading: false,
            action: 'default',
            openCustomRange: false,
            dateFrom: null,
            dateTo: null,
            origin: null
        }
    },
    computed: {
        months() {
            return this.$store.getters['transactionreport/getMonths'];
        },
        currentMonth() {
            return new Date().getMonth() + 1;
        },
        currentYear() {
            return new Date().getFullYear();
        },
        yearsFrom() {
            return this.$store.getters['transactionreport/getYearsFrom'];
        },
        yearsUntil() {
            return this.$store.getters['transactionreport/getYearsUntil'];
        },
        year() {
            return this.$store.getters['transactionreport/getYear'];
        },
        drafts() {
            if(this.result) {
                return this.months.map(month => {
                    let _month = this.result.find(m => m.monthIndex == month.monthIndex);
                    if(_month) {
                        return {
                            text: month.text,
                            monthIndex: month.monthIndex,
                            amount: '₱' + parseFloat(_month.amount).toLocaleString(),
                            collection: _month.collection,
                            jo_count: 'J.O.' + _month.paid_jo + '/' + _month.total_jo + ' Paid',
                            expenses: '₱' + parseFloat(_month.expenses).toLocaleString(),
                            newCustomers: _month.newCustomers ? _month.newCustomers + ' new customer' + (_month.newCustomers > 1 ? 's' : '') : 'No new Customer',
                            collection: '₱' + parseFloat(_month.collection).toLocaleString(),
                            totalDeposit: '₱' + parseFloat(_month.totalDeposit).toLocaleString(),
                            active: true,
                            current: _month.monthIndex == this.currentMonth && _month.year == this.currentYear
                        };
                    } else {
                        return month;
                    }
                });
                // let _months = [];
                // for(var index = 0; index < 12; index++) {
                //     let _month = this.result.find(m => m.monthIndex == index + 1);
                //     if(_month) {
                //         _months[index] = {
                //             text:
                //         };
                //     } else {
                //         _months[index] = {
                //             text: index + 1
                //         }
                //     }
                // }
                // return _months;
            }
        }
    },
    methods: {
        setMonth(monthIndex) {
            this.$store.commit('transactionreport/setActiveMonth', monthIndex);
            this.$router.push('/sales-report/calendar-view');
        },
        load() {
            this.result = [];
            this.loading = true;
            axios.get(`/api/sales-report/${this.year}/monthly`).then((res, rej) => {
                this.result = res.data.result;
            }).finally(() => {
                this.loading = false;
                this.action = 'default';
            });
        },
        next() {
            this.action = 'next';
            this.$store.commit('transactionreport/setActiveYear', this.year + 1);
        },
        prev() {
            this.action = 'prev';
            this.$store.commit('transactionreport/setActiveYear', this.year - 1);
        },
        browseYear() {
            this.$router.push('/sales-report/yearly-view');
        },
        preview(monthIndex, e) {
            e.stopPropagation();
            this.dateFrom = moment().set('year', this.year).set('month', monthIndex - 1).startOf('month').format('YYYY-MM-DD');
            this.dateTo = moment().set('year', this.year).set('month', monthIndex - 1).endOf('month').format('YYYY-MM-DD');
            this.openCustomRange = true;
            this.origin = 'Sales for : ' + moment().set('month', monthIndex - 1).format('MMMM') + ', ' + this.year;
        },
        viewYearSummary() {
            this.dateFrom = moment().set('year', this.year).set('month', 0).startOf('month').format('YYYY-MM-DD');
            this.dateTo = moment().set('year', this.year).set('month', 11).endOf('month').format('YYYY-MM-DD');
            this.origin = 'Sales for : ' + this.year //moment().set('year', this.year).format('YYYY');
            this.openCustomRange = true;
        }
    },
    created() {
        // this.$store.commit('transactionreport/setActiveMonth', 0);
        this.load();
    },
    watch: {
        year(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>

<style scoped>
.active {
    border: 2px solid #08ed90!important;
    background-color: #c5ffe8!important;
    transition: .2s;
}
.disabled {
    border: 2px solid rgba(255, 255, 255, .2)!important;
    background-color: rgba(255, 255, 255, 0.1)!important;
    transition: .2s;
}
</style>
