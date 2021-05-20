<template>
    <div>
        <v-card-title class="px-0">
            <v-spacer></v-spacer>
            <v-btn to="/sales-report/custom-date" class="ma-1 translucent" round>Custom date range</v-btn>
        </v-card-title>

        <v-layout row wrap v-if="result" align-center>
            <v-flex xs6 sm4 lg3 xl2 class="text-xs-left">
                <v-btn @click="prev" class="ma-1 translucent" round :loading="loading && action == 'prev'">
                    <v-icon>chevron_left</v-icon>
                    {{yearsFrom - 10}} to {{yearsFrom - 1}}
                </v-btn>
            </v-flex>
            <v-flex xs6 sm4 lg3 xl2 v-for="(draft, i) in drafts" :key="i">
                <v-hover v-slot:default="{ hover }" v-if="draft.active">
                    <v-card :elevation="hover ? 12 : 2"  @click="setYear(draft.year)" class="pointer ma-1 rounded-card" :class="{'active': draft.current}">
                        <v-card-title class="px-3 py-2 teal white--text font-weight-bold">{{draft.year}}
                            <v-spacer></v-spacer>
                            <v-btn small icon class="ma-0" @click="preview(draft.year, $event)">
                                <v-icon>open_in_new</v-icon>
                            </v-btn>
                        </v-card-title>


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


                        <!-- <pre>{{draft}}</pre> -->
                    </v-card>
                </v-hover>
                <v-card v-else class="ma-1 disabled" flat style="height: 189px">
                    <v-card-title class="px-3 py-2 grey--text font-weight-bold">{{draft.year}}</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="grey--text font-italic">
                        [No data]
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex xs6 sm4 lg3 xl2 class="text-xs-right">
                <v-btn @click="next" class="ma-1 translucent" round :loading="loading && action == 'next'">
                    {{yearsUntil + 1}} to {{yearsUntil + 10}}
                    <v-icon>chevron_right</v-icon>
                </v-btn>
            </v-flex>
        </v-layout>
        <custom-range-dialog v-model="openCustomRange" :dateFrom="dateFrom" :dateTo="dateTo"></custom-range-dialog>
    </div>
</template>
<script>
import CustomRangeDialog from '../date-range/CustomRangeDialog.vue';
export default {
    components: {
        CustomRangeDialog
    },
    data() {
        return {
            result: null,
            loading: false,
            action: 'default',
            dateFrom: null,
            dateTo: null,
            openCustomRange: false
        }
    },
    methods: {
        setYear(year) {
            this.$store.commit('transactionreport/setActiveYear', year);
            this.$router.push('/sales-report/monthly-view');
        },
        next() {
            this.action = 'next';
            this.$store.commit('transactionreport/setActiveYear', this.year + 10);
        },
        prev() {
            this.action = 'prev';
            this.$store.commit('transactionreport/setActiveYear', this.year - 10);
        },
        load() {
            this.loading = true;
            this.result = [];
            axios.get(`/api/sales-report/${this.yearsFrom}/${this.yearsUntil}/yearly`).then((res, rej) => {
                this.result = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        preview(year, e) {
            e.stopPropagation();
            this.dateFrom = moment().set('year', year).set('month', 0).startOf('month').format('YYYY-MM-DD');
            this.dateTo = moment().set('year', year).set('month', 11).endOf('month').format('YYYY-MM-DD');
            this.openCustomRange = true;
        }
    },
    computed: {
        year() {
            return this.$store.getters['transactionreport/getYear'];
        },
        years() {
            var _years = [];
            for(var i = this.yearsFrom; i <= this.yearsUntil; i++) {
                _years.push(i);
            }
            return _years;
        },
        yearsFrom() {
            return this.$store.getters['transactionreport/getYearsFrom'];
            // return Math.floor(this.currentYear / 10) * (10);
            // return this.years[0];
        },
        yearsUntil() {
            return this.$store.getters['transactionreport/getYearsUntil'];
            // return Math.ceil(this.currentYear / 10) * (10);
            // return this.years[this.years.length - 1];
        },
        currentYear() {
            // return new Date().getFullYear();
        },
        drafts() {
            if(this.result) {
                return this.years.map((y) => {
                    var _year = this.result.find(_y => _y.year == y);
                    if(_year) {
                        return {
                            amount: '₱' + parseFloat(_year.amount).toLocaleString(),
                            collection: _year.collection,
                            jo_count: 'J.O.' + _year.paid_jo + '/' + _year.total_jo + ' Paid',
                            expenses: '₱' + parseFloat(_year.expenses).toLocaleString(),
                            newCustomers: _year.newCustomers ? _year.newCustomers + ' new customer' + (_year.newCustomers > 1 ? 's' : '') : 'No new Customer',
                            collection: '₱' + parseFloat(_year.collection).toLocaleString(),
                            totalDeposit: '₱' + parseFloat(_year.totalDeposit).toLocaleString(),
                            year: y,
                            current: _year.year == this.currentYear,
                            active: true
                        }
                    } else {
                        return {
                            year: y,
                            current: false,
                            active: false
                        }
                    }
                });
            }
        }
    },
    created() {
        this.load();
    },
    watch: {
        year() {
            this.load();
        }
    }
}
</script>

<style scoped>
.active {
    border: 2px solid #08ed90!important;
    background: #c5ffe8!important;
}
.disabled {
    border: 2px solid rgba(255, 255, 255, .2)!important;
    background: rgba(255, 255, 255, 0.1)!important;
}
</style>
