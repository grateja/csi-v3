<template>
    <v-container v-if="result">
        <v-card class="transparent" flat>
            <v-card-title>
            <h3 class="title white--text">Daily Report</h3>
            <v-spacer></v-spacer>
            <!-- <v-btn round>Import</v-btn> -->
            <v-btn @click="print" round>Print</v-btn>
            </v-card-title>
        </v-card>

        <!-- <v-card>
            <span class="title white--text my-3">Daily Report</span>
            <v-spacer />
            <v-btn>keme</v-btn>
        </v-card> -->
        <v-progress-linear v-if="loading" indeterminate class="my-3"></v-progress-linear>
        <v-divider v-else class="my-3"></v-divider>
        <date-navigator v-model="date" class="my-2" />


        <!-- <summary-preview :summary="result.summary" /> -->

        <v-card class="translucent rounded-card mt-2" v-if="result && result.customers && result.customers.length">
            <v-card-title>
                <h4 class="font-weight-bold title">NEW CUSTOMERS</h4>
            </v-card-title>
            <v-card-text v-if="result">
                <v-divider />
                <v-layout class="font-weight-bold">
                    <v-flex xs1>CRN</v-flex>
                    <v-flex xs3>NAME</v-flex>
                    <v-flex xs4>ADDRESS</v-flex>
                    <v-flex xs2>C.#.</v-flex>
                </v-layout>
                <v-divider />
                <v-layout v-for="customer in result.customers" :key="customer.id">
                    <v-flex xs1>{{customer.crn}}</v-flex>
                    <v-flex xs3>{{customer.name}}</v-flex>
                    <v-flex xs4>{{customer.address}}</v-flex>
                    <v-flex xs2>{{customer.contact_number}}</v-flex>
                </v-layout>
            </v-card-text>
        </v-card>

        <template v-if="result?.jobOrdersToday?.created?.length > 0">
            <v-card class="translucent rounded-card mt-2">
                <v-card-title>
                    <h4 class="font-weight-bold title">JOB ORDERS CREATED TODAY</h4>
                </v-card-title>
                <v-card-text>
                    <v-divider />
                    <job-orders :items="result.jobOrdersToday.created" @openJobOrder="openTransaction" />
                </v-card-text>
            </v-card>
        </template>

        <template v-if="result?.jobOrdersToday?.paid?.length > 0">
            <v-card class="translucent rounded-card mt-2">
                <v-card-text>
                    <h4 class="font-weight-bold title">JOB ORDERS PAID TODAY</h4>
                    <h4 class="grey--text font-italic"> (Including Job Orders created from previous days)</h4>
                </v-card-text>
                <v-card-text>
                    <v-divider />
                    <job-orders :items="result.jobOrdersToday.paid" @openJobOrder="openTransaction" />
                </v-card-text>
            </v-card>
        </template>

        <template v-if="result?.jobOrdersOtherDays?.unCollected?.length > 0">
            <v-card class="translucent rounded-card mt-2">
                <v-card-text>
                    <h4 class="font-weight-bold title">UNCOLLECTED</h4>
                    <h4 class="grey--text font-italic"> (Including Job Orders created from previous days)</h4>
                </v-card-text>
                <v-card-text>
                    <v-divider />
                    <job-orders :items="result.jobOrdersOtherDays.unCollected" @openJobOrder="openTransaction" />
                </v-card-text>
            </v-card>
        </template>

        <!-- <template v-if="Object.keys(result?.washes).length || Object.keys(result?.dries).length">
            <v-card class="translucent rounded-card mt-2">
                <v-card-text>
                    <h4 class="font-weight-bold title">PROCESSED WASH/DRY</h4>
                    <h4 class="grey--text font-italic">(Including Job Orders created from previous days)</h4>
                </v-card-text>
                <v-card-text>
                    <template v-if="Object.keys(result?.washes).length > 0">
                        <h3 class="font-weight-bold mt-3">Washes</h3>
                        <processed-wash-dry :items="result.washes" @openJobOrder="openTransaction" />
                    </template>
                    <template v-if="Object.keys(result?.dries).length > 0">
                        <h3 class="font-weight-bold mt-3">Dries</h3>
                        <processed-wash-dry :items="result.dries" @openJobOrder="openTransaction" />
                    </template>
                </v-card-text>
            </v-card>
        </template> -->
        <wash-dry-wrapper :washes="result?.washes" :dries="result?.dries" @openJobOrder="openTransaction" title="PROCESSED WASH/DRY" />
        <wash-dry-wrapper :washes="result?.rewash" :dries="result?.redry" @openJobOrder="openTransaction" title="REWORKS AND TRANSFERS" />
        <used-products :items="result.usedProducts" @openJobOrder="openTransaction" />
        <lagoon-services :items="result.lagoon" @openJobOrder="openTransaction" />
        <other-services :items="result.otherServices" @openJobOrder="openTransaction" />
        <scarpa-services :items="result.scarpa" @openJobOrder="openTransaction" />
        <elux-services :items="result.eluxServices" @openJobOrder="openTransaction" />
        <expenses :items="result.expenses" />

        <template v-if="result.pendingServices != null">
            <v-card class="translucent rounded-card mt-2">
                <v-card-title>
                    <h4 class="font-weight-bold title">UNPROCESSED WASH/DRY</h4>
                </v-card-title>
                <v-card-text>
                    <template v-if="result.pendingServices?.today?.length > 0">
                        <h3 class="font-weight-bold mt-3">Today</h3>
                        <pending-services :items="result.pendingServices.today" @openJobOrder="openTransaction" />
                    </template>
                    <template v-if="result.pendingServices?.allTime?.length > 0">
                        <h3 class="font-weight-bold mt-3">Other days</h3>
                        <pending-services :items="result.pendingServices.allTime" @openJobOrder="openTransaction" />
                    </template>
                </v-card-text>
            </v-card>
        </template>
        <template v-if="isOwner && result.jobOrdersOtherDays?.canceled?.length + result.jobOrdersToday?.canceled?.length > 0">
            <v-card class="translucent rounded-card mt-2">
                <v-card-title>
                    <h4 class="font-weight-bold title">CANCELED JOB ORDERS</h4>
                </v-card-title>
                <v-card-text>
                    <template v-if="result.jobOrdersToday?.canceled?.length > 0">
                        <h3 class="font-weight-bold mt-3">Today</h3>
                        <job-orders :items="result.jobOrdersToday.canceled" @openJobOrder="openTransaction" />
                    </template>
                    <template v-if="result.jobOrdersOtherDays?.canceled?.length > 0">
                        <h3 class="font-weight-bold mt-3">Other days</h3>
                        <job-orders :items="result.jobOrdersOtherDays.canceled" @openJobOrder="openTransaction" />
                    </template>
                </v-card-text>
            </v-card>
        </template>
        <transaction-dialog v-model="openTransactionDialog" :transactionId="transactionId"/>
    </v-container>
</template>

<script>
import TransactionDialog from '../transaction-reports/TransactionDialog.vue';
import DateNavigator from '../shared/DateNavigator.vue';
import JobOrders from './JobOrders.vue';
import PendingServices from './PendingServices.vue';
import SummaryPreview from './Summary.vue';
import WashDryWrapper from './WashDryWrapper.vue';
import UsedProducts from './UsedProducts.vue';
import OtherServices from './OtherServices.vue';
import LagoonServices from './LagoonServices.vue';
import ScarpaServices from './ScarpaServices.vue';
import EluxServices from './EluxServices.vue';
import Expenses from './Expenses.vue';

export default {
    components: {
        DateNavigator,
        JobOrders,
        PendingServices,
        SummaryPreview,
        WashDryWrapper,
        UsedProducts,
        OtherServices,
        LagoonServices,
        TransactionDialog,
        ScarpaServices,
        EluxServices,
        Expenses
    },
    data() {
        return {
            date: moment().format('YYYY-MM-DD'),
            result: null,
            loading: false,
            transactionId: null,
            openTransactionDialog: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/daily-sales', {
                params: {
                    date: this.date,
                }
            }).then((res, rej) => {
                this.result = res.data;
            }).finally(() => {
                this.loading = false;
            })
        },
        openTransaction(transactionId) {
            this.transactionId = transactionId;
            this.openTransactionDialog = true;
        },
        print() {
            this.$store.dispatch('printer/printDailySale', {
                date: this.date,
                until: this.date
            }).then((res, rej) => {
                console.log(res.data);
            })
        }
    },
    created() {
        this.load();
    },
    watch: {
        date(val) {
            if(val) {
                this.load();
            }
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        },
    }
}
</script>
