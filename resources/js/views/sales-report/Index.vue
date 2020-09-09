<template>
    <v-container fluid>
        <h3 class="title grey--text">Sales report</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn :to="`/sales-report/calendar?year=${year}&monthIndex=${monthIndex}`" class="ml-0" active-class="primary" round>Calendar view</v-btn>
        <v-btn :to="`/sales-report/week?year=${year}&monthIndex=${monthIndex}`" active-class="primary" round>Week view</v-btn>
        <v-card class="rounded-card">
            <v-card-actions>
                <v-btn icon @click="subtractMonth">
                    <v-icon>chevron_left</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                    <v-btn flat large @click="openMonthSelector = true" outline round>
                        {{month}}, {{year}}
                        <v-icon right>arrow_drop_up</v-icon>
                    </v-btn>
                <v-spacer></v-spacer>
                <v-btn icon @click="addMonth">
                    <v-icon>chevron_right</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-divider class="my-2 transparent"></v-divider>
        <router-view />
        <month-selector v-model="openMonthSelector" @select="selectMonth" :year="year" @selectYear="selectYear" />
    </v-container>
</template>

<script>
import MonthSelector from './MonthSelector.vue';
export default {
    components: {
        MonthSelector
    },
    data() {
        return {
            dateContext: null,
            openMonthSelector: false
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
            this.$router.push({
                path: this.$router.currentRoute.path,
                query: {
                    year: this.year,
                    monthIndex: this.monthIndex
                }
            });
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
        month() {
            return this.dateContext.format('MMMM');
        },
        monthIndex() {
            return this.dateContext.format('M');
        },
        year() {
            return this.dateContext.format('YYYY');
        }
    },
    created() {
        this.dateContext = moment();
        this.emitMonth();
    }
}
</script>
