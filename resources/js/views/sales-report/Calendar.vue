<template>
    <v-card class="calendar">
        <v-card-actions>
            <v-btn icon @click="subtractMonth">
                <v-icon>chevron_left</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <h4 class="title">{{month + ' - ' + year}}</h4>
            <v-spacer></v-spacer>
            <v-btn icon @click="addMonth">
                <v-icon>chevron_right</v-icon>
            </v-btn>
        </v-card-actions>
        <div class="main-container">
            <v-card-text>
                <ul class="weekdays">
                    <li v-for="(day, i) in days" :key="i + 'w'">
                        <h3 class="font-weight-bold text-xs-center grey--text py-2">{{day}}</h3>
                    </li>
                </ul>
                <ul class="dates">
                    <li v-for="(blank, i) in firstDayOfMonth" :key="i + 'd'">&nbsp;</li>
                    <li v-for="(date, i) in daysInMonth" :key="i + 'i'" :class="{'current-day': date == initialDate &amp;&amp; month == initialMonth && year == initialYear}">
                        <v-hover v-slot:default="{ hover }">
                            <v-card class="pt-2 pb-3 ma-0 pointer" :elevation="hover ? 6 : 0" flat @click="dateClick(date)">
                                <v-btn small icon class="ma-0" :class="hover ? 'blue--text' : 'grey--text'">
                                    {{date}}
                                </v-btn>
                                <div class="title">{{amount(date)}}</div>
                                <pre>{{newCustomers(date)}}</pre>
                            </v-card>
                        </v-hover>
                    </li>
                </ul>
            </v-card-text>
        </div>
    </v-card>
</template>
<script>
export default {
    props: [
        'results', 'customers'
    ],
    data(){
        return {
            today: moment(),
            dateContext: moment(),
            days: ['S', 'M', 'T', 'W', 'T', 'F', 'S']
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
        amount(day) {
            if(this.results) {
                let i = this.results.find(d => moment(d.date).format('DD') == day);
                if(i) {
                    return '₱' + parseFloat(i.amount).toFixed(2);
                }
                return '-';
            }
            return '...';
        },
        newCustomers(day) {
            if(this.customers) {
                let i = this.customers.find(d => moment(d.day).format('DD') == day);
                if(i) {
                    return i.total_count + ' new customer';
                }
                return '-';
            }
            return '...';
        },
        emitMonth() {
            this.$emit('month-changed', this.dateContext);
        },
        dateClick(date) {
            this.$emit('input', date);
        }
    },
    computed: {
        year() {
            return this.dateContext.format('YYYY');
        },
        month() {
            return this.dateContext.format('MMMM');
        },
        daysInMonth() {
            return this.dateContext.daysInMonth();
        },
        currentDate() {
            return this.dateContext.get('date');
        },
        firstDayOfMonth() {
            var firstDay = moment(this.dateContext).subtract((this.currentDate - 1), 'days');
            return firstDay.weekday();
        },
        initialDate() {
            return this.today.get('date');
        },
        initialMonth() {
            return this.today.format('MMMM');
        },
        initialYear() {
            return this.today.format('Y');
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
    border: 1px solid #f5f5f5;
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
    border: 1px solid #f8f8f8;
}
.calendar {
    overflow-x: auto;
}
.main-container {
    min-width: 720px;
}
</style>
