<template>
    <v-dialog :value="value" max-width="400" persistent>
        <v-card>
            <v-card-text>
                <v-radio-group v-model="selectedCardId">
                    <div v-for="card in cards" :key="card.id" @click="select(card)" class="cursor block">
                        <v-radio v-model="card.id" :label="card.owner_name + '  (' + card.rfid + ')'"></v-radio>
                        <span class="grey--text">P {{ parseFloat(card.balance).toFixed(2)}}</span>
                    </div>
                </v-radio-group>
                <template>
                    <v-expand-transition v-if="!!selectedCard">
                        <div>
                            <v-text-field v-model="amount" label="Amount to use:" outline></v-text-field>
                            <v-text-field v-model="balance" label="Remaing balance :" outline readonly></v-text-field>
                        </div>
                    </v-expand-transition>
                </template>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="ok">OK</v-btn>
                <v-btn @click="cancel">cancel</v-btn>
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
            cards: [],
            selectedCardId: null,
            amount: 0
        }
    },
    methods: {
        loadCards() {
            axios.get('/api/rfid-cards/customer-cards', {
                params: {customerId: this.customerId}
            }).then((res, rej) => {
                this.cards = res.data.result;
            });
        },
        ok() {
            if(this.selectedCardId == null) {
                alert('No card selected');
                return;
            }

            if(this.amount > this.selectedCard.balance) {
                alert('Not enough balance');
                return;
            }

            this.$emit('setCard', {
                cardId: this.selectedCardId,
                amount: this.amount
            });
            this.close();
        },
        cancel() {
            this.amount = 0;
            this.selectedCardId = null;
            this.$emit('cancel');
            this.close();
        },
        close() {
            this.$emit('input', false);
        },
        select(card) {
            this.selectedCardId = card.id;
        }
    },
    computed: {
        selectedCard() {
            return this.cards.find(c => c.id == this.selectedCardId);
        },
        balance() {
            if(this.selectedCard) {
                return this.selectedCard.balance - this.amount;
            }
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.loadCards();
            }
        }
    }
}
</script>
