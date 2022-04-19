<template>
    <v-card class="rounded-card">
        <v-card-actions>
            <span class="title">
                Current job order
            </span>
            <v-spacer></v-spacer>
            <v-btn icon small @click="removeCustomer">
                <v-icon small>close</v-icon>
            </v-btn>
        </v-card-actions>
        <v-divider></v-divider>
        <v-card-text>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Customer name:</v-flex>
                <v-flex xs7>{{currentCustomer.name}}
                    <v-tooltip top v-if="currentTransaction && currentTransaction.birthdayToday">
                        <v-icon slot="activator" class="pointer red--text" small right>cake</v-icon>
                        <span>It's customer's birthday today</span>
                    </v-tooltip>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Date :</v-flex>
                <v-flex xs7>
                    <span v-if="currentTransaction && currentTransaction.date">
                        {{moment(currentTransaction.date).format('MMMM DD, YYYY hh:mm A')}}
                    </span>
                    <span v-else class="grey--text font-italic">(Date will appear after saving)</span>
                </v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Job order # :</v-flex>
                <v-flex xs7>
                    <span v-if="currentTransaction && currentTransaction.job_order" class="font-weight-bold">
                        {{currentTransaction.job_order}}
                    </span>
                    <span v-else class="grey--text font-italic">
                        (Job order number will appear after saving)
                    </span>
                </v-flex>
            </v-layout>
        </v-card-text>

            <v-card class="ma-1" v-if="currentTransaction && currentTransaction.posServiceItems.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>WASH/DRY SERVICES</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <template v-for="(item, i) in currentTransaction.posServiceItems">
                    <template>
                        <v-divider :key="i + '-div-services'"></v-divider>
                        <v-layout class="pa-1 pointer transaction-item" :key="i + 'services'" @click="viewServiceItems(item)">
                            <v-flex xs4>
                                <div>{{item.name}}</div>
                            </v-flex>
                            <v-flex xs3>
                                <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                            </v-flex>
                            <v-flex xs2>
                                <div class="text-xs-center">{{item.quantity}}</div>
                            </v-flex>
                            <v-flex xs3>
                                <div class="text-xs-right">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</div>
                            </v-flex>
                        </v-layout>
                    </template>
                    <template v-if="item.category == 'full'">
                        <div :key="i + 'services'" class="transaction-item" @click="viewServiceItems(item)">
                            <v-layout class="px-1 caption font-italic grey--text pointer transaction-item" v-for="(fullServiceItem, fsi) in item.full_service_items" :key="fsi">
                                <v-flex xs4>
                                    <div>- {{fullServiceItem.name}}</div>
                                </v-flex>
                                <v-flex xs3>
                                    <div class="text-xs-right">{{fullServiceItem.price ? 'P ' + parseFloat(fullServiceItem.price).toFixed(2) : 'FREE'}}</div>
                                </v-flex>
                                <v-flex xs2>
                                    <div class="text-xs-center">{{fullServiceItem.quantity}}</div>
                                </v-flex>
                                <v-flex xs3>
                                    <div class="text-xs-right">{{fullServiceItem.total_price ? 'P ' + parseFloat(fullServiceItem.total_price).toFixed(2) : 'FREE'}}</div>
                                </v-flex>
                            </v-layout>
                            <v-layout class="px-1 caption font-italic grey--text pointer transaction-item" v-for="(fullServiceProduct, fsi) in item.full_service_products" :key="fsi">
                                <v-flex xs4>
                                    <div>- {{fullServiceProduct.name}}</div>
                                </v-flex>
                                <v-flex xs3>
                                    <div class="text-xs-right">{{fullServiceProduct.price ? 'P ' + parseFloat(fullServiceProduct.price).toFixed(2) : 'FREE'}}</div>
                                </v-flex>
                                <v-flex xs2>
                                    <div class="text-xs-center">{{fullServiceProduct.quantity}}</div>
                                </v-flex>
                                <v-flex xs3>
                                    <div class="text-xs-right">{{fullServiceProduct.total_price ? 'P ' + parseFloat(fullServiceProduct.total_price).toFixed(2) : 'FREE'}}</div>
                                </v-flex>
                            </v-layout>
                        </div>
                    </template>
                </template>
                <v-divider class="black"></v-divider>
                <v-layout class="pa-1 font-weight-bold">
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posServiceSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posServiceSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout>
            </v-card>


<!-- Products -->

            <v-card class="ma-1" v-if="currentTransaction && currentTransaction.posProductItems.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>PRODUCTS</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <template v-for="(item, i) in currentTransaction.posProductItems">
                    <v-layout :key="i + 'products'" class="px-1">
                        <v-flex xs4>
                            <div>
                                <v-btn icon small class="ma-0 red" outline @click="reduceItems(item)" :loading="item.reducing">
                                    <v-icon small class="red--text">remove</v-icon>
                                </v-btn>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-products'"></v-divider>
                </template>
                <v-divider class="black"></v-divider>
                <v-layout class="pa-1 font-weight-bold" >
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posProductSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posProductSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout>
            </v-card>


<!-- Shoe cleanings -->

            <v-card class="ma-1" v-if="currentTransaction && currentTransaction.posScarpaCleaningItems.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>SCARPA</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <template v-for="(item, i) in currentTransaction.posScarpaCleaningItems">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                <v-btn icon small class="ma-0 red" outline @click="reduceShoeCleaning(item)" :loading="item.reducing">
                                    <v-icon small class="red--text">remove</v-icon>
                                </v-btn>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
                <v-divider class="black"></v-divider>
                <v-layout class="pa-1 font-weight-bold" >
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posScarpaCleaningSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posScarpaCleaningSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout>
            </v-card>

<!-- LAC -->
            <v-card class="ma-1" v-if="currentTransaction && currentTransaction.posLagoonItems.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>LAGOON</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <template v-for="(item, i) in currentTransaction.posLagoonItems">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                <v-btn icon small class="ma-0 red" outline @click="reduceLagoon(item)" :loading="item.reducing">
                                    <v-icon small class="red--text">remove</v-icon>
                                </v-btn>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
                <v-divider class="black"></v-divider>
                <v-layout class="pa-1 font-weight-bold" >
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posLagoonSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posLagoonSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout>
            </v-card>


<!-- End -->


            <!-- <table class="v-table top transaction-items" border="1">
                <tr>
                    <th colspan="4">Services</th>
                </tr>
                <tr>
                    <th>NAME</th>
                    <th>UNIT PRICE</th>
                    <th>QUANTITY</th>
                    <th>TOTAL</th>
                </tr>
                <template v-if="currentTransaction">
                    <tr v-for="item in currentTransaction.posServiceItems" :key="item.id" @click="viewServiceItems(item)">
                        <template v-if="item.category == 'full'">
                            <td class="pl-1" colspan="4">{{item.name}}
                                <table class="caption v-table">
                                    <tr v-for="fullService in item.full_service_items" :key="fullService.id">
                                        <td>- {{fullService.name}}</td>
                                        <td>{{fullService.price}}</td>
                                        <td>{{fullService.quantity}}</td>
                                        <td>{{fullService.total_price}}</td>
                                    </tr>
                                    <tr v-for="fullService in item.full_service_products" :key="fullService.id">
                                        <td>- {{fullService.name}}</td>
                                        <td>{{fullService.price}}</td>
                                        <td>{{fullService.quantity}}</td>
                                        <td>{{fullService.total_price}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                                    </tr>
                                </table>
                            </td>
                        </template>
                        <template v-else>
                            <td class="pl-1">{{item.name}}</td>
                            <td class="text-xs-center">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</td>
                            <td class="text-xs-center">
                                {{item.quantity}}
                                <v-tooltip top>
                                    <v-btn slot="activator" small icon><v-icon small>list</v-icon></v-btn>
                                    <span>View all</span>
                                </v-tooltip>
                            </td>
                            <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                        </template>
                    </tr>
                </template>
                <tr class=" font-weight-bold" v-if="currentTransaction">
                    <td colspan="2" class="pl-1">Total</td>
                    <td class="text-xs-center">{{currentTransaction.posServiceSummary.total_quantity}}</td>
                    <td class="text-xs-center">P {{parseFloat(currentTransaction.posServiceSummary.total_price).toFixed(2)}}</td>
                </tr>
            </table>

            <v-divider class="my-2"></v-divider>

            <table class="v-table" border="1">
                <tr>
                    <th colspan="4">Products</th>
                </tr>
                <tr>
                    <th>NAME</th>
                    <th>UNIT PRICE</th>
                    <th>QUANTITY</th>
                    <th>TOTAL</th>
                </tr>
                <template v-if="currentTransaction">
                    <tr v-for="item in currentTransaction.posProductItems" :key="item.id">
                        <td class="pl-1">{{item.name}}</td>
                        <td class="text-xs-center">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</td>
                        <td class="text-xs-center">
                            {{item.quantity}}
                            <v-tooltip top>
                                <v-btn slot="activator" small icon :loading="item.reducing" @click="reduceItems(item)"><v-icon small>remove</v-icon></v-btn>
                                <span>Remove 1 item</span>
                            </v-tooltip>
                        </td>
                        <td class="text-xs-center">{{item.total_price ? 'P ' + parseFloat(item.total_price).toFixed(2) : 'FREE'}}</td>
                    </tr>
                </template>
                <tr class=" font-weight-bold" v-if="currentTransaction">
                    <td colspan="2" class="pl-1">Total</td>
                    <td class="text-xs-center">{{currentTransaction.posProductSummary.total_quantity}}</td>
                    <td class="text-xs-center">P {{parseFloat(currentTransaction.posProductSummary.total_price).toFixed(2)}}</td>
                </tr>
            </table>

            <v-divider class="my-3"></v-divider> -->

            <v-card v-if="currentTransaction && currentTransaction.partial_payment" class="rounded-card">
                <v-card-title>
                    <span class="grey--text font-weight-bold">Partial payment</span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-layout>
                        <v-flex xs5 class="text-xs-right mr-3">Date:</v-flex>
                        <v-flex xs7>{{moment(currentTransaction.partial_payment.date).format('MMMM DD, YYYY hh:mm A')}}</v-flex>
                    </v-layout>

                    <v-layout class="font-weight-bold">
                        <v-flex xs5 class="text-xs-right mr-3">Amount to pay:</v-flex>
                        <v-flex xs7>P{{parseFloat(currentTransaction.total_price).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-layout v-if="currentTransaction.partial_payment.points_in_peso">
                        <v-flex xs5 class="text-xs-right mr-3">Points:</v-flex>
                        <v-flex xs7>P{{parseFloat(currentTransaction.partial_payment.points_in_peso).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-layout v-if="currentTransaction.partial_payment.card_load_used">
                        <v-flex xs5 class="text-xs-right mr-3">RFID Load:</v-flex>
                        <v-flex xs7>P{{parseFloat(currentTransaction.partial_payment.card_load_used).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5 class="text-xs-right mr-3">Cash:</v-flex>
                        <v-flex xs7>P{{parseFloat(currentTransaction.partial_payment.cash).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-layout class="font-weight-bold">
                        <v-flex xs5 class="text-xs-right mr-3">Balance:</v-flex>
                        <v-flex xs7>P{{parseFloat(currentTransaction.partial_payment.balance).toFixed(2)}}</v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5 class="text-xs-right mr-3">Staff name:</v-flex>
                        <v-flex xs7>{{currentTransaction.partial_payment.paid_to}}</v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>

            <v-card-actions v-if="currentTransaction && !currentTransaction.saved">
                <v-spacer></v-spacer>
                <v-btn class="title" color="#cf0" @click="saveTransaction" round :loading="saving"> <span class="font-weight-bold">{{totalPrice}} </span> &nbsp; confirm</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
            <v-card-actions v-else-if="!!currentTransaction">
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="viewPayment" round>{{totalPrice}}&nbsp;&nbsp;Payment</v-btn>
                <v-btn @click="printClaimStub" round :loading="claimStubLoading">Print claim stub</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        <service-item-dialog v-if="currentTransaction" v-model="openServiceItemDialog" :serviceName="activeServiceItemName" :transactionId="currentTransaction.id"></service-item-dialog>
        <payment-dialog :transaction="currentTransaction" v-model="openPaymentDialog" />
    </v-card>
</template>
<script>
import ServiceItemDialog from './ServiceItemDialog.vue';
import PaymentDialog from './PaymentDialog.vue';

export default {
    components: {
        ServiceItemDialog,
        PaymentDialog
    },
    data() {
        return {
            openPaymentDialog: false,
            openServiceItemDialog: false,
            activeServiceItemName: null
        }
    },
    computed: {
        currentTransaction() {
            return this.$store.getters['postransaction/getCurrentTransaction'];
        },
        saving() {
            return this.$store.getters['postransaction/isSaving'];
        },
        claimStubLoading() {
            return this.$store.getters['printer/claimStubLoading'];
        },
        totalPrice() {
            if(this.currentTransaction) {
                return 'P' + parseFloat(this.currentTransaction.total_price).toFixed(2);
            }
        },
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        }
    },
    methods: {
        removeCustomer() {
            this.$store.commit('postransaction/removeCustomer');
        },
        saveTransaction() {
            this.$store.dispatch('postransaction/saveTransaction', this.currentTransaction.id).then((res, rej) => {

            });
        },
        viewServiceItems(item) {
            this.activeServiceItemName = item.name;
            this.openServiceItemDialog = true;
        },
        reduceItems(item) {
            Vue.set(item, 'reducing', true);
            this.$store.dispatch('postransaction/reduceProduct', {
                productId: item.product_id,
                transactionId: this.currentTransaction.id
            }).finally(() => {
                this.$store.dispatch('postransaction/refreshTransaction').finally(() => {
                    Vue.set(item, 'reducing', false);
                });
            });
        },
        reduceShoeCleaning(item) {
            Vue.set(item, 'reducing', true);
            this.$store.dispatch('postransaction/reduceShoeCleaning', {
                scarpaVariationId: item.scarpa_variation_id,
                transactionId: this.currentTransaction.id
            }).finally(() => {
                this.$store.dispatch('postransaction/refreshTransaction').finally(() => {
                    Vue.set(item, 'reducing', false);
                });
            });
        },
        reduceLagoon(item) {
            Vue.set(item, 'reducing', true);
            this.$store.dispatch('postransaction/reduceLagoon', {
                lagoonId: item.lagoon_id,
                transactionId: this.currentTransaction.id
            }).finally(() => {
                this.$store.dispatch('postransaction/refreshTransaction').finally(() => {
                    Vue.set(item, 'reducing', false);
                });
            });
        },
        viewPayment() {
            this.openPaymentDialog = true;
        },
        printClaimStub() {
            this.$store.dispatch('printer/printClaimStub', {
                transactionId: this.currentTransaction.id
            });
        }
    }
}
</script>
<style lang="scss" scoped>
.transaction-item {
    color: black;
    background-color: white;
    transition: .2s;
}
.transaction-item:hover {
    color: #698fff;
    background-color: #d1ffff;
    transition: .2s;
}
</style>
