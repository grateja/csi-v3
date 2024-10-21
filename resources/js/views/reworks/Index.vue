<template>
    <v-container>
        <h3 class="title white--text">Reworks</h3>

        <v-divider class="my-3"></v-divider>

        <v-layout justify-center>
            <v-flex style="max-width: 500px">
                <v-text-field class="translucent-input round-input" label="Search customer or Job order number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
            </v-flex>
        </v-layout>
        <v-layout justify-center>
            <v-flex shrink>
                <v-text-field label="Specify date activated" v-model="date" type="date" append-icon="date" @change="filter" outline class="mr-2 round-input translucent-input" style="width: 200px" dense></v-text-field>
            </v-flex>
            <v-flex shrink>
                <v-combobox class="mx-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['job order number', 'customer name', 'date activated', 'machine name']" @change="filter"></v-combobox>
            </v-flex>
            <v-flex shrink>
                <v-combobox class="ml-2 translucent-input round-input" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
            </v-flex>
        </v-layout>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>
                            <v-btn round small class="font-weight-bold" @click="previewTransaction(props.item.transaction_id)">
                                {{ props.item.job_order }}
                            </v-btn>
                        </td>
                        <td>{{ props.item.machine_name }}</td>
                        <td>{{ props.item.customer_name }}</td>
                        <td>{{ moment(props.item.created_at).format('LLL') }}</td>
                        <td>{{ props.item.remarks }}</td>
                        <td>{{ props.item.account_name }}</td>
                        <!-- <td>P {{ parseFloat(props.item.initial_price).toFixed(2) }}</td> -->
                        <!-- <td>{{ props.item.additional_price ? 'P ' + parseFloat(props.item.additional_price).toFixed(2) : 'Disabled' }}</td> -->
                        <!-- <td>{{ (props.item.additional_time)? props.item.additional_time + 'Mins.' : 'Disabled' }}</td> -->
                        <!-- <td>
                            <v-tooltip top v-if="isOwner">
                                <v-btn slot="activator" small icon @click="edit(props.item, $event)" outline>
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit prices and minutes</span>
                            </v-tooltip>
                        </td> -->
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
        <transaction-dialog v-model="openTransactionDialog" :transactionId="transactionId" />
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
            transactionId: null,
            openTransactionDialog: false,
            cancelSource: null,
            keyword: null,
            sortBy: 'job order number',
            orderBy: 'desc',
            page: 1,
            reset: false,
            items: [],
            loading: false,
            headers: [
                {
                    text: 'Job Order',
                    sortable: false
                },
                {
                    text: 'Machine name',
                    sortable: false
                },
                {
                    text: 'Customer name',
                    sortable: false
                },
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'Activated by',
                    sortable: false
                }
            ],
            date: null
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
            axios.get('/api/re-works', {
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
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.load();
        },
        previewTransaction(item) {
            this.transactionId = item;
            this.openTransactionDialog = true;
        }
    },
    created() {
        this.load();
    }
}
</script>
