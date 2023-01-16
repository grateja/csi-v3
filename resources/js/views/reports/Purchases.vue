<template>
    <div>
        <h4 class="title grey--text">Product purchases</h4>
        <v-divider class="my-3"></v-divider>
        <v-layout>
            <v-flex xs9>
                <v-card>
                    <v-card-text>
                        <form @submit.prevent="filter">
                            <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                        </form>
                        <v-data-table hide-actions :items="items" :headers="headers" :loading="loading">
                            <template v-slot:items="props">
                                <tr>
                                    <td>{{date(props.item.date)}}</td>
                                    <td>{{props.item.productName}}</td>
                                    <td>{{props.item.quantity}}</td>
                                    <td>P {{props.item.unitCost.toFixed(2)}}</td>
                                    <td>P {{props.item.totalCost.toFixed(2)}}</td>
                                    <td>{{props.item.receipt}}</td>
                                    <td>{{props.item.remarks}}</td>
                                    <!-- <td>
                                        <v-btn icon small>
                                            <v-icon small>settings</v-icon>
                                        </v-btn>
                                    </td> -->
                                </tr>
                            </template>
                        </v-data-table>
                    </v-card-text>
                    <v-card-actions>
                        <v-pagination v-model="page" :length="totalPage" @input="navigate"></v-pagination>
                    </v-card-actions>
                </v-card>
            </v-flex>
            <v-flex xs3>
                <v-card v-if="summary">
                    <v-card-title class="title grey--text">
                        Summary
                    </v-card-title>
                    <v-list dense>
                        <v-list-tile v-for="sum in summary" :key="sum.name">
                            <v-list-tile-content>{{sum.name}}</v-list-tile-content>
                            <v-list-tile-action>{{sum.quantity}} Pcs.</v-list-tile-action>
                            <v-list-tile-action>P {{sum.priceSum}}</v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </v-card>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    components: {
        moment
    },
    data() {
        return {
            page: this.$route.query.page || 1,
            keyword: this.$route.query.keyword,
            totalPage: 0,
            loading: false,
            items: [],
            summary: null,
            headers: [
                {
                    sortable: false,
                    text: 'Date'
                },
                {
                    sortable: false,
                    text: 'Product name'
                },
                {
                    sortable: false,
                    text: 'Quantity'
                },
                {
                    sortable: false,
                    text: 'Unit cost'
                },
                {
                    sortable: false,
                    text: 'Total cost'
                },
                {
                    sortable: false,
                    text: 'Receipt no.'
                },
                {
                    sortable: false,
                    text: 'Remarks'
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
            axios.get('/api/search/product-purchases/self', {
                params: {
                    page: this.page,
                    keyword: this.keyword
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
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
        },
        navigate(page) {
            this.page = page;
            this.load();
        }
    },
    created() {
        this.load();
    }
}
</script>
