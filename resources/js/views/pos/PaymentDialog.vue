<template>
    <v-dialog max-width="820" :value="value" persistent>
        <v-card>
            <v-card-title class="title">Payment</v-card-title>

            <v-progress-linear v-if="loading" indeterminate></v-progress-linear>
            <v-divider></v-divider>

            <v-card-text>
                <v-layout row wrap>
                    <v-flex>
                        <v-card class="mr-3 mb-4">
                            <v-card-text>
                                <h4 class="title grey--text">Customer info</h4>
                                <v-divider></v-divider>
                                <dl v-if="!!customer">
                                    <dt class="caption grey--text">Customer name :</dt>
                                    <dd class="ml-3">{{customer.name}}</dd>

                                    <template v-if="customer.address">
                                        <dt class="caption grey--text">Address :</dt>
                                        <dd class="ml-3">{{customer.address}}</dd>
                                    </template>


                                    <!-- <dt class="caption grey--text">Loyalty :</dt>
                                    <dd>
                                        <v-list dense>
                                            <v-list-tile v-for="l in loyalty" :key="l.service_id">
                                                {{l.service.name}}
                                            </v-list-tile>
                                        </v-list>
                                    </dd> -->

                                    <dt class="caption grey--text">Current points :</dt>
                                    <dd class="ml-3">{{currentPoints}}</dd>

                                    <dt class="caption grey--text">Points in peso :</dt>
                                    <dd class="ml-3">{{pointsInPeso}}
                                        <v-btn small @click="usePoints">{{formData.pointsInPeso}} use</v-btn>
                                    </dd>

                                </dl>
                                <v-card v-if="!!customerCards && customerCards.length" dark class="blue">
                                    <v-card-title class="caption white--text">Customer cards</v-card-title>
                                    <v-data-table :items="customerCards" :headers="headers" hide-actions>
                                        <template v-slot:items="props">
                                            <tr @click="useCard(props.item)">
                                                <td>{{props.item.rfid}}</td>
                                                <td>P {{props.item.balance}}</td>
                                                <td>P {{props.item.use}}</td>
                                            </tr>
                                        </template>
                                    </v-data-table>
                                </v-card>
                                <!-- <v-list dense>
                                    <v-list-tile outline v-for="rfidCard in customerCards" :key="rfidCard.id" @click="useCard(rfidCard)">
                                        <v-list-tile-action>
                                            {{rfidCard.rfid}}
                                        </v-list-tile-action>
                                        <v-spacer></v-spacer>
                                        <v-list-tile-action>
                                            P {{rfidCard.balance}} Bal.
                                        </v-list-tile-action>
                                    </v-list-tile>
                                </v-list> -->

                            </v-card-text>
                        </v-card>


                        <h4 class="title grey--text">Transaction summary</h4>
                        <v-divider></v-divider>


                        <dl v-if="transaction">
                            <dt class="caption grey--text">Services total amount</dt>
                            <dd class="ml-3">P {{serviceAmount}}</dd>
                            <dt class="caption grey--text">Products total amount</dt>
                            <dd class="ml-3">P {{productAmount}}</dd>
                            <dt class="caption grey--text">Grand total amount</dt>
                            <dd class="ml-3 font-weight-bold title">P {{serviceAmount + productAmount}}</dd>
                            <dt class="caption grey--text">Earning points</dt>
                            <dd class="ml-3 green--text font-weight-bold">{{earningPoints}}</dd>
                        </dl>
                    </v-flex>
                    <v-flex>
                        <v-card outline>
                            <v-card-text>
                                <v-text-field v-if="usedCards > 0" label="Use cards balance" :value="usedCards" readonly></v-text-field>

                                <v-text-field label="Cash" v-model="formData.cash"></v-text-field>

                                <v-card dark class="blue">
                                    <v-card-text>
                                        <v-combobox label="Discount" :items="discounts" item-text="name" v-model="discount" @change="setDiscount"></v-combobox>

                                        <v-text-field v-if="!!discount && discount.discount_type == 'c'" label="Enter discount in peso" v-model="cashDiscount"></v-text-field>

                                        <!-- <pre>P {{discountInPeso}}</pre>
                                        <pre>{{discountInPercent}} %</pre> -->
                                    </v-card-text>
                                    <v-card-actions v-if="discount">
                                        <v-btn small @click="discount = null">Cancel discount</v-btn>
                                    </v-card-actions>
                                </v-card>

                                <!-- <pre>{{discount}}</pre> -->

                                <!-- <v-expand-transition>
                                    <template v-if="balance > 0"> -->
                                        <v-text-field label="Balance" :value="balance" :readonly="true" class="red--text" hint="Not enought cash" :error-messages="errors.get('balance')"></v-text-field>
                                    <!-- </template>
                                </v-expand-transition> -->

                                <v-text-field label="Change" :value="change" :readonly="true"></v-text-field>

                                <v-textarea label="Remarks" rows="3" v-model="formData.remarks"></v-textarea>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn class="primary" small @click="pay" :loading="saving">Ok</v-btn>
                                <v-btn @click="close" small>Close</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-divider></v-divider>
        </v-card>
        <use-card-dialog v-model="openUseCard" :rfidCard="activeCard" @ok="deductCard"></use-card-dialog>
        <points-dialog v-model="openUsePoints" :currentPoints="currentPoints" :pointsInPeso="pointsInPeso" @ok="deductPoints"></points-dialog>
    </v-dialog>
</template>

<script>
import UseCardDialog from './UseCardDialog.vue';
import PointsDialog from './PointsDialog.vue';

export default {
    components: {
        UseCardDialog,
        PointsDialog
    },
    props: [
        'value'
    ],
    data() {
        return {
            headers: [
                {
                    text: 'RFID',
                    sortable: false
                },
                {
                    text: 'Balance',
                    sortable: false
                },
                {
                    text: 'Use',
                    sortable: false
                }
            ],
            loyalty: [],
            customerCards: null,
            currentPoints: 0,
            serviceAmount: 0,
            productAmount: 0,
            earningPoints: 0,
            pointsInPeso: 0,
            openUseCard: false,
            openUsePoints: false,
            activeCard: null,
            discounts: [],
            discount: null,
            cashDiscount: 0,
            loading: false,
            formData: {
                cash: 0,
                customerId: null,
                remarks: null,
                customerCards: [],
                discount: 0,
                pointsInPeso: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        pay() {
            if(!this.transaction) {
                alert('No transaction');
                return;
            }

            if(!this.customer) {
                alert('No customer selected');
                return;
            }

            this.formData.customerId = this.customer.id;
            this.formData.discount = this.discountInPercent;

            this.$store.dispatch('payment/proceedToPayment', {
                transactionId: this.transaction.id,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('ok', res.data);
            });
        },
        get() {
            this.formData.customerCards = [];
            this.formData.cash = 0;
            this.formData.remarks = null;
            this.formData.pointsInPeso = 0;
            this.formData.discount = 0;
            this.discount = null;
            this.loading = true;
            axios.get(`/api/payments/${this.transaction.id}`).then((res, rej) => {
                this.loyalty = res.data.loyalty;
                this.currentPoints = res.data.currentPoints;
                this.serviceAmount = res.data.totalServiceAmount;
                this.productAmount = res.data.totalProductAmount;
                this.earningPoints = res.data.earningPoints;
                this.customerCards = res.data.customerCards;
                this.pointsInPeso = res.data.pointsInPeso;
                // this.formData.cash = this.grandTotal;
                this.discounts = res.data.discounts;
                console.log('Transaction', res.data);
            }).finally(() => {
                this.loading = false;
            });

            // axios.get(`/api/customers/${this.customer.id}/loyalty-services`).then((res, rej) => {
            //     console.log(res.data);
            // });
        },
        useCard(rfidCard) {
            this.activeCard = rfidCard;
            this.openUseCard = true;
        },
        usePoints() {
            this.openUsePoints = true;
        },
        deductCard(amount) {
            Vue.set(this.activeCard, 'use', parseFloat(amount));
            this.formData.customerCards = this.customerCards.filter(c => c.use > 0)
                .map(c => {
                    return {
                        id: c.id,
                        use: c.use
                    };
                });
            // let card = this.formData.customerCards.find(c => c.id == this.activeCard.id);
            // if(card) {
            //     card.use = amount;
            // } else {
            //     this.formData.customerCards.push(this.activeCard);
            // }
        },
        setDiscount(val) {
            if(val) {
                if(val.discount_type == 'p') {
                    this.formData.discount = val.percentage;
                    this.cashDiscount = this.discountInPeso;
                } else if(val.discount_type == 'c') {
                    this.formData.discount = 0;

                }
                console.log('discount', val);
            }
        },
        deductPoints(amount) {
            this.formData.pointsInPeso = amount;
            console.log('Amount', amount);
        }
    },
    computed: {
        errors() {
            return this.$store.getters['payment/getErrors'];
        },
        customer() {
            return this.$store.getters['customer/getCustomer'];
        },
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        },
        saving() {
            return this.$store.getters['payment/isLoading'];
        },
        grandTotal() {
            return parseFloat(this.serviceAmount) + parseFloat(this.productAmount);
        },
        change() {
            console.log('cash', this.formData.cash);
            console.log('service', this.serviceAmount);
            console.log('product', this.productAmount);
            console.log('grand total', this.grandTotal);
            console.log('discount in peso', this.discountInPeso);

            if(parseFloat(this.formData.cash) + parseFloat(this.usedCards) + parseFloat(this.formData.pointsInPeso) + this.discountInPeso > this.grandTotal) {

                console.log(this.usedCards);

                return parseFloat(parseFloat(this.formData.cash) + parseFloat(this.discountInPeso) + parseFloat(this.usedCards) + parseFloat(this.formData.pointsInPeso) - this.grandTotal).toFixed(2);
            } else {
                return 0;
            }
        },
        balance() {
            console.log('cash from balance', parseFloat(this.formData.cash | 0));
            if(this.grandTotal - this.usedCards - this.discountInPeso - parseFloat(this.formData.pointsInPeso) - parseFloat(this.formData.cash | 0) > 0) {
                return parseFloat(this.grandTotal - this.formData.cash - this.usedCards - this.discountInPeso - parseFloat(this.formData.pointsInPeso)).toFixed(2);
            } else {
                return 0;
            }
        },
        usedCards() {
            if(this.formData.customerCards) {
                return parseFloat(this.formData.customerCards.reduce((sum, c) => sum + c.use, 0)).toFixed(2);
            }
            return 0;
        },
        discountInPeso() {
            if(this.cashDiscount && this.discount && this.discount.discount_type == 'c') {
                return parseFloat(this.cashDiscount).toFixed(2);
            } else if(this.discount && this.discount.discount_type == 'p'){
                return parseFloat(this.grandTotal * this.formData.discount / 100).toFixed(2);
            } else {
                return 0;
            }
        },
        discountInPercent() {
            if(this.discount) {
                return parseFloat((this.cashDiscount / this.grandTotal) * 100).toFixed(2);
            }
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.get();
            }
        }
    }
}
</script>
