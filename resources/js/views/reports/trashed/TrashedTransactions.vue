<template>
    <div>
        <form @submit.prevent="filter">
            <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
        </form>

        <v-divider></v-divider>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{ date(props.item.date) }}</td>
                <td>{{ props.item.customerName }}</td>
                <td>{{ props.item.jobOrder }}</td>
                <td>{{ props.item.remarks }}</td>
                <td>{{ props.item.userName }}</td>
            </template>
            <!-- <tr slot="footer" v-if="!!summary" class="font-weight-bold grey lighten-3">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{summary.totalProducts}}</td>
                <td>{{summary.totalServices}}</td>
                <td>P {{summary.totalServicesAmount + summary.totalProductsAmount}}</td>
                <td></td>
            </tr> -->
        </v-data-table>

        <v-divider class="my-2"></v-divider>

        <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: parseInt(this.$route.query.page) || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeTransaction: null,
            transactionId: null,
            openTransactionDialog: false,
            summary: null,
            headers: [
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Customer name.',
                    sortable: false
                },
                {
                    text: 'Job order',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'User',
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

            this.$router.push({
                query: {
                    keyword: this.keyword,
                    page: this.page
                }
            });

            this.loading = true;

            axios.get('/api/search/trashed/transactions', {
                params: {
                    keyword: this.keyword,
                    page: this.page
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
        navigate(page) {
            this.page = page;
            this.load();
        },
        date(date) {
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
        },
        voidTransaction(transaction) {
            this.transactionId = transaction.id;
            this.openTransactionDialog = true;
        },
        removeTransaction(transactionId) {
            console.log(transactionId);
            this.items = this.items.filter(t => t.id != transactionId);
        }
    },
    computed: {
        isAdmin() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
