<template>
    <v-container>
        <h3 class="title grey--text">Sales report</h3>
        <v-progress-linear class="my-3" v-if="loading" indeterminate height="1" />
        <v-divider class="my-3" v-else></v-divider>
        <calendar :results="results" :customers="newCustomers" :year="year" @month-changed="monthChanged" @input="preview" />
        <daily-summary v-model="openDailySummary" :date="date" />
    </v-container>
</template>

<script>
import Calendar from './Calendar.vue';
import DailySummary from './DailySummary.vue';

export default {
    components: {
        Calendar,
        DailySummary
    },
    data() {
        return {
            results: [],
            newCustomers: [],
            monthIndex: moment().format('MM'),
            year: moment().format('YYYY'),
            date: moment(),
            openDailySummary: false,
            loading: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get(`/api/sales-report/${this.monthIndex}/${this.year}/all`).then((res, rej) => {
                this.results = res.data.result;
                this.newCustomers = res.data.newCustomers;
            }).finally(() => {
                this.loading = false;
            })
        },
        monthChanged(dateContext) {
            this.monthIndex = moment(dateContext).format('M');
            this.year = moment(dateContext).format('YYYY');
            this.results = null;
            this.newCustomers = null;
            this.load();
        },
        preview(day) {
            this.date = moment(`${this.year}-${this.monthIndex}-${day}`).format('YYYY-MM-DD');
            this.openDailySummary = true;
        }
    },
    created() {
        this.load();
    }
}
</script>
