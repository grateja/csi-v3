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
                <v-combobox class="ml-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['fullname', 'rfid', 'enrolled']" @change="filter"></v-combobox>
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
                        <v-text-field class="ml-1" label="Search rfid or customer name" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Sort by" v-model="sortBy" outline :items="['fullname', 'rfid', 'enrolled']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex shrink>
                        <v-combobox class="ml-1" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>

            </v-card-text>
        </v-card> -->

        <v-btn class="primary" @click="add" round>
            <v-icon left>add</v-icon>
            register rfid card
        </v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{ props.index + 1 }}</td>
                        <td>{{ moment(props.item.enrolled).format('LLL') }}</td>
                        <td>{{ props.item.fullname }}</td>
                        <td>{{ props.item.rfid }}</td>
                        <td>{{ props.item.card_type == 'u' ? 'Unlimited' : 'P ' + parseFloat(props.item.balance).toFixed(2) }}
                            <v-tooltip top v-if="props.item.card_type == 'c'">
                                <v-btn outline icon small slot="activator" color="green" dark @click="loadACard(props.item)">
                                    <v-icon small>how_to_vote</v-icon>
                                </v-btn>
                                <span>Top up/ Load a card</span>
                            </v-tooltip>
                        </td>
                        <td>{{ cardType(props.item) }}</td>
                        <td>
                            <v-btn icon outline v-if="isOwner || !isOwner && props.item.card_type == 'c'" small @click="edit(props.item)">
                                <v-icon small>edit</v-icon>
                            </v-btn>
                            <v-btn icon outline v-if="isOwner || !isOwner && props.item.card_type == 'c'" small @click="deleteCard(props.item)" :loading="props.item.isDeleting">
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

        <rfid-card-dialog :rfidCard="activeRfidCard" v-model="openRfidCardDialog" @save="updateCards" />
        <rfid-load-dialog :rfidCard="activeRfidCard" v-model="openRfidLoadDialog" @save="updateCards"/>
    </div>
</template>

<script>
import RfidCardDialog from './RfidCardDialog.vue';
import RfidLoadDialog from './RfidLoadDialog.vue';

export default {
    components: {
        RfidCardDialog,
        RfidLoadDialog
    },
    data() {
        return {
            keyword: null,
            page: 1,
            date: null,
            sortBy: 'fullname',
            orderBy: 'asc',
            cancelSource: null,
            items: [],
            total: 0,
            loading: false,
            reset: true,
            openRfidCardDialog: false,
            openRfidLoadDialog: false,
            activeRfidCard: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Date enrolled',
                    sortable: false
                },
                {
                    text: 'Card owner',
                    sortable: false
                },
                {
                    text: 'RFID',
                    sortable: false
                },
                {
                    text: 'Balance',
                    sortable: false
                },
                {
                    text: 'Card type',
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
            axios.get('/api/rfid-cards', {
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
        cardType(card) {
            if(card.card_type == 'c') {
                return 'Loyalty card';
            } else {
                return 'Master card';
            }
        },
        edit(rfidCard) {
            this.activeRfidCard = rfidCard;
            this.openRfidCardDialog = true;
        },
        add() {
            this.activeRfidCard = null;
            this.openRfidCardDialog = true;
        },
        updateCards(data) {
            if(data.mode == 'insert') {
                this.activeRfidCard = data.rfidCard;
                this.items.push(data.rfidCard);
            } else if(data.mode == 'load') {
                this.activeRfidCard.balance = data.rfidCard.balance;
            } else {
                this.activeRfidCard.rfid = data.rfidCard.rfid;
            }
        },
        loadACard(rfidCard) {
            this.activeRfidCard = rfidCard;
            this.openRfidLoadDialog = true;
        },
        deleteCard(item) {
            if(confirm('Delete this card?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('rfidcard/deleteRfidCard', item.rfid_card_id).then((res, rej) => {
                    this.items = this.items.filter(card => card.rfid_card_id != item.rfid_card_id);
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
