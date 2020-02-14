<template>
    <v-container>
        <h3 class="title grey--text">Unpaid transactions</h3>
        <v-divider class="my-3"></v-divider>
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
                <td>{{ parseFloat(props.item.total_price).toFixed(2) }}</td>
            </template>
        </v-data-table>
        <transaction-dialog :transactionId="transactionId" v-model="openTransactionDialog" @savePayment="closePayment" />
    </v-container>
</template>

<script>
import TransactionDialog from '../transaction-reports/TransactionDialog.vue';

export default {
    components: {
        TransactionDialog
    },
    data() {
        return {
            items: [],
            keyword: null,
            date: null,
            cancelSource: null,
            sortBy: 'job_order',
            orderBy: 'asc',
            page: 1,
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
                    text: 'Date saved',
                    sortable: false
                },
                {
                    text: 'Price',
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
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get('/api/transactions/unpaid-transactions', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    date: this.date,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.items = res.data.result.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        previewTransaction(transaction) {
            this.transactionId = transaction.id;
            this.openTransactionDialog = true;
        },
        closePayment(transaction) {
            this.items = this.items.filter(t => t.id != transaction.id);
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
