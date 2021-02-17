<template>
    <div>
        <year-navigator v-model="year" />
        <month-navigator v-model="monthIndex" />
        <pre>{{result}}</pre>
    </div>
</template>

<script>
import MonthNavigator from '../shared/MonthNavigator.vue';
import YearNavigator from '../shared/YearNavigator.vue';

export default {
    components: {
        MonthNavigator,
        YearNavigator
    },
    data() {
        return {
            groupBy: 'transaction',
            result: [],
            monthIndex: moment().format('M'),
            year: moment().format('YYYY')
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get(`/api/sales-report/${this.monthIndex}/${this.year}/pos-transactions`, {
                params: {
                    date: this.date,
                    groupBy: this.groupBy
                }
            }).then((res, rej) => {
                this.result = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        }
    },
    created() {
        this.load();
    },
    watch: {
        year(val) {
            if(val) {
                this.load();
            }
        },
        monthIndex(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
