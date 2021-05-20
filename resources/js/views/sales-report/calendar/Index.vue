<template>
    <div>

        <v-card-title class="px-0">
            <v-btn round class="ml-0 translucent" to="/sales-report/monthly-view">
                <v-icon left>
                    chevron_left
                </v-icon>
                January to December {{year}}
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn to="/sales-report/custom-date" class="ma-1 translucent" round>Custom date range</v-btn>
        </v-card-title>
        <navigator @next="next" @prev="prev" :loading="loading" :action="action" :text="activeMonth + ' ' + year" @browse="browseMonth">
            <v-btn icon slot="extraButton" outline class="ml-3" @click="viewMonthSummary">
                <v-icon>open_in_new</v-icon>
            </v-btn>
        </navigator>
        <v-progress-linear indeterminate v-if="loading" height="3" class="my-4"></v-progress-linear>
        <v-divider class="my-4" v-else></v-divider>

        <div class="calendar">
            <div class="main-container">
                <v-layout class="day-of-week">
                    <v-flex class="col7-custom" v-for="(day, i) in days" :key="i + 'w'">
                        <h3 class="font-weight-bold text-xs-left pa-2"><span>{{day}}</span></h3>
                    </v-flex>
                    <v-flex>
                        <h3 class="sunday pa-2">Sunday</h3>
                    </v-flex>
                </v-layout>


                <v-layout row wrap v-if="results">
                    <template v-if="firstDayOfMonth > 0">
                        <v-flex v-for="(blank, i) in firstDayOfMonth" :key="i + 'd'" class="blank col7-custom">
                            <div class="blank day"></div>
                        </v-flex>
                    </template>
                    <template v-for="draft in drafts">
                        <v-flex v-if="draft.active" class="col7-custom active" :key="draft.date" :class="{'sunday' : draft.dayOfWeek == 'Sunday', 'today': draft.today }">
                            <div class="day" @click="preview(draft.date)">
                                <div class="ma-0 px-3 date">{{draft.date}}</div>
                                <v-tooltip top v-if="draft.newCustomers">
                                    <v-layout slot="activator" class="caption">
                                        <v-flex xs3 class="text-xs-right pr-1">
                                            <v-icon small>people</v-icon>
                                        </v-flex>
                                        <v-flex xs9 class="pl-1">
                                            <span class="font-weight-bold">{{draft.newCustomers}}</span>
                                        </v-flex>
                                    </v-layout>
                                    <span>{{draft.newCustomers}} new customer(s)</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <v-layout slot="activator" class="caption">
                                        <v-flex xs3 class="text-xs-right pr-1">
                                            <v-icon small :color="draft.jo_color">bookmark</v-icon>
                                        </v-flex>
                                        <v-flex xs9 class="pl-1">
                                            <div :class="draft.jo_color+ '--text font-weight-bold'">{{draft.jo_count}}</div>
                                        </v-flex>
                                    </v-layout>
                                    <span>Total number of Job Orders</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <v-layout slot="activator" class="caption">
                                        <v-flex xs3 class="text-xs-right pr-1">
                                            <v-icon small>collections_bookmark</v-icon>
                                        </v-flex>
                                        <v-flex xs9 class="pl-1">
                                            <span class="font-weight-bold">{{draft.amount}}</span>
                                        </v-flex>
                                    </v-layout>
                                    <span>Total sales</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <v-layout slot="activator" class="caption red--text">
                                        <v-flex xs3 class="text-xs-right pr-1">
                                            <v-icon small>save</v-icon>
                                        </v-flex>
                                        <v-flex xs9 class="pl-1">
                                            <span class="font-weight-bold">
                                                {{draft.expenses}}
                                            </span>
                                        </v-flex>
                                    </v-layout>
                                    <span>Total expenses</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <v-layout slot="activator" class="caption">
                                        <v-flex xs3 class="text-xs-right pr-1">
                                            <v-icon small>move_to_inbox</v-icon>
                                        </v-flex>
                                        <v-flex xs9 class="pl-1">
                                            <span class="font-weight-bold">
                                            {{draft.collection}}
                                            </span>
                                        </v-flex>
                                    </v-layout>
                                    <span>Total collections</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <v-layout slot="activator" class="font-weight-bold">
                                        <v-flex xs3 class="text-xs-right pr-1">
                                            <v-icon small>account_balance_wallet</v-icon>
                                        </v-flex>
                                        <v-flex xs9 class="pl-1">
                                            <span class="font-italic">{{draft.totalDeposit}}</span>
                                        </v-flex>
                                    </v-layout>
                                    <span>Total Deposit</span>
                                </v-tooltip>
                                <!-- <pre>{{d}}</pre> -->
                            </div>
                        </v-flex>
                        <v-flex v-else :key="draft.date" class="col7-custom inactive" :class="{'sunday' : draft.dayOfWeek == 'Sunday', 'today': draft.today }">
                            <div class="day">
                                <div class="ma-0  px-3 date">{{draft.date}}</div>
                            </div>
                        </v-flex>
                    </template>
                </v-layout>
            </div>
        </div>

        <!-- <pre>{{cumulative}}</pre> -->



        <!-- <div class="calendar">
            <div class="main-container">
                <ul class="weekdays">
                    <li v-for="(day, i) in days" :key="i + 'w'">
                        <h3 class="font-weight-bold text-xs-center py-2"><span>{{day}}</span></h3>
                    </li>
                    <li class="sunday">
                        <h3 class="font-weight-bold text-xs-center py-2"><span>Sun</span></h3>
                    </li>
                </ul>
                <ul class="dates">
                    <template v-if="firstDayOfMonth > 0">
                        <li v-for="(blank, i) in firstDayOfMonth" :key="i + 'd'" class="blank">&nbsp;</li>
                    </template>
                    <template v-for="draft in drafts">
                        <li v-if="draft" :key="draft.date" :class="{'sunday' : draft.dayOfWeek == 'Sunday', 'current-day green': draft.date == 'dateContext' &amp;&amp; monthIndex == initialMonth && year == initialYear}">
                            <template v-if="draft.active">
                                <v-hover v-slot:default="{ hover }" v-if="draft.active">
                                    <v-card class="ma-0 pointer translucent day" :elevation="hover ? 6 : 0" flat @click="preview(draft.date)" height="147px">
                                        <span class="pa-2 grey--text font-weigth-bold">{{draft.date}}</span>
                                        <v-tooltip top v-if="draft.newCustomers">
                                            <v-layout slot="activator" class="caption">
                                                <v-flex xs3 class="text-xs-right pr-1">
                                                    <v-icon small>people</v-icon>
                                                </v-flex>
                                                <v-flex xs9 class="pl-1">
                                                    <span class="font-weight-bold">{{draft.newCustomers}}</span>
                                                </v-flex>
                                            </v-layout>
                                            <span>{{draft.newCustomers}} new customer(s)</span>
                                        </v-tooltip>
                                        <v-tooltip top>
                                            <v-layout slot="activator" class="caption">
                                                <v-flex xs3 class="text-xs-right pr-1">
                                                    <v-icon small :color="draft.jo_color">bookmark</v-icon>
                                                </v-flex>
                                                <v-flex xs9 class="pl-1">
                                                    <div :class="draft.jo_color+ '--text font-weight-bold'">{{draft.jo_count}}</div>
                                                </v-flex>
                                            </v-layout>
                                            <span>Total number of Job Orders</span>
                                        </v-tooltip>
                                        <v-tooltip top>
                                            <v-layout slot="activator" class="caption">
                                                <v-flex xs3 class="text-xs-right pr-1">
                                                    <v-icon small>collections_bookmark</v-icon>
                                                </v-flex>
                                                <v-flex xs9 class="pl-1">
                                                    <span class="font-weight-bold">{{draft.amount}}</span>
                                                </v-flex>
                                            </v-layout>
                                            <span>Total sales</span>
                                        </v-tooltip>
                                        <v-tooltip top>
                                            <v-layout slot="activator" class="caption red--text">
                                                <v-flex xs3 class="text-xs-right pr-1">
                                                    <v-icon small>save</v-icon>
                                                </v-flex>
                                                <v-flex xs9 class="pl-1">
                                                    <span class="font-weight-bold">
                                                        {{draft.expenses}}
                                                    </span>
                                                </v-flex>
                                            </v-layout>
                                            <span>Total expenses</span>
                                        </v-tooltip>
                                        <v-tooltip top>
                                            <v-layout slot="activator" class="caption">
                                                <v-flex xs3 class="text-xs-right pr-1">
                                                    <v-icon small>move_to_inbox</v-icon>
                                                </v-flex>
                                                <v-flex xs9 class="pl-1">
                                                    <span class="font-weight-bold">
                                                    {{draft.collection}}
                                                    </span>
                                                </v-flex>
                                            </v-layout>
                                            <span>Total collections</span>
                                        </v-tooltip>
                                        <v-tooltip top>
                                            <v-layout slot="activator" class="font-weight-bold">
                                                <v-flex xs3 class="text-xs-right pr-1">
                                                    <v-icon small>account_balance_wallet</v-icon>
                                                </v-flex>
                                                <v-flex xs9 class="pl-1">
                                                    <span class="font-italic">{{draft.totalDeposit}}</span>
                                                </v-flex>
                                            </v-layout>
                                            <span>Total Deposit</span>
                                        </v-tooltip>
                                    </v-card>
                                </v-hover>
                            </template>
                            <template v-else>
                                <v-card class="ma-0 translucent day" color="#f6f6f6" flat height="147px">
                                    <div>
                                        <span class="pa-2 grey--text font-weigth-bold">{{draft.date}}</span>
                                    </div>
                                </v-card>
                            </template>
                        </li>
                    </template>
                </ul>
            </div>
        </div> -->
        <daily-summary v-model="openDailySummary" :date="date" />
        <custom-range-dialog v-model="openMonthSummary" :dateFrom="dateFrom" :dateTo="dateTo"></custom-range-dialog>
    </div>
</template>
<script>
import DailySummary from './DailySummary.vue';
import Navigator from '../Navigator.vue';
import CustomRangeDialog from '../date-range/CustomRangeDialog.vue';

export default {
    components: {
        Navigator,
        DailySummary,
        CustomRangeDialog
    },
    data() {
        return {
            results: null,
            days: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            openDailySummary: false,
            openMonthSummary: false,
            loading: false,
            action: 'default',
            date: null,
            dateFrom: null,
            dateTo: null
        }
    },
    methods: {
        load() {
            this.loading = true;
            this.results = [];
            axios.get(`/api/sales-report/${this.monthIndex}/${this.year}/all`).then((res, rej) => {
                this.results = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        preview(day) {
            this.date = moment(`${this.year}-${this.monthIndex}-${day}`).format('YYYY-MM-DD');
            this.openDailySummary = true;
        },
        next() {
            this.action = 'next';
            this.$store.commit('transactionreport/setActiveMonth', this.monthIndex + 1);
        },
        prev() {
            this.action = 'prev';
            this.$store.commit('transactionreport/setActiveMonth', this.monthIndex - 1);
        },
        browseMonth() {
            this.$router.push('/sales-report/monthly-view');
        },
        viewMonthSummary() {
            this.dateFrom = moment().set('month', this.monthIndex - 1).startOf('month').format('YYYY-MM-DD');
            this.dateTo = moment().set('month', this.monthIndex - 1).endOf('month').format('YYYY-MM-DD');
            this.openMonthSummary = true;
        }
    },
    computed: {
        year() {
            return this.$store.getters['transactionreport/getYear'];
        },
        monthIndex() {
            return this.$store.getters['transactionreport/getMonthIndex'];
            // var m = this.$store.getters['transactionreport/getMonthIndex'];
            // if(m) return m;
            // else return new Date().getMonth() + 1;
        },
        activeMonth() {
            var m = this.$store.getters['transactionreport/getActiveMonth'];
            if(m) return m.text;
        },
        // day() {
        //     return this.$store.getters['transactionreport/getActiveDay'];
        // },
        numberOfDaysInMonth() {
            return this.$store.getters['transactionreport/getDaysInMonth'];
        },
        firstDayOfMonth() {
            return this.$store.getters['transactionreport/getFirstDayOfMonth'];
        },
        daysInMonth() {
            return Array.apply(null, Array(this.numberOfDaysInMonth))
                .map((x, i) => {
                    return i + 1;
                });
        },
        drafts() {
            return this.daysInMonth.map(d => {
                var day = this.results.find(_d => moment(_d.date).format('DD') == d);
                if(day) {
                    return {
                        date: d,
                        dayOfWeek: moment(day.date).format('dddd'),
                        today: moment().isSame(day.date, 'day'),
                        active: true,
                        amount: '₱' + parseFloat(day.amount).toLocaleString(),
                        expenses: '₱' + parseFloat(day.expenses).toLocaleString(),
                        newCustomers: day.newCustomers ? day.newCustomers + ' customer' + (day.newCustomers > 1 ? 's' : '') : '',
                        jo_count: 'J.O.' + day.paid_jo + '/' + day.total_jo + ' Paid',
                        collection: '₱' + parseFloat(day.collection).toLocaleString(),
                        totalDeposit: '₱' + parseFloat(day.totalDeposit).toLocaleString()
                    }
                } else {
                    var _date = moment(`${this.year}-${this.monthIndex}-${d}`);
                    return {
                        date: d,
                        dayOfWeek: _date.format('dddd'),
                        today: moment().isSame(_date, 'day')
                    }
                }
            });
        },
        cumulative() {
            let prev = null;
            return this.daysInMonth.map((day, index) => {
                let current = this.results.find(_d => moment(_d.date).format('DD') == day);
                if(current) {
                    if(!prev) {
                        prev = {
                            date: day,
                            collection: current.collection
                        }
                    } else {
                        prev = {
                            date: day,
                            collection: current.collection + prev.collection
                        }
                    }
                } else {
                    if(!prev) {
                        prev = {
                            date: day,
                            collection: 0
                        }
                    } else {
                        prev = {
                            date: day,
                            collection: prev.collection
                        }
                    }
                }
                return prev;
            });
        }
    },
    created() {
        if(!this.monthIndex) {
            // this.$store.commit('transactionreport/disolve');
            this.$store.commit('transactionreport/setActiveMonth', new Date().getMonth() + 1);
        }
        this.load();
    },
    watch: {
        monthIndex(val) {
            this.load();
        }
    }
}
</script>


<style scoped>
.date {
    color: white;
}
.today .date::after, .today .date{
    content: " (Today)";
    font-weight: bold;
    font-style: italic;
    color: greenyellow;
}
div.day {
    border-left: 1px solid grey;
    border-bottom: 1px solid grey;
    min-height: 140px;
    height: 100%;
}
div.active .day {
    background: white;
    cursor: pointer;
}
div.active .date {
    background-color: teal;
    transition: .2s;
}
div.active:hover .date {
    background-color: rgb(0, 187, 187);
    transition: .2s;
}
div.active.sunday:hover .date {
    background-color: #ff49ce;
}
div.inactive .day {
    border: 1px solid rgba(255, 255, 255, .2);
    background-color: rgba(255, 255, 255, 0.4);
    cursor: default;
}
div.inactive .date {
    background-color: rgba(0, 128, 128, 0.3);
}
div.active.sunday .date{
    background-color: #c40090;
}
div.inactive.sunday .date {
    background-color: rgba(196, 0, 144, 0.4);
}
div.today .day{
    border: 1px solid rgb(178 255 0)!important;
}
div.today.active .day{
    background-color: #c5ffe8!important;
}
div.today.sunday .day{
    border: 1px solid #e774f3!important;
}
.blank.day {
    border: 1px solid rgba(255, 255, 255, .2);
    background-color: rgba(255, 255, 255, 0.1);
    cursor: default;
}
.calendar {
    overflow-x: auto;
}
.main-container {
    min-width: 720px;
}
.day-of-week h3{
    border: 1px solid rgba(255, 255, 255, .2);
    background-color: rgba(255, 255, 255, 0.1);
    color: teal;
}
.day-of-week h3.sunday{
    border: 1px solid rgba(134, 19, 109, 0.2);
    background-color: rgba(255, 255, 255, 0.1);
    color: #c4008f;
}
</style>
