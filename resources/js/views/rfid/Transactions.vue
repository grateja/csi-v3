<template>
    <div>

        <v-layout justify-center>
            <v-flex style="max-width: 500px">
                <v-text-field class="translucent-input round-input" label="Search customer or Job order number" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
            </v-flex>
        </v-layout>
        <v-layout justify-center>
            <v-flex shrink>
                <v-menu offset-y>
                    <v-btn slot="activator" class="translucent" round> <v-icon left>keyboard_arrow_down</v-icon>{{cardTypeStr}}</v-btn>
                    <v-list dense>
                        <v-list-tile @click="setCardType(null)">
                            All cards
                        </v-list-tile>
                        <v-list-tile @click="setCardType('customer')">
                            Customer cards only
                        </v-list-tile>
                        <v-list-tile @click="setCardType('user')">
                            User cards only
                        </v-list-tile>
                    </v-list>
                </v-menu>
            </v-flex>
            <v-flex shrink>
                <v-text-field label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline class="mr-2 round-input translucent-input" style="width: 200px" dense></v-text-field>
            </v-flex>
            <v-flex shrink>
                <v-combobox class="mx-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['Customer name', 'RFID', 'Date', 'Machine name']" @change="filter"></v-combobox>
            </v-flex>
            <v-flex shrink>
                <v-combobox class="ml-2 translucent-input round-input" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
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
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['owner_name', 'rfid', 'created_at', 'machine_name']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>
                <v-menu offset-y>
                    <v-btn slot="activator"> <v-icon left>keyboard_arrow_down</v-icon>{{cardTypeStr}}</v-btn>
                    <v-list dense>
                        <v-list-tile @click="setCardType(null)">
                            All cards
                        </v-list-tile>
                        <v-list-tile @click="setCardType('customer')">
                            Customer cards only
                        </v-list-tile>
                        <v-list-tile @click="setCardType('user')">
                            User cards only
                        </v-list-tile>
                    </v-list>
                </v-menu>
            </v-card-text>
        </v-card> -->

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <td>{{props.index + 1}}</td>
                    <td>{{ props.item.owner_name }}</td>
                    <td>{{ props.item.rfid }}</td>
                    <td>{{ props.item.machine_name }}</td>
                    <td>P{{ parseFloat(props.item.price).toFixed(2) }}</td>
                    <td>{{ props.item.minutes }} Mins</td>
                    <td>{{ moment(props.item.created_at).format('LLL') }}</td>
                    <td>
                        <v-btn outline icon small v-if="isOwner" @click="deleteTransaction(props.item)" :loading="props.item.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                        <v-btn outline icon small @click="print(props.item)" :loading="props.item.isPrinting">
                            <v-icon small>print</v-icon>
                        </v-btn>
                    </td>
                </template>
                <template slot="footer">
                    <tr v-if="!!summary">
                        <td colspan="4">
                            <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{summary.total_items}}</span> result(s)</div>
                        </td>
                        <td class="font-weight-bold">P {{parseFloat(summary.total_price).toLocaleString()}}</td>
                        <td class="font-weight-bold">{{summary.total_minutes}} Mins</td>
                        <td></td>
                        <td></td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" class="translucent" round>Load more</v-btn>
    </div>
</template>

<script>
export default {
    data() {
        return {
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'Customer name',
            orderBy: 'desc',
            cardType: null,
            cancelSource: null,
            items: [],
            summary: null,
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
                    text: 'RFID',
                    sortable: false
                },
                {
                    text: 'Machine name',
                    sortable: false
                },
                {
                    text: 'Price',
                    sortable: false
                },
                {
                    text: 'Minutes',
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
            axios.get('/api/rfid-cards/tap-transactions', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy,
                    date: this.date,
                    cardType: this.cardType
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
            this.page+= 1;
            this.load();
        },
        deleteTransaction(rfidCard) {
            if(confirm("Deleting rfid load transaction also removes the amount credited to the card. Do you want to continue?")) {
                Vue.set(rfidCard, 'isDeleting', true);
                this.$store.dispatch('rfidcard/deleteTransaction', rfidCard.id).then((res, rej) => {
                    this.items = this.items.filter(c => c.id != rfidCard.id);
                }).finally(() => {
                    Vue.set(rfidCard, 'isDeleting', false);
                })
            }
        },
        updateCards(data) {
            if(data.mode == 'insert') {
                this.activeRfidCard = data.rfidCard;
                this.items.push(data.rfidCard);
            } else {
                this.activeRfidCard.rfid = data.rfidCard.rfid;
            }
        },
        setCardType(cardType) {
            this.cardType = cardType;
            this.reset = true;
            this.load();
        },
        print(item) {
            Vue.set(item, 'isPrinting', true);
            this.$store.dispatch('printer/print', {
                entity: 'tap-card',
                transactionId: item.id
            }).finally(() => {
                Vue.set(item, 'isPrinting', false);
            });
        }
    },
    created() {
        this.load();
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        },
        cardTypeStr() {
            if(this.cardType == 'customer') {
                return 'Customer cards only';
            } else if(this.cardType == 'user') {
                return 'User cards only';
            } else {
                return 'All cards';
            }
        }
    }
}
</script>
