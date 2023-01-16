<template>
    <v-dialog :value="value" max-width="400" persistent>
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title">Select discount</span>
            </v-card-title>
            <v-progress-linear v-if="retrievingDiscounts" indeterminate height="1"></v-progress-linear>
            <v-card-text v-else-if="!retrievingDiscounts && discounts.length == 0">No discounts available</v-card-text>
            <v-divider v-else></v-divider>
            <v-card-text>
                <v-card v-if="rfidCards.length > 0" :elevation="useRfidCard ? 4 : 0" class="mb-4">
                    <v-card-title class="py-0 my-0">
                        <v-checkbox label="Use discount card" v-model="useRfidCard"></v-checkbox>
                        <v-spacer></v-spacer>
                        <v-btn flat icon v-if="useRfidCard" @click="useRfidCard = false">
                            <v-icon>close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-expand-transition>
                        <div v-if="useRfidCard && selectedCard">
                            <v-divider></v-divider>
                            <v-card-text>
                                <v-layout>
                                    <v-flex xs5 class="text-xs-right mr-3">RFID:</v-flex>
                                    <v-flex xs7>{{selectedCard.rfid}}</v-flex>
                                </v-layout>
                                <v-layout>
                                    <v-flex xs5 class="text-xs-right mr-3">Balance:</v-flex>
                                    <v-flex xs7>Php {{selectedCard.balance}}</v-flex>
                                </v-layout>
                            </v-card-text>
                        </div>
                    </v-expand-transition>
                    <v-expand-transition>
                        <div class="ma-3" v-if="useRfidCard && selectedCard == null">
                            <h3 class="title grey--text">Select card</h3>
                            <v-divider></v-divider>
                            <v-expand-transition>
                                <v-list v-if="rfidCards.length > 0">
                                    <v-list-tile v-for="item in rfidCards" :key="item.id" @click="selectCard(item)" :disabled="item.balance <= 0" :class="{'primary' : !!selectedCard && item.id == selectedCard.id}">
                                        <v-list-tile-content>
                                            <v-list-tile-title class="title">{{item.rfid}}</v-list-tile-title>
                                            <v-list-tile-sub-title>Php {{item.balance}} available</v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </v-list>
                            </v-expand-transition>
                        </div>
                    </v-expand-transition>
                </v-card>
                <v-expand-transition>
                    <div v-if="discounts && !useRfidCard">
                        <h3 class="title grey--text">Select discount</h3>
                        <v-list>
                            <v-list-tile v-for="item in discounts" :key="item.id" @click="select(item)">
                                <v-list-tile-content>
                                    <v-list-tile-title class="title">{{item.name}}</v-list-tile-title>
                                    <v-list-tile-sub-title>{{item.percentage}} % Discount</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </div>
                </v-expand-transition>
            </v-card-text>

            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'customerId'
    ],
    data() {
        return {
            discounts: [],
            rfidCards: [],
            retrievingDiscounts: false,
            retrievingRfids: false,
            useRfidCard: false,
            selectedCard: null,
            selectedDiscount: null
        }
    },
    methods: {
        close() {
            this.selectedCard = null;
            this.$emit('input', false);
        },
        selectCard(card) {
            this.selectedCard = card;
            this.select(this.selectedDiscount);
        },
        select(item) {
            if(item.id == 'loyalty-card' && this.rfidCards.length == 0) {
                alert("Cannot apply loyalty discount for customer without RFID Cards")
                return;
            }
            if(item.id == 'loyalty-card' && this.selectedCard == null) {
                this.useRfidCard = true;
                return;
            }
            this.$emit('setDiscount', {
                discount: item,
                card: this.selectedCard
            });
            this.close();
        },
        loadRfidCards() {
            this.retrievingRfids = true;
            axios.get('/api/rfid-cards/customer-cards', {
                params: {customerId: this.customerId}
            }).then((res, rej) => {
                this.rfidCards = res.data.result;
                this.retrievingRfids = false;
            }).catch(e => {
                this.retrievingRfids = false;
            });
        }
    },
    created() {
        this.retrievingDiscounts = true;
        axios.get('/api/discounts').then((res, rej) => {
            this.discounts = res.data.result;
            this.retrievingDiscounts = false;
        }).catch(e => {
            this.retrievingDiscounts = false;
        });
    },
    watch: {
        value(val) {
            if(val) {
                this.loadRfidCards();
            }
        },
        useRfidCard(val) {
            if(val) {
                this.selectedDiscount = this.discounts.find(d => d.id == 'loyalty-card');
            } else {
                this.selectedCard = null;
                this.selectedDiscount = null;
            }
        }
    }
}
</script>
