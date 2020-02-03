<template>
    <div>
        <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search customer or Job order number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['job_order', 'customer_name', 'date']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>

            </v-card-text>
        </v-card>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>
                    <v-btn small outline class="font-weight-bold" color="primary" @click="previewTransaction(props.item)">
                        {{ props.item.job_order }}
                    </v-btn>
                </td>
                <td>{{ props.item.customer_name }}</td>
                <td>{{ moment(props.item.date).format('LLL') }}</td>
                <td>P {{ parseFloat(props.item.total_price).toFixed(2) }}</td>
                <td>{{ datePaid(props.item) }}</td>
            </template>
        </v-data-table>
        <v-btn block @click="loadMore" :loading="loading">Load more</v-btn>
        <transaction-dialog v-model="openTransactionDialog" :transactionId="transactionId" @savePayment="savePayment" />
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
            sortBy: 'job_order',
            orderBy: 'desc',
            date: null,
            page: 1,
            reset: false,
            items: [],
            loading: false,
            transactionId: null,
            openTransactionDialog: false,
            headers: [
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
                    sortBy: this.sortBy,
                    orderBy: this.orderBy
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.reset = false;
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                }
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
        datePaid(item) {
            if(item.date_paid) {
                return moment(item.date_paid).format('LLL');
            } else {
                return "(Not paid)";
            }
        },
        savePayment(transaction) {
            let t = this.items.find(tr => tr.id == transaction.id);
            if(t) {
                t.date_paid = transaction.date_paid;
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
