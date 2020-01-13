<template>
    <v-container>
        <v-layout>
            <v-flex shrink>
                <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter"></v-text-field>
            </v-flex>
            <v-flex>
                <v-text-field label="Search customer" v-model="keyword" append-icon="search" @keyup="filter"></v-text-field>
            </v-flex>
        </v-layout>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{ props.item.job_order }}</td>
                <td>{{ props.item.customer_name }}</td>
                <td>{{ props.item.saved }}</td>
                <td>{{ parseFloat(props.item.total_price).toFixed(2) }}</td>
                <td>
                    <v-btn small round @click="previewTransaction(props.item)">preview</v-btn>
                </td>
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
                },
                {
                    text: '',
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
                    date: this.date,
                    page: this.page
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
