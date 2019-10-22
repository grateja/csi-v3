<template>
    <div>
        <h3 class="grey--text title">Expenses list</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 green white--text flat" @click="addExpense">Add new expense</v-btn>
        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>
                <v-data-table hide-actions :items="items" :headers="headers" :loading="loading">
                    <template v-slot:items="props">
                        <td>{{date(props.item.date)}}</td>
                        <td>{{props.item.remarks}}</td>
                        <td>{{props.item.amount}}</td>
                        <td>{{props.item.user.fullname}}</td>
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
        <add-edit-expense @ok="updateList" v-model="openAddExpense" :expense="activeExpense"></add-edit-expense>
    </div>
</template>

<script>
import moment from 'moment';
import AddEditExpense from './AddEditExpense.vue';

export default {
    components: {
        moment,
        AddEditExpense
    },
    data() {
        return {
            page: this.$route.query.page || 1,
            keyword: this.$route.query.keyword,
            openAddExpense: false,
            activeExpense: null,
            totalPage: 0,
            loading: false,
            items: [],
            headers: [
                {
                    sortable: false,
                    text: 'Date'
                },
                {
                    sortable: false,
                    text: 'Remarks'
                },
                {
                    sortable: false,
                    text: 'Amount'
                },
                {
                    sortable: false,
                    text: 'Added by'
                // },
                // {
                //     sortable: false,
                //     text: 'Price'
                // },
                // {
                //     sortable: false,
                //     text: 'Date'
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
            axios.get('/api/search/expenses/self', {
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
        },
        addExpense() {
            this.activeExpense = null;
            this.openAddExpense = true;
        },
        updateList(data) {
            if(data.mode == 'insert') {
                this.items.push(data.expense);
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
