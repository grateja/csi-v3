<template>
    <v-card flat>
        <v-card-text>

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
            <v-divider></v-divider>
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                <template v-slot:items="props">
                    <td>{{ date(props.item.date) }}</td>
                    <td>{{ props.item.jobOrder }}</td>
                    <td>{{ props.item.customerName }}</td>
                    <td>{{ props.item.productName }}</td>
                    <td>P {{ props.item.totalAmount }}</td>
                    <td>{{ date(props.item.datePaid) }}</td>
                    <!-- <td>{{ props.item.quantity }}</td> -->
                    <!-- <td>P {{ props.item.unitPrice }}</td> -->
                    <td>{{ props.item.addedBy }}</td>
                    <td>{{ props.item.paidTo }}</td>
                    <td>
                        <v-tooltip top>
                            <v-btn slot="activator" small icon @click="voidItem(props.item)">
                                <v-icon small>clear</v-icon>
                            </v-btn>
                            <span>Void item</span>
                        </v-tooltip>
                        <!-- <v-tooltip top v-if="props.item.rfid_cards_count > 0">
                            <v-btn slot="activator" small icon :to="`/rfid/cards/c?keyword=${props.item.name}`">
                                <v-icon small>credit_card</v-icon>
                                {{props.item.rfid_cards_count}}
                            </v-btn>
                            <span>{{props.item.rfid_cards_count}} Registered RFID card(s)</span>
                        </v-tooltip> -->
                    </td>
                </template>
            </v-data-table>
        </v-card-text>

        <v-divider class="my-2"></v-divider>
        <v-card-actions>
            <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
            <v-spacer></v-spacer>
            <!-- <v-btn @click="exportDownload" :loading="exporting">
                <v-icon left>archive</v-icon> Download
            </v-btn> -->
        </v-card-actions>

        <v-card v-if="summary" class="grey lighten-4">
            <v-card-title class="title grey--text">Summary</v-card-title>
            <v-divider></v-divider>
            <v-data-table :headers="summaryHeaders" :items="summary" :loading="loading" hide-actions dense>
                <template v-slot:items="props">
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.quantity }}</td>
                    <td>P {{ props.item.amount }}</td>
                </template>
            </v-data-table>
        </v-card>



        <void-product-dialog v-model="openVoidProduct" :productTransaction="activeTransaction" @ok="removeItem"></void-product-dialog>
    </v-card>
</template>
<script>

import VoidProductDialog from './VoidProductDialog.vue';
import DateRangeSelector from '../../../shared/DateRangeSelector.vue';
import moment from 'moment';

export default {
    components: {
        VoidProductDialog,
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
            summary: null,
            activeTransaction: null,
            openTransactionDialog: false,
            openVoidProduct: false,
            headers: [
                {
                    text: 'Date created',
                    sortable: false
                },
                {
                    text: 'Job order',
                    sortable: false
                },
                {
                    text: 'Customer',
                    sortable: false
                },
                {
                    text: 'Product',
                    sortable: false
                },
                // {
                //     text: 'QTY.',
                //     sortable: false
                // },
                // {
                //     text: 'Price',
                //     sortable: false
                // },
                {
                    text: 'Total amount',
                    sortable: false
                },
                {
                    text: 'Date paid',
                    sortable: false
                },
                {
                    text: 'Added by',
                    sortable: false
                },
                {
                    text: 'Paid to',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ],
            summaryHeaders: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Quantity',
                    sortable: false
                },
                {
                    text: 'Amount',
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

            // this.$router.push({
            //     query: {
            //         keyword: this.keyword,
            //         page: this.page,
            //         transactionId: this.transactionId
            //     }
            // });

            this.loading = true;

            axios.get('/api/search/transactions/pos/products/self', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    transactionId: this.$route.query.transactionId ? this.$route.query.transactionId : null,
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
                uri: 'pos-products'
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
        voidItem(product) {
            this.activeTransaction = product;
            this.openVoidProduct = true;
        },
        removeItem(productId) {
            this.items = this.items.filter(i => i.id != productId);
        },
        selectDate() {
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
