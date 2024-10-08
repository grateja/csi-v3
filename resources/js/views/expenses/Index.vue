<template>
    <v-container>
        <h3 class="title white--text">Expenses</h3>
        <v-divider class="my-3"></v-divider>

        <v-layout justify-center>
            <v-flex style="max-width: 500px">
                <v-text-field class="ml-1 translucent-input round-input" label="Search expenses" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
            </v-flex>
        </v-layout>
        <v-layout justify-center>
            <v-flex style="max-width: 220px">
                <v-text-field class="translucent-input round-input" label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
            </v-flex>
            <v-flex style="max-width: 220px">
                <v-combobox class="ml-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['Amount', 'Date', 'Remarks', 'Encoded by', 'Date encoded']" @change="filter"></v-combobox>
            </v-flex>
            <v-flex style="max-width: 220px">
                <v-combobox class="ml-1 translucent-input round-input" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
            </v-flex>
        </v-layout>

        <!-- <v-card>
            <v-card-text>
                <v-layout>
                    <v-flex shrink>
                        <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1" label="Search expense" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['amount', 'date', 'remarks', 'staff_name']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card> -->

        <v-btn class="success ml-0 my-3" round @click="addNewExpense"><v-icon left>add</v-icon> Add new expense</v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.index + 1}}</td>
                        <td>{{ moment(props.item.date).format('LL') }}</td>
                        <td>{{ moment(props.item.created_at).format('MMM DD, YYYY h:mm A') }}</td>
                        <td>{{ props.item.remarks }}</td>
                        <td>P {{ parseFloat(props.item.amount).toFixed(2) }}</td>
                        <td>{{ props.item.staff_name }}</td>
                        <td class="text-xs-right">
                            <v-btn icon small @click="editExpense(props.item)" v-if="props.item.type == 'exp'" outline>
                                <v-icon small>edit</v-icon>
                            </v-btn>
                            <v-btn icon small @click="deleteExpense(props.item)" outline :loading="props.item.isDeleting">
                                <v-icon small>delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
                <template slot="footer">
                    <tr>
                        <td colspan="10">
                            <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{total}}</span> result(s)</div>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
        <expense-dialog v-model="openExpenseDialog" :expense="activeExpese" @save="save" />
    </v-container>
</template>
<script>
import ExpenseDialog from './ExpenseDialog.vue';

export default {
    components: {
        ExpenseDialog
    },
    data() {
        return {
            activeExpese: null,
            openExpenseDialog: false,
            cancelSource: null,
            keyword: null,
            total: 0,
            sortBy: 'date',
            orderBy: 'desc',
            date: null,
            page: 1,
            reset: false,
            items: [],
            loading: false,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Encoded',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'Amount',
                    sortable: false
                },
                {
                    text: 'User`s name',
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
            this.reset = true;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get('/api/expenses', {
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
                this.total = res.data.result.total;
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
        editExpense(expense) {
            this.activeExpese = expense;
            this.openExpenseDialog = true;
        },
        addNewExpense() {
            this.activeExpese = null;
            this.openExpenseDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.expense);
                this.activeExpese = data.expense;
            } else {
                this.activeExpese.date = data.expense.date;
                this.activeExpese.remarks = data.expense.remarks;
                this.activeExpese.amount = data.expense.amount;
                this.activeExpese.staff_name = data.expense.staff_name;
            }
        },
        deleteExpense(item) {
            if(confirm('Delete this expense?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('expense/deleteExpense', item.id).then((res, rej) => {
                    this.items = this.items.filter(i => i.id != item.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        }
    },
    created() {
        this.load();
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    }
}
</script>
