<template>
    <v-card class="transparent" flat>
        <v-layout row wrap>
            <v-flex lg10>
                <div class="calendar">
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
                                <li v-if="draft" :key="draft.date" :class="{'sunday' : draft.dayOfWeek == 'Sunday', 'current-day green': draft.date == dateContext &amp;&amp; month == initialMonth && year == initialYear}">
                                    <template v-if="draft.active">
                                        <v-hover v-slot:default="{ hover }" v-if="draft.active">
                                            <v-card class="ma-0 pointer translucent day" :elevation="hover ? 6 : 0" flat @click="dateClick(draft.date)" height="121px">
                                                <span>{{draft.date}}</span>
                                                <v-tooltip top>
                                                    <div slot="activator" class="title text-xs-center caption" :class="draft.jo_color">{{draft.jo_count}}</div>
                                                    <span>Total number of Job Orders</span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <div slot="activator" class="title text-xs-center">
                                                        <v-icon small>collections_bookmark</v-icon>
                                                        {{draft.amount}}</div>
                                                    <span>Total sales</span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <div slot="activator" class="text-xs-center blue--text">
                                                        <v-icon small color="blue">move_to_inbox</v-icon>
                                                        {{draft.collection}}</div>
                                                    <span>Total collections</span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <div slot="activator" class="text-xs-center grey--text">
                                                        <v-icon small>save</v-icon>
                                                        {{draft.expenses}}</div>
                                                    <span>Total expenses</span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <div slot="activator" class="text-xs-center">{{draft.newCustomers}}</div>
                                                    <span>Total number of customer</span>
                                                </v-tooltip>
                                            </v-card>
                                        </v-hover>
                                        <v-progress-linear :background-color="draft.background" color="green" v-if="draft.active && draft.profitPercentage" :value="draft.value" class="ma-0" height="20px">
                                            <div class="white--text text-xs-center">{{draft.profitStr}} ({{Math.round(draft.profitPercentage)}} %)</div>
                                        </v-progress-linear>
                                    </template>
                                    <template v-else>
                                        <v-card class="ma-0 translucent day" color="#f6f6f6" flat height="141px">
                                            <div>
                                                <span class="day ma-1">{{draft.date}}</span>
                                            </div>
                                        </v-card>
                                    </template>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </v-flex>
            <v-flex lg2>
                <v-card v-if="summary" flat>
                    <v-card-title class="title">Summary</v-card-title>
                    <v-divider color="grey"></v-divider>
                    <v-card-text class="my-0 py-0">
                        <v-list>
                            <v-list-tile>
                                <v-icon left>bookmarks</v-icon>
                                <v-list-tile-content>
                                    {{summary.paid_jo}} / {{summary.total_jo}} paid
                                    <span class="caption grey--text">Job Orders</span>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider />
                            <v-list-tile>
                                <v-icon left>collections_bookmark</v-icon>
                                <v-list-tile-content>
                                    ₱ {{parseFloat(summary.totalSales).toLocaleString()}}
                                    <span class="caption grey--text">Total sales</span>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider />
                            <v-list-tile>
                                <v-icon left>move_to_inbox</v-icon>
                                <v-list-tile-content>
                                    ₱ {{parseFloat(summary.totalCollections).toLocaleString()}}
                                    <span class="caption grey--text">Collections</span>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider />
                            <v-list-tile>
                                <v-icon left>save</v-icon>
                                <v-list-tile-content>
                                    ₱ {{parseFloat(summary.expenses).toLocaleString()}}
                                    <span class="caption grey--text">Expenses</span>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-divider />
                            <v-divider />
                            <v-list-tile>
                                <v-icon left>people</v-icon>
                                <v-list-tile-content>
                                    {{summary.totalNewCustomers}}
                                    <span class="caption grey--text">New customers</span>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                        <v-divider class="grey"></v-divider>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <month-selector v-model="openMonthSelector" @select="selectMonth" :year="year" @selectYear="selectYear" />
    </v-card>
</template>
<script>
import MonthSelector from './MonthSelector.vue';
export default {
    components: {
        MonthSelector
    },
    props: [
        'results', 'summary', 'dateContext'
    ],
    data(){
        return {
            // today: moment(),
            days: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            openMonthSelector: false,
            openYearSelector: false
        }
    },
    methods: {
        addMonth() {
            this.dateContext = moment(this.dateContext).add(1, 'month');
            this.emitMonth();
        },
        subtractMonth() {
            this.dateContext = moment(this.dateContext).subtract(1, 'month');
            this.emitMonth();
        },
        emitMonth() {
            this.$emit('month-changed', this.dateContext);
        },
        dateClick(date) {
            console.log(date)
            this.$emit('input', date);
        },
        selectMonth(monthIndex) {
            this.dateContext = moment(this.dateContext).set('month',monthIndex);
            this.emitMonth();
        },
        selectYear(year) {
            this.dateContext = moment(this.dateContext).set('year', year);
            this.emitMonth();
        }
    },
    computed: {
        drafts() {
            if(this.results) {
                let days = [];
                for (let index = 1; index <= this.daysInMonth; index++) {
                    let i = this.results.find(d => moment(d.date).format('DD') == index);
                    if(i) {
                        let profitPeso = i.amount - i.expenses;
                        let profit = profitPeso / i.amount * 100;
                        let lossPeso = i.expenses - i.amount;
                        let loss = lossPeso / i.expenses * 100;

                        days[index] = {
                            active: true,
                            date: index,
                            dayOfWeek: moment(i.date).format('dddd'),
                            amount: '₱' + parseFloat(i.amount).toLocaleString(),
                            expenses: '₱' + parseFloat(i.expenses).toLocaleString(),
                            profitStr: '₱' + parseFloat(profitPeso).toLocaleString(),
                            profitPercentage: profit > 1 ? profit : -loss,
                            value: (!i.amount && !i.expenses) ? 0 : profit > 1 ? profit : 100 - loss || 0,
                            background: profit > 1 ? 'blue' : 'pink',
                            newCustomers: i.newCustomers ? i.newCustomers + ' new customer' + (i.newCustomers > 1 ? 's' : '') : '',
                            jo_color: i.total_jo == i.paid_jo ? 'green--text' : 'grey--text',
                            jo_count: 'J.O.' + i.paid_jo + '/' + i.total_jo + ' Paid',
                            collection: '₱' + parseFloat(i.collection).toLocaleString(),
                        };
                    } else if(index != 0) {
                        days[index] = {
                            date: index,
                            dayOfWeek: moment(`${this.year}-${this.month}-${index}`).format('dddd')
                        };
                    }
                }
                return days;
            }
            return [];
        },
        year() {
            return this.$route.query.year;
        },
        month() {
            return this.dateContext.format('MMMM');
        },
        daysInMonth() {
            return this.dateContext.daysInMonth();
        },
        firstDayOfMonth() {
            return moment(this.dateContext).set('date', 1).day() - 1;
        },
        initialMonth() {
            return moment(this.dateContext).format('MMMM');
        }
    }
}
</script>

<style scoped>
ul.weekdays{
    display: flex;
    margin: 0;
    padding: 0;
}
ul.weekdays li {
    margin: 0;
    list-style: none;
    width: 14.28%;
    /* border-top: 1px solid #c1c6f7; */
    /* border-bottom: 1px solid #c1c6f7; */
    /* background: rgb(195, 229, 238); */
}
ul.weekdays li span {
    color: white;
    background: #2196f3;
    padding: 5px;
    padding-left: 30px;
    padding-right: 30px;
    border-radius: 20px;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.4);
}
ul.weekdays li.sunday span {
    color: white;
    background: #e91e63;
    /* border-left: 1px solid #f1b9d5 !important; */
    /* border-right: 1px solid #f1b9d5 !important; */
}
div.day {
    border-radius:10px !important;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
}
.sunday > h3 > span {
}

ul.dates{
    display: flex;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
}
ul.dates li {
    margin: 0;
    list-style: none;
    width: 14.28%;
    padding: 5px;
    /* border: 1px solid #e6e6e6; */
}
ul.dates li.blank {
    border: none;
}
.calendar {
    overflow-x: auto;
}
.main-container {
    min-width: 720px;
}
.sunday .day {
    color: #a80b5a;
    font-weight: bold;
}
/* .day-blank {
    box-shadow:5px 5px 5px rgba(0,0,0,.05) inset,
        -5px -5px 5px #fff inset;
    height: 100%;
} */
</style>
