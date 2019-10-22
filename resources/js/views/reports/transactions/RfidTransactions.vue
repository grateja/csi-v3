<template>
    <div>
        <v-divider class="my-3"></v-divider>
        <v-card>
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
                <v-data-table hide-actions :items="items" :headers="headers" :loading="loading">
                    <template v-slot:items="props">
                        <td>{{props.item.machine.name}}</td>
                        <td>{{props.item.rfid_card.rfid}}</td>
                        <td>{{props.item.rfid_card.owner_name}}</td>
                        <td>{{props.item.rfid_service_price.name}}</td>
                        <td>{{props.item.rfid_service_price.price}}</td>
                        <td>{{date(props.item.created_at)}}</td>
                        <!-- <td>
                            <v-btn icon small>
                                <v-icon small>settings</v-icon>
                            </v-btn>
                        </td> -->
                    </template>
                    <tr slot="footer" v-if="!!summary" class="font-weight-bold grey lighten-3">
                        <td>Total </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{summary.totalPrice}}</td>
                        <td></td>
                    </tr>
                </v-data-table>
            </v-card-text>
            <v-card-actions>
                <v-pagination v-model="page" :length="totalPage" @input="navigate"></v-pagination>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
import DateRangeSelector from '../../shared/DateRangeSelector.vue';
import moment from 'moment';

export default {
    components: {
        moment,
        DateRangeSelector
    },
    data() {
        return {
            dateRange: null,
            page: this.$route.query.page || 1,
            keyword: this.$route.query.keyword,
            totalPage: 0,
            loading: false,
            items: [],
            summary: null,
            headers: [
                {
                    sortable: false,
                    text: 'Machine'
                },
                {
                    sortable: false,
                    text: 'RFID'
                },
                {
                    sortable: false,
                    text: 'Card owner'
                },
                {
                    sortable: false,
                    text: 'Service name'
                },
                {
                    sortable: false,
                    text: 'Price'
                },
                {
                    sortable: false,
                    text: 'Date'
                // },
                // {
                //     sortable: false,
                //     text: 'Actions'
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
            this.loading = true;
            axios.get('/api/search/transactions/rfid-services/self', {
                params: {
                    page: this.page,
                    keyword: this.keyword,
                    dateRange: this.dateRange
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
                this.summary = res.data.summary;
                this.totalPage = res.data.result.last_page;
            }).finally(() => {
                this.loading = false;
            });
        },
        date(date) {
            return moment(date).format('LLL')
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        exportDownload() {
            this.$store.dispatch('exportdownload/download', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    transactionId: this.$route.query.transactionId ? this.$route.query.transactionId : null,
                    dateRange: this.dateRange
                },
                uri: 'rfid-transactions'
            });
        },
        selectDate() {
            this.load();
        }
    },
    computed: {
        exporting() {
            return this.$store.getters['exportdownload/isLoading'];
        }
    },
    created() {
        this.load();
    }
}
</script>
