<template>
    <v-dialog :value="value" persistent max-width="500px">
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title grey--text">Summary from {{moment(dateFrom).format('MMM D, YYYY')}} to {{moment(dateTo).format('MMM D, YYYY')}}</span>
                <v-spacer></v-spacer>
                <v-btn small icon @click="close">
                    <v-icon small>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-progress-linear class="my-0" height="1" v-if="loading" indeterminate />
            <v-divider v-else class="mb-3"></v-divider>
            <v-card-text style="max-height:70vh; overflow-y: auto" v-if="!loading">

                <v-btn small round :class="{'primary': view == 'simplified'}" @click="view = 'simplified'">
                    <v-icon small>format_align_justify</v-icon>
                    simplified
                </v-btn>
                <v-btn small round :class="{'primary': view == 'breakdown'}" @click="view = 'breakdown'">
                    <v-icon small>format_align_right</v-icon>
                    breakdown
                </v-btn>

                <v-divider class="mb-4"></v-divider>
                <customers :newCustomers="newCustomers" />
                <job-orders :posSummary="posSummary" :view="view" />
                <used-services :usedServices="usedServices" :view="view" />
                <used-products :usedProducts="usedProducts" :view="view" />
                <rfid-transactions :rfidCard="rfidCard" :view="view" />
                <rfid-load :rfidLoad="rfidLoad" :view="view" />
                <total-sales :totalSales="totalSales" :view="view" />
                <collections :collections="collections" :view="view" />
                <expenses :expenses="expenses" :view="view" />
                <deposit :deposit="deposit" />
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close" round>close</v-btn>
                <v-btn @click="printAll" round :loading="printing">print</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import UsedProducts from '../calendar/dailysummary/UsedProducts.vue';
import UsedServices from '../calendar/dailysummary/UsedServices.vue';
import JobOrders from '../calendar/dailysummary/JobOrders.vue';
import RfidTransactions from '../calendar/dailysummary/RfidTransactions.vue';
import RfidLoad from '../calendar/dailysummary/RfidLoad.vue';
import Collections from '../calendar/dailysummary/Collections.vue';
import Expenses from '../calendar/dailysummary/Expenses.vue';
import TotalSales from '../calendar/dailysummary/TotalSales.vue';
import Deposit from '../calendar/dailysummary/Deposit.vue';
import Customers from '../calendar/dailysummary/Customers.vue';

export default {
    components: {
        UsedProducts,
        UsedServices,
        JobOrders,
        RfidTransactions,
        RfidLoad,
        Collections,
        Expenses,
        TotalSales,
        Deposit,
        Customers
    },
    props: [
        'value', 'dateFrom', 'dateTo',
    ],
    data() {
        return {
            view: 'simplified',
            loading: false,
            excelDownloading: null,
            rfidPrinting: null,
            rfidLoadPrinting: null,
            exportingPosTransactions: false,
            posCollectionPrinting: false,
            posTransactionsPrinting: false,
            posSummary: null,
            rfidCard: null,
            rfidLoad: null,
            collections: null,
            expenses: null,
            usedProducts: null,
            usedServices: null,
            newCustomers: 0,
            totalSales: null,
            deposit: 0,
            printing: false
        }
    },
    methods: {
        load(print = false) {
            this.loading = true;
            axios.get(`/api/sales-report/custom-range`, {
                params: {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
            }).then((res, rej) => {
                this.posSummary = res.data.posSummary;
                this.rfidCard = res.data.rfidCard;
                this.rfidLoad = res.data.rfidLoad;
                this.collections = res.data.collections;
                this.expenses = res.data.expenses;
                this.usedProducts = res.data.usedProducts;
                this.usedServices = res.data.usedServices;
                this.newCustomers = res.data.newCustomers;
                this.totalSales = res.data.totalSales;
                this.deposit = res.data.totalDeposit;
            }).catch(err => {

            }).finally(() => {
                this.loading = false;
            });
        },
        close() {
            this.$emit('input', false);
        },
        printAll() {
            this.printing = true;
            axios.get(`/api/sales-report/custom-range/1`, {
                params: {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
            }).then((res, rej) => {

            }).finally(() => {
                this.printing = false;
            })
        }
    },
    computed: {

    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
