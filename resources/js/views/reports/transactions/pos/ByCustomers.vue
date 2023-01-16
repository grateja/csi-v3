<template>
    <div>
        <v-layout>
            <v-flex grow>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                    <v-layout>
                        <v-flex shrink>
                            <date-range-selector @input="selectDate" v-model="dateRange"></date-range-selector>
                        </v-flex>
                        <v-flex shrink>
                            <v-tooltip top>
                                <v-btn slot="activator" icon @click="exportDownload" :loading="exporting">
                                    <v-icon>archive</v-icon>
                                </v-btn>
                                <span>Download excel</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>
                </form>
            </v-flex>
            <v-flex shrink>
            </v-flex>
        </v-layout>

        <v-divider></v-divider>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{ date(props.item.date) }}</td>
                <td>{{ props.item.customerName }}</td>
                <td>{{ props.item.jobOrder }}</td>
                <td>{{ date(props.item.datePaid) }}</td>
                <td>{{ date(props.item.userName) }}</td>
                <td>{{ props.item.paidTo }}</td>
                <td>
                    <v-tooltip top>
                        <v-btn slot="activator" small icon :to="`/reports/transactions/pos-transactions/by-items?tab=1&transactionId=${props.item.id}`">
                        {{props.item.productsCount}}
                            <!-- <v-icon small>list</v-icon> -->
                        </v-btn>
                        <span>View items</span>
                    </v-tooltip>
                </td>
                <td>
                    <v-tooltip top>
                        <v-btn slot="activator" small icon :to="`/reports/transactions/pos-transactions/by-items?tab=0&transactionId=${props.item.id}`">
                        {{props.item.servicesCount}}
                            <!-- <v-icon small>list</v-icon> -->
                        </v-btn>
                        <span>View items</span>
                    </v-tooltip>
                    <!-- <v-tooltip top>
                        <v-btn slot="activator" small icon @click="editCustomer(props.item)">
                            <v-icon small>edit</v-icon>
                        </v-btn>
                        <span>Edit details</span>
                    </v-tooltip>
                    <v-tooltip top v-if="props.item.rfid_cards_count > 0">
                        <v-btn slot="activator" small icon :to="`/rfid/cards/c?keyword=${props.item.name}`">
                            <v-icon small>credit_card</v-icon>
                            {{props.item.rfid_cards_count}}
                        </v-btn>
                        <span>{{props.item.rfid_cards_count}} Registered RFID card(s)</span>
                    </v-tooltip> -->
                </td>
                <td>P {{ props.item.serviceAmount + props.item.productAmount }}</td>
                <td>
                    <v-tooltip top v-if="props.item.paid">
                        <v-btn slot="activator" small icon @click="printReceipt(props.item)" :loading="props.item.isPrinting">
                            <v-icon small>print</v-icon>
                        </v-btn>
                        <span>Print receipt</span>
                    </v-tooltip>
                    <v-tooltip top>
                        <v-btn slot="activator" small icon @click="voidTransaction(props.item)">
                            <v-icon small>clear</v-icon>
                        </v-btn>
                        <span>Void transaction</span>
                    </v-tooltip>

                </td>
            </template>
            <tr slot="footer" v-if="!!summary" class="font-weight-bold grey lighten-3">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{summary.totalProducts}}</td>
                <td>{{summary.totalServices}}</td>
                <td>P {{summary.totalServicesAmount + summary.totalProductsAmount}}</td>
                <td></td>
            </tr>
        </v-data-table>

        <v-divider class="my-2"></v-divider>

        <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>

        <!-- <v-card class="primary lighten-3">
            <v-card-title class="white--text title">Summary</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <dl>
                    <dt class="caption">Total products</dt>
                    <dd class="ml-4 font-weight-bold">{{summary.totalProducts}}</dd>
                    <dt class="caption">Total services</dt>
                    <dd class="ml-4 font-weight-bold">{{summary.totalServices}}</dd>
                    <dt class="caption">Total products amount</dt>
                    <dd class="ml-4 font-weight-bold">{{summary.totalProductsAmount}}</dd>
                    <dt class="caption">Total services amount</dt>
                    <dd class="ml-4 font-weight-bold">{{summary.totalServicesAmount}}</dd>
                </dl>
            </v-card-text>
        </v-card> -->
        <void-transaction-dialog v-model="openTransactionDialog" :transactionId="transactionId" @ok="removeTransaction"></void-transaction-dialog>
    </div>
</template>

<script>
import VoidTransactionDialog from '../../../pos/VoidTransactionDialog.vue';
import DateRangeSelector from '../../../shared/DateRangeSelector.vue';
import moment from 'moment';
export default {
    components: {
        VoidTransactionDialog,
        DateRangeSelector
    },
    data() {
        return {
            dateRange: null,
            keyword: this.$route.query.keyword,
            page: parseInt(this.$route.query.page) || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeTransaction: null,
            transactionId: null,
            openTransactionDialog: false,
            summary: null,
            headers: [
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Customer',
                    sortable: false
                },
                {
                    text: 'Job order',
                    sortable: false
                },
                {
                    text: 'Date paid',
                    sortable: false
                },
                {
                    text: 'Saved by',
                    sortable: false
                },
                {
                    text: 'Paid to',
                    sortable: false
                },
                {
                    text: 'Products',
                    sortable: false
                },
                {
                    text: 'Services',
                    sortable: false
                },
                {
                    text: 'Total amount',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.load();
        },
        load() {
            if(this.loading) return;

            this.$router.push({
                query: {
                    keyword: this.keyword,
                    page: this.page
                }
            });

            this.loading = true;

            axios.get('/api/search/transactions/pos/by-customers/self', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    dateRange: this.dateRange
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
                this.summary = res.data.summary;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        exportDownload() {
            this.$store.dispatch('exportdownload/download', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    transactionId: this.$route.query.transactionId ? this.$route.query.transactionId : null,
                    dateRange: this.dateRange
                },
                uri: 'pos-job-order'
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        date(date) {
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
        },
        voidTransaction(transaction) {
            this.transactionId = transaction.id;
            this.openTransactionDialog = true;
        },
        removeTransaction(transactionId) {
            console.log(transactionId);
            this.items = this.items.filter(t => t.id != transactionId);
        },
        printReceipt(item) {
            console.log('Print', item);
            Vue.set(item, 'isPrinting', true);
            this.$store.dispatch('printer/printReceipt', {
                transactionId: item.id
            }).finally(() => {
                Vue.set(item, 'isPrinting', false);
            });
        },
        selectDate(dates) {
            console.log(dates);
            this.load();
        }
    },
    computed: {
        isAdmin() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        },
        exporting() {
            return this.$store.getters['exportdownload/isLoading'];
        }
    },
    created() {
        this.load();
    }
}
</script>
