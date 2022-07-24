<template>
    <div>
        <h3 class="title grey--text">RFID Top up Transactions</h3>
        <v-divider class="my-3"></v-divider>
        <v-card>
            <v-card-text>

                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>
                <v-data-table hide-actions :items="items" :headers="headers" :loading="loading">
                    <template v-slot:items="props">
                        <td>{{props.item.rfid_card.rfid}}</td>
                        <td>{{props.item.rfid_card.owner_name}}</td>
                        <td>{{props.item.amount}}</td>
                        <td>{{props.item.remarks}}</td>
                        <td>{{date(props.item.created_at)}}</td>
                        <!-- <td>
                            <v-btn icon small>
                                <v-icon small>settings</v-icon>
                            </v-btn>
                        </td> -->
                    </template>
                </v-data-table>
            </v-card-text>
            <v-card-actions>
                <v-pagination v-model="page" :length="totalPage" @input="navigate"></v-pagination>
            </v-card-actions>
        </v-card>
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
            headers: [
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
                    text: 'Amount'
                },
                {
                    sortable: false,
                    text: 'Remarks'
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
            axios.get('/api/search/transactions/rfid-top-up/self', {
                params: {
                    page: this.page,
                    keyword: this.keyword
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
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
        }
    },
    created() {
        this.load();
    }
}
</script>
