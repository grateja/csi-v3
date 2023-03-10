<template>
    <v-dialog :value="value" persistent>
        <v-card>
            <v-card-title>
                <span class="title">Job Orders Created from {{ date | simpleDate }} to {{ until | simpleDate }}</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="$emit('input', false)">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-card class="rounded-card translucent-table" style="overflow-y: auto; max-height: 70vh;">
                    <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                        <template v-slot:items="props">
                            <tr @click="previewTransaction(props.item)" class="pointer" :class="className(props.item)">
                                <td>{{props.index + 1}}</td>
                                <td><div class="font-weight-bold">{{ props.item.job_order }}</div></td>
                                <td>{{props.item.customer_name}}</td>
                                <td>{{ props.item.created_at | simpleDateTime }}</td>
                                <td>{{ props.item.date_paid | simpleDateTime }}</td>
                                <td>
                                    <span v-if="props.item.total_price">
                                        {{props.item.total_price.toFixed(2)}}
                                    </span>
                                    <span v-if="props.item.payment">[{{ props.item.payment.payment_method }}]</span>
                                </td>
                            </tr>
                        </template>
                        <template slot="footer">
                            <tr v-if="totalResult">
                                <td colspan="10">
                                    <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{totalResult}}</span> items(s)</div>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card>
                <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="excelExportContinue">
                        <v-img src="/img/excel-btn.png" width="30px" />
                        export
                    </v-btn>
                    <v-spacer />
                </v-card-actions>
            </v-card-text>
        </v-card>
        <transaction-dialog :transactionId="transactionId" v-model="openTransactionDialog" />
    </v-dialog>
</template>

<script>
import TransactionDialog from '../../transaction-reports/TransactionDialog.vue';

export default {
    components: {
        TransactionDialog
    },
    props: [
        'value', 'date', 'until',
    ],
    data() {
        return {
            openTransactionDialog: false,
            transactionId: null,
            items: [],
            loading: false,
            reset: true,
            cancelSource: null,
            page: 1,
            totalPage: 0,
            totalResult: 0,
            export: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'JobOrder#',
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
                    text: 'Date paid',
                    sortable: false
                },
                {
                    text: 'Amount',
                    sortable: false
                },
            ]
        }
    },
    methods: {
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            axios.get('/api/transactions/by-job-orders', {
                params: {
                    page: this.page,
                    date: this.date,
                    until: this.until,
                    export: this.export,
                    sortBy: 'date paid'
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.items = res.data.result.data;
                    console.log('reset')
                } else {
                    console.log('not reset')
                    console.log('old items', this.items)
                    console.log('new items', res.data.result.data)
                    this.items = [...this.items, ...res.data.result.data]
                    console.log('merged', this.items);
                    // setTimeout(() => {
                    //     window.scrollTo({
                    //         top: document.body.scrollHeight,
                    //         behavior: 'smooth'
                    //     });
                    // }, 10);
                }
                this.totalResult = res.data.result.total;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).finally(() => {
                this.export = null;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.reset = false;
            this.load();
        },
        excelExportContinue() {
            this.export = 'excel';
            console.log(this.date)
            this.$store.dispatch('exportdownload/download', {
                uri: 'job-orders',
                params: {
                    date: this.date,
                    until: this.until,
                }
            })
        },
        previewTransaction(item) {
            this.transactionId = item.id;
            this.openTransactionDialog = true;
        },
        className(item) {
            if(item.date_paid != null) {
                return 'paid'
            } else if(item.partial_payment != null) {
                return 'partial'
            } else {
                return 'unpaid'
            }
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.items = []
                this.reset = true
                this.page = 1
                this.load();
            }
        }
    }
}
</script>

<style>
    .paid {
        background-color: rgb(222, 244, 222);
    }
    .unpaid {
        background-color: rgb(238, 212, 212);
    }
    .partial {
        background-color: antiquewhite;
    }
</style>