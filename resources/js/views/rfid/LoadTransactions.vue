<template>
    <div>

        <v-layout justify-center>
            <v-flex style="max-width: 500px">
                <v-text-field class="ml-1 translucent-input round-input" label="Search rfid or customer name" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
            </v-flex>
        </v-layout>
        <v-layout justify-center>
            <v-flex style="max-width: 220px">
                <v-text-field class="translucent-input round-input" label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
            </v-flex>
            <v-flex style="max-width: 220px">
                <v-combobox class="ml-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['Customer name', 'RFID', 'Date', 'Load amount']" @change="filter"></v-combobox>
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
                        <v-text-field class="ml-1" label="Search customer or RFID" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['customer_name', 'rfid', 'created_at', 'amount']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>

            </v-card-text>
        </v-card> -->

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.index + 1}}</td>
                        <td>{{ props.item.customer_name }}</td>
                        <td>{{ props.item.amount }}</td>
                        <td>{{ props.item.rfid }}</td>
                        <td>{{ props.item.staff_name }}</td>
                        <td>{{ props.item.remarks }}</td>
                        <td>{{ moment(props.item.created_at).format('LLL') }}</td>
                        <td>
                            <v-btn icon small v-if="isOwner" @click="printTransaction(props.item)" :loading="props.item.isPrinting" outline>
                                <v-icon small>print</v-icon>
                            </v-btn>
                            <v-btn icon small v-if="isOwner" @click="deleteTransaction(props.item)" :loading="props.item.isDeleting" outline>
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
        <v-btn round class="translucent" block @click="loadMore" :loading="loading">Load more</v-btn>
    </div>
</template>

<script>
export default {
    data() {
        return {
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'Date',
            orderBy: 'desc',
            cancelSource: null,
            items: [],
            total: 0,
            loading: false,
            reset: true,
            openRfidCardDialog: false,
            activeRfidCard: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Customer name',
                    sortable: false
                },
                {
                    text: 'Amount',
                    sortable: false
                },
                {
                    text: 'RFID',
                    sortable: false
                },
                {
                    text: 'Staff',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'Date',
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
            axios.get('/api/rfid-cards/load-transactions', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy,
                    date: this.date
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.items = res.data.result.data;
                    this.reset = false;
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
            this.page+= 1;
            this.load();
        },
        deleteTransaction(rfidCard) {
            if(confirm("Deleting rfid load transaction also removes the amount credited to the card. Do you want to continue?")) {
                Vue.set(rfidCard, 'isDeleting', true);
                this.$store.dispatch('rfidcard/deleteLoadTransaction', rfidCard.id).then((res, rej) => {
                    this.items = this.items.filter(c => c.id != rfidCard.id);
                }).finally(() => {
                    Vue.set(rfidCard, 'isDeleting', false);
                })
            }
        },
        printTransaction(rfidCard) {
            Vue.set(rfidCard, 'isPrinting', true);
            this.$store.dispatch('printer/rfidLoadTransaction', rfidCard.id).then((res, rej) => {

            }).finally(() => {
                Vue.set(rfidCard, 'isPrinting', false);
            });
        },
        updateCards(data) {
            if(data.mode == 'insert') {
                this.activeRfidCard = data.rfidCard;
                this.items.push(data.rfidCard);
            } else {
                this.activeRfidCard.rfid = data.rfidCard.rfid;
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
