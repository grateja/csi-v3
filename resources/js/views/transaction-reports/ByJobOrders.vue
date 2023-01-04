<template>
    <div>
        <v-layout justify-center>
            <v-flex style="max-width: 500px">
                <v-text-field class="translucent-input round-input" label="Search customer or Job order number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
            </v-flex>
            <v-flex>
                <v-checkbox v-model="hideDeleted" class="ml-4 font-weight-bold" color="primary" label="Hide deleted"></v-checkbox>
            </v-flex>
        </v-layout>
        <v-layout justify-center>
            <v-flex shrink>
                <v-text-field label="Specify date created" v-model="date" type="date" append-icon="date" @change="filter" outline class="mr-2 round-input translucent-input" style="width: 200px" dense></v-text-field>
            </v-flex>
            <v-flex shrink>
                <v-text-field label="Specify date paid" v-model="datePaid" type="date" append-icon="date" @change="filter" outline class="mx-1 round-input translucent-input" style="width: 200px" dense></v-text-field>
            </v-flex>
            <v-flex shrink>
                <v-combobox class="mx-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['job order number', 'customer name', 'date created', 'date paid']" @change="filter"></v-combobox>
            </v-flex>
            <v-flex shrink>
                <v-combobox class="ml-2 translucent-input round-input" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
            </v-flex>
        </v-layout>

        <!-- <v-card class="rounded-card translucent">
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date created" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-text-field label="Specify date paid" v-model="datePaid" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1 translucent-input round-input" label="Search customer or Job order number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['job_order', 'customer_name', 'date', 'date_paid']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card> -->

        <v-card class="translucent-table rounded-card">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.index + 1}}</td>
                        <td>
                            <v-btn round small class="font-weight-bold" :color="joColor(props.item)" @click="previewTransaction(props.item)">
                                {{ props.item.job_order }}
                            </v-btn>
                            <div class="caption red--text" v-if="props.item.deleted_at">
                                DELETED:
                                {{ moment(props.item.deleted_at).format('LLL')}}
                            </div>
                            <div v-if="props.item.cancelation_remarks">
                                {{props.item.cancelation_remarks}}
                            </div>
                        </td>
                        <td>{{ props.item.customer_name }}</td>
                        <td>{{ moment(props.item.date).format('LLL') }}</td>
                        <td>P {{ parseFloat(props.item.total_price).toFixed(2) }}</td>
                        <td>{{ datePaidStr(props.item) }}</td>
                    </tr>
                </template>
                <template slot="footer">
                    <tr v-if="!!summary && hideDeleted">
                        <td colspan="4">
                            <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{summary.total_items}}</span> result(s)</div>
                        </td>
                        <td class="font-weight-bold">P {{parseFloat(summary.total_price).toLocaleString()}}</td>
                        <td></td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
        <transaction-dialog v-model="openTransactionDialog" :transactionId="transactionId" @savePayment="savePayment" @deleteTransaction="deleteTransaction" />
    </div>
</template>

<script>
import TransactionDialog from './TransactionDialog.vue';

export default {
    components: {
        TransactionDialog
    },
    data() {
        return {
            cancelSource: null,
            keyword: null,
            sortBy: 'job order number',
            orderBy: 'desc',
            date: null,
            datePaid: null,
            page: 1,
            hideDeleted: true,
            reset: false,
            items: [],
            summary: null,
            loading: false,
            transactionId: null,
            openTransactionDialog: false,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Job order',
                    sortable: false
                },
                {
                    text: 'Customer name',
                    sortable: false
                },
                {
                    text: 'Date created',
                    sortable: false
                },
                {
                    text: 'Price',
                    sortable: false
                },
                {
                    text: 'Date paid',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.reset = true;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get('/api/transactions/by-job-orders', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    date: this.date,
                    datePaid: this.datePaid,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy,
                    hideDeleted: this.hideDeleted
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.reset = false;
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
                this.summary = res.data.summary;
            }).finally(() => {
                this.loading = false;
            });
        },
        previewTransaction(transaction) {
            this.transactionId = transaction.id;
            this.openTransactionDialog = true;
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.load();
        },
        datePaidStr(item) {
            if(item.date_paid) {
                return moment(item.date_paid).format('LLL');
            } else if(item.partial_payment) {
                return moment(item.partial_payment.date).format('LLL') + ' [PARTIAL PAYMENT]';
            } else {
                return "(Not paid)";
            }
        },
        savePayment(transaction) {
            let t = this.items.find(tr => tr.id == transaction.id);
            if(t) {
                t.date_paid = transaction.date_paid;
                t.partial_payment = transaction.partial_payment;
            }
        },
        deleteTransaction(transaction) {
            this.items = this.items.filter(i => i.id != transaction.id);
        },
        joColor(transaction) {
            if(transaction.cancelation_remarks) {
                return '#999'
            } else if(transaction.date_paid != null) {
                return `#31f239`
            } else if(!!transaction.partial_payment) {
                return '#eeb'
            } else {
                return '#f766c2'
            }
        }
    },
    created() {
        this.load();
    },
    watch: {
        hideDeleted(val) {
            this.reset = true;
            this.load();
        }
    }
}
</script>
