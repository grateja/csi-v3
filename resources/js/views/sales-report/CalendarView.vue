<template>
    <div>
        <v-progress-linear indeterminate v-if="loading" height="1" class="ma-0"></v-progress-linear>
        <v-divider v-else></v-divider>
        <calendar :results="results" :year="year" @month-changed="monthChanged" @input="preview" :summary="monthlySummary" :date-context="dateContext" />
        <daily-summary v-model="openDailySummary" :date="date" />
    </div>
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
            monthlySummary: null,
            openDailySummary: false,
            loading: false,
            date: null
        }
    },
    methods: {
        load() {
            this.loading = true;
            this.results = [];
            axios.get(`/api/sales-report/${this.monthIndex}/${this.year}/all`).then((res, rej) => {
                this.results = res.data.result;
                this.monthlySummary = res.data.summary;
            }).finally(() => {
                this.loading = false;
            })
        },
        monthChanged(dateContext) {
            this.monthIndex = moment(dateContext).format('M');
            this.year = moment(dateContext).format('YYYY');
            this.results = null;
            this.monthlySummary = null;
            this.load();
        },
        preview(day) {
            this.date = moment(`${this.year}-${this.monthIndex}-${day}`).format('YYYY-MM-DD');
            this.openDailySummary = true;
        }
    },
    // created() {
    //     this.load();
    // },
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
