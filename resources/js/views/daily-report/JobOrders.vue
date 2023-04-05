<template>
    <div>

        <v-layout class="font-weight-bold">
            <v-flex xs1 class="text-xs-center">DATE</v-flex>
            <v-flex xs1 class="text-xs-center">JO#</v-flex>
            <v-flex xs2 class="text-xs-center">CUSTOMER</v-flex>
            <v-flex xs1 class="text-xs-center">TOTAL</v-flex>
            <v-flex xs2 class="text-xs-center">DISCOUNT</v-flex>
            <v-flex xs1 class="text-xs-center">AMT.DUE</v-flex>
            <v-flex xs4 class="text-xs-center">PAYMENT</v-flex>
        </v-layout>

        <v-divider></v-divider>

        <div v-for="jobOrder in items" :key="jobOrder.id">
            <v-layout @click="$emit('openJobOrder', jobOrder.id)">
                <v-flex xs1>
                    <div class="text-xs-center">{{ jobOrder.date | simpleDate }}</div>
                </v-flex>
                <v-flex xs1>
                    <div class="text-xs-center">{{jobOrder.job_order}}</div>
                </v-flex>
                <v-flex xs2 class="text-xs-center">{{jobOrder.customer_name}}</v-flex>
                <v-flex xs1 class="text-xs-center">{{jobOrder.total_price | peso}}</v-flex>
                <v-flex xs2 class="green--text text-xs-center">
                    <div v-if="jobOrder.payment && jobOrder.payment.discount">
                        <div>{{ jobOrder.payment.discount_name }}({{ jobOrder.payment.discount }}%)</div>
                        <div class="font-weight-bold">-{{ jobOrder.payment.discount_in_peso | peso }}</div>
                    </div>
                </v-flex>
                <v-flex xs1 class="font-weight-bold text-xs-center">
                    <span v-if="jobOrder.payment && jobOrder.payment.discount">{{ jobOrder.payment.discounted_price | peso }}</span>
                    <span v-else>{{ jobOrder.total_price | peso }}</span>
                </v-flex>
                <v-flex xs4>
                    <div class="text-xs-center" v-if="jobOrder.cancelation_remarks || jobOrder.deleted_at != null">
                        {{ jobOrder.cancelation_remarks }}
                        <ul>
                            <li v-for="remarks in jobOrder.remarks">{{ remarks.remarks }}</li>
                        </ul>
                    </div>
                    <div v-if="jobOrder.payment || jobOrder.partial_payment">
                        <div v-if="jobOrder.partial_payment">
                            <div  class="font-weight-bold text-xs-center">
                                <v-chip small class="ml-0" color="#eeeebb">Partial payment <template v-if="jobOrder.payment == null">only</template></v-chip>
                            </div>
                            <v-layout class="font-italic grey--text">
                                <v-flex xs1><v-icon small>today</v-icon></v-flex>
                                <v-flex xs7>{{jobOrder.partial_payment.date | simpleDateTime}}</v-flex>
                                <v-flex xs4 class="text-xs-right" v-if="jobOrder.partial_payment.or_number">[OR#: {{ jobOrder.partial_payment.or_number }}]</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.partial_payment.cash">
                                <v-flex xs1><v-icon small>money</v-icon></v-flex>
                                <v-flex xs7>Cash received:</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.partial_payment.cash | peso }}</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.partial_payment.change">
                                <v-flex xs1><v-icon small>autorenew</v-icon></v-flex>
                                <v-flex xs7>Change:</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.partial_payment.change | peso }}</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.partial_payment.balance" :class="{'red--text' : jobOrder.payment == null}">
                                <v-flex xs1><v-icon small>announcement</v-icon></v-flex>
                                <v-flex xs7>Balance:</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.partial_payment.balance | peso }}</v-flex>
                            </v-layout>
                        </div>
                        <v-divider v-if="jobOrder.payment && jobOrder.partial_payment" />
                        <div v-if="jobOrder.payment">
                            <div class="font-weight-bold text-xs-center">
                                <v-chip small class="ml-0" color="green">Full payment</v-chip>
                            </div>
                            <v-layout>
                                <v-flex xs1><v-icon small>today</v-icon></v-flex>
                                <v-flex xs7>{{jobOrder.payment.date | simpleDateTime}}</v-flex>
                                <v-flex xs4 class="text-xs-right" v-if="jobOrder.payment.or_number">[OR#: {{ jobOrder.payment.or_number }}]</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.payment.cash">
                                <v-flex xs1><v-icon small>money</v-icon></v-flex>
                                <v-flex xs7>Cash received:</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.payment.cash | peso }}</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.payment.change">
                                <v-flex xs1><v-icon small>autorenew</v-icon></v-flex>
                                <v-flex xs7>Change:</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.payment.change | peso }}</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.payment.cash_less_provider" class="font-italic">
                                <v-flex xs1><v-icon small>speaker_phone</v-icon></v-flex>
                                <v-flex xs7>{{jobOrder.payment.cash_less_provider}} (REF#:{{ jobOrder.payment.cash_less_reference_number }})</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.payment.cash_less_amount | peso }}</v-flex>
                            </v-layout>
                            <v-layout v-if="jobOrder.payment.points" class="font-italic">
                                <v-flex xs1><v-icon small>stars</v-icon></v-flex>
                                <v-flex xs7>{{jobOrder.payment.points}} pts. used</v-flex>
                                <v-flex xs4 class="text-xs-right">{{ jobOrder.payment.points_in_peso | peso }}</v-flex>
                            </v-layout>
                        </div>

                        <v-layout>
                            <v-flex xs8></v-flex>
                            <v-flex xs4></v-flex>
                        </v-layout>
                    </div>
                    <div v-else class="font-weight-bold text-xs-center">
                        <v-chip small class="ml-0" color="red">Unpaid</v-chip>
                    </div>
                </v-flex>
            </v-layout>
            <v-divider></v-divider>
        </div>
        <!-- <pre>{{ total }}</pre> -->
        <v-layout class="font-weight-bold" v-if="total">
            <v-flex xs4>TOTAL</v-flex>
            <v-flex xs1 class="text-xs-center">{{total.totalAmount | peso}}</v-flex>
            <v-flex xs2 class="text-xs-center">-{{total.totalDiscounts | peso}}</v-flex>
            <v-flex xs1 class="text-xs-center">{{total.totalAmountDue | peso}}</v-flex>
            <v-flex xs4 class="font-weight-bold">
                <v-layout :class="summary.balance > 0 ? 'red--text' : 'green--text'">
                    <v-flex>Balance</v-flex>
                    <v-flex class="text-xs-right">{{ summary.balance | peso }}</v-flex>
                </v-layout>
                <v-layout v-if="summary.collected">
                    <v-flex>Cash</v-flex>
                    <v-flex class="text-xs-right">{{ summary.collected | peso }}</v-flex>
                </v-layout>
                <v-layout v-if="summary.cashless">
                    <v-flex>Cashless</v-flex>
                    <v-flex class="text-xs-right">{{ summary.cashless | peso }}</v-flex>
                </v-layout>
                <v-layout v-if="summary.points">
                    <v-flex>Points</v-flex>
                    <v-flex class="text-xs-right">{{ summary.points | peso }}</v-flex>
                </v-layout>
                <v-layout v-if="summary.rfid">
                    <v-flex>RFID</v-flex>
                    <v-flex class="text-xs-right">{{ summary.rfid | peso }}</v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {
    props: ['items'],
    data() {
        return {

        }
    },
    computed: {
        total() {
            if(this.items) {
                var totalAmount = this.items.reduce((sum, item) => sum + parseFloat(item.total_price), 0);
                var totalDiscounts = this.items.reduce((sum, item) => {
                        if(item.payment) {
                            return sum + item.payment.discount_in_peso;
                        }
                        return sum;
                    }, 0);
                return {
                    totalAmount,
                    totalDiscounts,
                    totalAmountDue: totalAmount - totalDiscounts
                }
            }
        },
        summary() {
            if(this.items) {
                var cashless = 0;
                var points = 0;
                var rfid = 0;
                var collected = 0;
                var balance = 0;
                this.items.forEach(item => {
                    if(item.payment) {
                        cashless += item.payment.cash_less_amount;
                        points += item.payment.points_in_peso;
                        rfid += item.payment.rfid;
                        collected += item.payment.collected;
                    }
                    if(item.payment == null && item.partial_payment) {
                        balance += item.partial_payment.balance;
                        collected += item.partial_payment.cash;
                    }
                    if(item.payment == null && item.partial_payment == null) {
                        balance += item.total_price;
                    }
                })
                // var cashless = this.items.reduce((sum, item) => {
                //     if(item.payment) {
                //         return sum + item.payment.cash_less_amount;
                //     }
                //     return sum;
                // }, 0);
                return {
                    cashless,
                    points,
                    rfid,
                    collected,
                    balance
                }
            }
        }
    }
}
</script>
