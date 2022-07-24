<template>
    <div>
        <h3 class="title grey--text">Customer cards</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn to="/rfid/cards/all" active-class="primary" class="ml-0 white">All</v-btn>
        <v-btn to="/rfid/cards/c" active-class="primary" class="white">Customer cards</v-btn>
        <v-btn to="/rfid/cards/u" active-class="primary" class="white" v-if="isAdmin">Master cards</v-btn>
        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>

                <v-btn v-if="$route.params.cardType != 'all'" class="green ml-0 white--text" flat @click="registerRfid">
                    <v-icon left>add</v-icon>
                    Register new {{$route.params.cardType == 'c' ? 'Customer' : 'Master'}} card
                </v-btn>

                <v-data-table hide-actions :items="items" :headers="headers" :loading="loading">
                    <template v-slot:items="props">
                        <td>{{props.item.rfid}}</td>
                        <td>{{props.item.owner_name}}</td>
                        <td v-if="props.item.unlimited">Unlimited</td>
                        <td v-else>{{props.item.balance}}
                            <v-tooltip top>
                                <v-btn icon small slot="activator" class="green--text" @click="topUp(props.item)">
                                    <v-icon small>add</v-icon>
                                </v-btn>
                                <span>Top up</span>
                            </v-tooltip>
                        </td>
                        <td>
                            <v-btn icon small @click="edit(props.item)">
                                <v-icon small>edit</v-icon>
                            </v-btn>
                        </td>
                    </template>
                </v-data-table>
            </v-card-text>
            <v-card-actions>
                <v-pagination v-model="page" :length="totalPage" @input="navigate"></v-pagination>
            </v-card-actions>
        </v-card>
        <card-registration-dialog v-model="openRegistrationDialog" :rfidCard="activeRfidCard" @save="save"></card-registration-dialog>
        <card-load-dialog v-model="openCardLoadDialog" :rfidCard="activeRfidCard" @save="topUpContinue"></card-load-dialog>
    </div>
</template>

<script>
import CardRegistrationDialog from './CardRegistrationDialog.vue';
import CardLoadDialog from './CardLoadDialog.vue';

export default {
    components: {
        CardRegistrationDialog,
        CardLoadDialog
    },
    data() {
        return {
            cardType: this.$route.params.cardType,
            page: this.$route.query.page | 1,
            keyword: this.$route.query.keyword,
            loading: false,
            totalPage: 0,
            openRegistrationDialog: false,
            openCardLoadDialog: false,
            activeRfidCard: null,
            items: [],
            headers: [
                {
                    sortable: false,
                    text: 'RFID'
                },
                {
                    sortable: false,
                    text: 'Name'
                },
                {
                    sortable: false,
                    text: 'Balance'
                },
                {
                    sortable: false,
                    text: 'Actions'
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
            axios.get(`/api/search/rfid-cards/${this.$route.params.cardType}/self`, {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    customerId: this.$route.query.customerId
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
                this.totalPage = res.data.result.last_page;
            }).finally(() => {
                this.loading = false;
            });
        },
        registerRfid() {
            this.activeRfidCard = null;
            this.openRegistrationDialog = true;
        },
        edit(rfidCard) {
            console.log(rfidCard);
            this.activeRfidCard = rfidCard;
            this.openRegistrationDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.rfidCard);
            } else {
                // this.items.find(c => c.id == data.rfidCard.id).rfid = data.rfidCard.rfid;
                this.activeRfidCard.rfid = data.rfidCard.rfid;
                this.activeRfidCard.unlimited = data.rfidCard.unlimited;
            }
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        topUp(rfidCard) {
            this.activeRfidCard = rfidCard;
            this.openCardLoadDialog = true;
        },
        topUpContinue(rfidCard) {
            this.items.find(rfid => rfid.id == rfidCard.id).balance = rfidCard.balance;
        }
    },
    watch: {
        '$route.params.cardType': {
            handler(val) {
                this.items = [];
                this.keyword = '';
                this.page = 1;
                this.load();
            },
            deep: true,
            immediate: true
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
