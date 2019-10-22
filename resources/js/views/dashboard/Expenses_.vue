<template>
    <v-card>
        <v-card-text class="grey--text title">Expenses</v-card-text>
        <v-progress-linear class="my-0 py-0" height="3" v-if="loading" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-card-text v-if="result">
            <v-layout>
                <v-flex xs5>
                    <!-- <div class="progress-wrapper">
                        <div class="progress">
                            <div class="expenses" :style="`width:${expenses}%`" @click="liquidateExpenses">{{expenses}}</div>
                            <div class="purchases" :style="`width:${purchases}%`" @click="liquidatePurchases">{{purchases}}</div>
                        </div>
                    </div> -->
                    <apexcharts type="pie" :options="expenseOptions" :series="expenseSeries" />
                </v-flex>
                <v-flex xs7>
                    <v-list v-if="expensesSum">
                        <v-list-tile v-for="expense in expensesSum.expenses" :key="expense.id">
                            <v-list-tile-content>
                                <v-list-tile-title>{{expense.expense_type}} P {{expense.totalCost}}</v-list-tile-title>
                                <v-progress-linear class="my-0" :value="getPercent(expense.totalCost, result.expenses)"></v-progress-linear>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                {{getPercent(expense.totalCost, expensesSum.totalCost).toFixed(2)}} %
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                    <v-list v-if="purchasesSum">
                        <v-list-tile v-for="purchase in purchasesSum.purchases" :key="purchase.id">
                            <v-list-tile-content>
                                <v-list-tile-title>{{purchase.productName}} P {{purchase.totalCost}}</v-list-tile-title>
                                <v-progress-linear class="my-0" :value="getPercent(purchase.totalCost, result.purchases)"></v-progress-linear>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                {{getPercent(purchase.totalCost, purchasesSum.totalCost).toFixed(2)}} %
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: [
        'month',
        'year'
    ],
    data() {
        return {
            loading: false,
            result: null,
            expensesSum: null,
            purchasesSum: null,
            liquidating: false
        }
    },
    methods: {
        get() {
            this.expensesSum = null;
            this.purchasesSum = null;
            this.loading = true;
            axios.get('/api/dashboard/self/expenses', {
                params: {
                    month: this.month.index,
                    year: this.year
                }
            }).then((res, rej) => {
                this.result = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        liquidateExpenses() {
            axios.get('/api/dashboard/self/liquidate/expenses', {
                params: {
                    month: this.month.index,
                    year: this.year
                }
            }).then((res, rej) => {
                this.purchasesSum = null;
                this.expensesSum = res.data;
            }).finally(() => {
                this.liquidating = false;
            });
        },
        liquidatePurchases() {
            axios.get('/api/dashboard/self/liquidate/purchases', {
                params: {
                    month: this.month.index,
                    year: this.year
                }
            }).then((res, rej) => {
                this.expensesSum = null;
                this.purchasesSum = res.data;
            }).finally(() => {
                this.liquidating = false;
            });
        },
        getPercent(val, base) {
            return val / base * 100;
        },
        clickPie(data, e) {
            console.log('select');
        }
    },
    computed: {
        expenseOptions() {
            return {
                labels: ['Expenses', 'Purchases'],
                chart: {
                    events: {
                        dataPointSelection: (e, chart, option) => {
                            console.log('chart', chart)
                            console.log('option', option)
                            console.log('index', option.dataPointIndex);
                            if(option.dataPointIndex == 0) {
                                this.liquidateExpenses();
                            } else {
                                this.liquidatePurchases();
                            }
                            // console.log(this.result[option.dataPointIndex]);
                        }
                    }
                }
            }
        },
        expenseSeries() {
            if(this.result) {
                return this.result.map(s => s.data);
            }
            return [];
        },
        expenses() {
            let e = this.result[0] / (this.result.expenses + this.result.purchases) * 100;
            if(e) {
                return e;
            } else {
                return 0;
            }
        },
        purchases() {
            let p = this.result.purchases / (this.result.expenses + this.result.purchases) * 100;
            if(p) {
                return p;
            } else {
                return 0;
            }
        }
    },
    mounted() {
        this.get();
    },
    watch: {
        month(val) {
            if(val) {
                this.get();
            }
        }
    }
}
</script>
<style scoped>
div.progress-wrapper {
    background: silver;
}
div.progress {
    display: flex;
}
div.expenses {
    background-color: red;
    height: 20px;
}
div.purchases {
    background: yellow;
    height: 20px;
}
</style>
