<template>
    <v-card v-if="currentTransaction">
        <v-card-text>
            <dl>
                <dt>Date :</dt>
                <dd>{{currentTransaction && currentTransaction.date ? moment(currentTransaction.date).format('MMMM DD, YYYY hh:mm A') : 'Date will appear upon saving'}}</dd>

                <dt>Job order :</dt>
                <dd>{{currentTransaction ? currentTransaction.job_order || 'Job order number will appear upon saving' : null}}</dd>
            </dl>


            <table class="v-table" border="1">
                <tr>
                    <th colspan="4">Services</th>
                </tr>
                <tr>
                    <th>NAME</th>
                    <th>UNIT PRICE</th>
                    <th>QUANTITY</th>
                    <th>TOTAL</th>
                </tr>
                <tr v-for="item in currentTransaction.posServiceItems" :key="item.id" @click="viewServiceItems(item)">
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
                </tr>
                <tr class=" font-weight-bold">
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
                <tr class=" font-weight-bold">
                    <td colspan="2" class="pl-1">Total</td>
                    <td class="text-xs-center">{{currentTransaction.posProductSummary.total_quantity}}</td>
                    <td class="text-xs-center">P {{parseFloat(currentTransaction.posProductSummary.total_price).toFixed(2)}}</td>
                </tr>
            </table>


            <v-card-actions v-if="currentTransaction && !currentTransaction.saved">
                <v-spacer></v-spacer>
                <v-btn class="title" color="#cf0" @click="saveTransaction" round :loading="saving"> <span class="font-weight-bold">{{totalPrice}} </span> &nbsp; confirm</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
            <v-card-actions v-else>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="viewPayment" round>Payment</v-btn>
                <v-btn @click="printClaimStub" round :loading="claimStubLoading">Print claim stub</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card-text>
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
                return 'P' + parseFloat(this.currentTransaction.total_amount).toFixed(2);
            }
        }
    },
    methods: {
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
