<template>
    <v-container fluid>
        <h3 class="title white--text">Sales report</h3>
        <v-divider class="my-3"></v-divider>
        <!-- <v-btn to="/sales-report/yearly-view" class="ml-0" active-class="primary" round>{{yearsFrom}} - {{yearsUntil}}</v-btn> -->
        <!-- <v-btn to="/sales-report/monthly-view" class="ml-0" active-class="primary" round>{{year}}</v-btn> -->
        <!-- <v-btn v-if="activeMonth" to="/sales-report/calendar-view" class="ml-0" active-class="primary" round>{{activeMonth.text}}</v-btn> -->
        <!-- <v-btn v-if="activeMonth" to="/sales-report/weekly-view" active-class="primary" round>Week view</v-btn> -->
        <!-- <v-card class="rounded-card translucent">
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
        </v-card> -->
        <!-- <v-divider class="my-2 transparent"></v-divider> -->
        <transition name="fade" mode="out-in">
            <router-view />
        </transition>
        <!-- <month-selector v-model="openMonthSelector" @select="selectMonth" :year="year" @selectYear="selectYear" /> -->
    </v-container>
</template>

<script>
// import MonthSelector from './MonthSelector.vue';
export default {
    components: {
        // MonthSelector
    },
    data() {
        return {
            // dateContext: null,
            // openMonthSelector: false
        }
    },
    methods: {
        // addMonth() {
        //     this.dateContext = moment(this.dateContext).add(1, 'month');
        //     this.emitMonth();
        // },
        // subtractMonth() {
        //     this.dateContext = moment(this.dateContext).subtract(1, 'month');
        //     this.emitMonth();
        // },
        // emitMonth() {
        //     this.$router.push({
        //         path: this.$router.currentRoute.path,
        //         query: {
        //             year: this.year,
        //             monthIndex: this.monthIndex
        //         }
        //     });
        // },
        // selectMonth(monthIndex) {
        //     this.dateContext = moment(this.dateContext).set('month',monthIndex);
        //     this.emitMonth();
        // },
        // selectYear(year) {
        //     this.dateContext = moment(this.dateContext).set('year', year);
        //     this.emitMonth();
        // }
    },
    computed: {
        activeMonth() {
            return this.$store.getters['transactionreport/getActiveMonth'];
        },
        // monthIndex() {
        //     return this.$store.getters['transactionreport/getActiveMonth'];
        // },
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
        year() {
            return this.$store.getters['transactionreport/getYear'];
        }
    },
    created() {
        this.$store.commit('transactionreport/initMonths');
        // this.dateContext = moment();
        // this.emitMonth();
    },
    beforeDestroy() {
        this.$store.commit('transactionreport/disolve');
    }
}
</script>

<style scoped>
/* .fade-enter-active,
.fade-leave-active {
  transition-duration: 0.2s;
  transition-property: opacity;
  transition-timing-function: ease;
}

.fade-enter,
.fade-leave-active {
  opacity: 0
} */
</style>>
