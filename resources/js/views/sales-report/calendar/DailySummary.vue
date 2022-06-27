<template>
    <v-dialog :value="value" max-width="520" persistent>
        <v-card class="rounded-card">
            <v-card-title class="grey--text"><span class="title">Summary for {{moment(date).format('MMMM DD, YYYY')}}</span>
                <v-spacer></v-spacer>
                <v-btn icon sm @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-progress-linear class="my-0" height="1" v-if="loading" indeterminate />
            <v-divider v-else class="mb-3"></v-divider>
            <div style="max-height:70vh; overflow-y: auto" v-if="!loading">

                <v-btn small round :class="{'primary': view == 'simplified'}" @click="view = 'simplified'">
                    <v-icon small>format_align_justify</v-icon>
                    simplified
                </v-btn>
                <v-btn small round :class="{'primary': view == 'breakdown'}" @click="view = 'breakdown'">
                    <v-icon small>format_align_right</v-icon>
                    breakdown
                </v-btn>
                <!-- <v-divider class="my-3"></v-divider> -->

                <!-- <h3 class="title gray--text ml-3">{{newCustomers}} New customer(s)</h3> -->

                <v-divider class="mb-4"></v-divider>
                <customers :newCustomers="newCustomers" />
                <job-orders :posSummary="posSummary" :view="view" />
                <used-services :usedServices="usedServices" :view="view" />
                <used-products :usedProducts="usedProducts" :view="view" />
                <scarpa-cleanings :usedScarpa="usedScarpa" :view="view" />
                <lagoon :usedLagoon="usedLagoon" :view="view" />
                <rfid-transactions :rfidCard="rfidCard" :view="view" />
                <rfid-load :rfidLoad="rfidLoad" :view="view" />
                <total-sales :totalSales="totalSales" :view="view" />
                <collections :collections="collections" :view="view" />
                <cashless :cashless="cashless" :view="view" />
                <expenses :expenses="expenses" :view="view" />
                <discounts :discounts="discounts" :view="view" />
                <deposit :deposit="deposit" />

                <!-- <h3 class="title gray--text ml-3">Job Orders</h3>
                <v-divider></v-divider>
                <v-card-text v-if="posSummary">
                    <div class="px-3 mx-3">
                        <v-layout>
                            <v-flex xs4>
                                <span>Fully Paid</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{posSummary.fully_paid.total_jo}}</v-flex>
                            <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.fully_paid.total_sales || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs4>
                                <span>Partially Paid</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{posSummary.partial_payments.total_jo}}</v-flex>
                            <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.partial_payments.total_sales || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs4>
                                <span>Unpaid</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{posSummary.unpaid.total_jo}}</v-flex>
                            <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.unpaid.total_sales || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout class="font-weight-bold">
                            <v-flex xs4>
                                <span>Total</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{posSummary.pos_transactions.total_jo}}</v-flex>
                            <v-flex xs4 class="text-xs-right">P{{parseFloat(posSummary.pos_transactions.total_sales || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                    </div>
                    <v-divider></v-divider>
                </v-card-text> -->


                <!-- <h3 class="title gray--text ml-3">RFID Tap Card Transactions</h3>
                <v-divider></v-divider>
                <v-card-text v-if="rfidCard">
                    <div class="px-3 mx-3">
                        <v-layout>
                            <v-flex xs6>
                                <span>Master card</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(rfidCard.users_card || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs6>
                                <span>Customer card</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(rfidCard.customers_card || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout class="font-weight-bold">
                            <v-flex xs6>
                                <span>Total</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(rfidCard.total_price || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </div>
                </v-card-text> -->

                <!-- <h3 class="title gray--text ml-3">RFID Load Transactions</h3>
                <v-divider></v-divider>
                <v-card-text v-if="rfidLoad">
                    <div class="px-3 mx-3">
                        <v-layout>
                            <v-flex xs6>
                                <span>Customer card</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{rfidLoad.total_count}}</v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(rfidLoad.total_price || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout class="font-weight-bold">
                            <v-flex xs6>
                                <span>Total</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{rfidLoad.total_count}}</v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(rfidLoad.total_price || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </div>
                </v-card-text> -->

                <!-- <h3 class="title gray--text ml-3">Collections</h3>
                <span class="ml-3 font-italic grey--text caption">Job orders paid to date including previous transactions</span>
                <v-divider></v-divider>
                <v-card-text v-if="collections">
                    <div class="px-3 mx-3">
                        <v-layout>
                            <v-flex xs6>
                                <span>Fully paid Job Orders</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.fullyPaid || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs6>
                                <span>Partially paid Job Orders</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.partiallyPaid || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs6>
                                <span>Master cards</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.rfidTap || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs6>
                                <span>Load transactions</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.rfidLoad || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout class="font-weight-bold">
                            <v-flex xs6>
                                <span>Total</span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(collections.total || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </div>
                </v-card-text> -->

                <!-- <h3 class="title gray--text ml-3">Expenses</h3>
                <v-divider></v-divider>
                <v-card-text v-if="expenses">
                    <div class="px-3 mx-3">
                        <v-layout>
                            <v-flex xs6>
                                <span>Product purchases</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{expenses.productPurchases.total_count}}</v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(expenses.productPurchases.total_cost || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs6>
                                <span>Other</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{expenses.otherExpenses.total_count}}</v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(expenses.otherExpenses.total_expense || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout class="font-weight-bold">
                            <v-flex xs6>
                                <span>Total</span>
                            </v-flex>
                            <v-flex xs4 class="text-xs-center">{{expenses.total}}</v-flex>
                            <v-flex xs6 class="text-xs-right">P{{parseFloat(expenses.total || 0).toFixed(2)}}</v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                    </div>
                </v-card-text> -->


                <v-card-text>
                    <!-- <v-layout>
                        <v-flex xs1 class="text-xs-right">
                            <v-icon class="ma-1">collections_bookmark</v-icon>
                        </v-flex>
                        <v-flex x9>
                            <h3 class="ml-1 mt-1 grey--text">
                                Job Orders
                            </h3>
                            <div class="ml-1">
                                <v-layout>
                                    <v-flex xs4>Paid</v-flex>
                                    <v-flex xs4>{{result.posTransactionSummary.paid_count}} item(s)</v-flex>
                                    <v-flex xs4>P {{parseFloat(result.posTransactionSummary.paid_total || 0).toFixed(2)}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                                <v-layout>
                                    <v-flex xs4>Unpaid</v-flex>
                                    <v-flex xs4>{{result.posTransactionSummary.unpaid_count}} item(s)</v-flex>
                                    <v-flex xs4>P {{parseFloat(result.posTransactionSummary.unpaid_total || 0).toFixed(2)}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                                <v-layout>
                                    <v-flex xs4>Total</v-flex>
                                    <v-flex xs4>{{result.posTransactionSummary.total_count}} item(s)</v-flex>
                                    <v-flex xs4>P {{parseFloat(result.posTransactionSummary.total_price || 0).toFixed(2)}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                            </div>
                        </v-flex>
                        <v-flex xs2>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mr-0" @click="printPosTransactions" :loading="posTransactionsPrinting">
                                    <v-icon>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'pos-transactions'" @click="excelDownload('pos-transactions')">
                                    <v-avatar size="24">
                                        <img src="/img/excel-btn.png" alt="">
                                    </v-avatar>
                                </v-btn>
                                <span>Export to Excel</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>

                    <v-divider class="my-3 transparent"></v-divider>

                    <v-layout>
                        <v-flex xs1 class="text-xs-right">
                            <v-icon class="ma-1">save</v-icon>
                        </v-flex>
                        <v-flex xs9>
                            <h3 class="ml-1 mt-1 grey--text">
                                Collected job orders
                            </h3>
                            <div class="ml-1">
                                <v-layout>
                                    <v-flex xs4></v-flex>
                                    <v-flex xs4>{{result.posCollections.total_count}} item(s)</v-flex>
                                    <v-flex xs4>P {{parseFloat(result.posCollections.total_price || 0).toFixed(2)}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                                <div class="font-italic">Job orders paid to date including previous transactions</div>
                            </div>
                        </v-flex>
                        <v-flex xs2>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mr-0" @click="printPosCollection" :loading="posCollectionPrinting">
                                    <v-icon>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'pos-collections'" @click="excelDownload('pos-collections')">
                                    <v-avatar size="24">
                                        <img src="/img/excel-btn.png" alt="">
                                    </v-avatar>
                                </v-btn>
                                <span>Export to Excel</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>

                    <v-divider class="my-3 transparent"></v-divider>

                    <v-layout>
                        <v-flex xs1 class="text-xs-right">
                            <v-icon class="ma-1">credit_card</v-icon>
                        </v-flex>
                        <v-flex xs9>
                            <h3 class="ml-1 mt-1 grey--text">
                                RFID Card
                            </h3>
                            <div class="ml-1">
                                <v-layout>
                                    <v-flex xs4>Master card</v-flex>
                                    <v-flex xs4></v-flex>
                                    <v-flex xs4>P {{parseFloat(result.rfidCardTransactionSummary.users_card || 0).toLocaleString()}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                                <v-layout>
                                    <v-flex xs4>Customer card</v-flex>
                                    <v-flex xs4></v-flex>
                                    <v-flex xs4>P {{parseFloat(result.rfidCardTransactionSummary.customers_card || 0).toLocaleString()}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                            </div>
                        </v-flex>
                        <v-flex xs2>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mr-0" @click="printRfid" :loading="rfidPrinting == 'all'">
                                    <v-icon>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'rfid-transactions'" @click="excelDownload('rfid-transactions')">
                                    <v-avatar size="24">
                                        <img src="/img/excel-btn.png" alt="">
                                    </v-avatar>
                                </v-btn>
                                <span>Export to Excel</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>

                    <v-divider class="my-3 transparent"></v-divider>

                    <v-layout>
                        <v-flex xs1 class="text-xs-right">
                            <v-icon class="ma-1">card_membership</v-icon>
                        </v-flex>
                        <v-flex xs9>
                            <h3 class="ml-1 mt-1 grey--text">
                                RFID Load Transactions
                            </h3>
                            <div class="ml-1">
                                <v-layout>
                                    <v-flex xs4></v-flex>
                                    <v-flex xs4>{{parseInt(result.rfidLoadTransactionSummary.total_count || 0)}} item(s)</v-flex>
                                    <v-flex xs4>P {{parseFloat(result.rfidLoadTransactionSummary.total_price || 0).toLocaleString()}}</v-flex>
                                </v-layout>
                                <v-divider></v-divider>
                            </div>
                        </v-flex>
                        <v-flex xs2>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mr-0" @click="printRfidLoadTransactions" :loading="rfidLoadPrinting">
                                    <v-icon>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'rfid-load-transactions'" @click="excelDownload('rfid-load-transactions')">
                                    <v-avatar size="24">
                                        <img src="/img/excel-btn.png" alt="">
                                    </v-avatar>
                                </v-btn>
                                <span>Export to Excel</span>
                            </v-tooltip>
                        </v-flex>
                    </v-layout>

                    <v-divider class="my-4"></v-divider>

                    <v-layout>
                        <v-flex xs3 class="text-xs-right">
                            <v-icon class="ma-1">bookmarks</v-icon>
                        </v-flex>
                        <v-flex xs4>
                            <h3 class="ml-1 mt-1">
                                Total Sales
                            </h3>
                        </v-flex>
                        <v-flex xs3>
                            <h3>P {{parseFloat(result.totalSales).toLocaleString()}}</h3>
                        </v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs3 class="text-xs-right">
                            <v-icon class="ma-1">move_to_inbox</v-icon>
                        </v-flex>
                        <v-flex xs4>
                            <h3 class="ml-1 mt-1">
                                Total Collections
                            </h3>
                        </v-flex>
                        <v-flex xs3>
                            <h3>P {{parseFloat(result.totalCollections).toLocaleString()}}</h3>
                        </v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs3 class="text-xs-right">
                            <v-icon class="ma-1">save</v-icon>
                        </v-flex>
                        <v-flex xs4>
                            <h3 class="ml-1 mt-1">
                                Total Expenses
                            </h3>
                        </v-flex>
                        <v-flex xs3>
                            <h3>P {{parseFloat(result.totalExpenses).toLocaleString()}}</h3>
                        </v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs3 class="text-xs-right">
                            <v-icon class="ma-1">account_balance_wallet</v-icon>
                        </v-flex>
                        <v-flex xs4>
                            <h3 class="ml-1 mt-1">
                                Total Deposit
                            </h3>
                        </v-flex>
                        <v-flex xs3>
                            <h3>P {{parseFloat(result.totalDeposit).toLocaleString()}}</h3>
                        </v-flex>
                    </v-layout> -->
                    <!-- <h3 class="grey--text mt-3">Job orders
                        <v-tooltip top>
                            <v-btn slot="activator" icon small class="mr-0" @click="printPosTransactions" :loading="posTransactionsPrinting">
                                <v-icon>print</v-icon>
                            </v-btn>
                            <span>Print</span>
                        </v-tooltip>
                        <v-tooltip top>
                            <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'pos-transactions'" @click="excelDownload('pos-transactions')">
                                <v-avatar size="24">
                                    <img src="/img/excel-btn.png" alt="">
                                </v-avatar>
                            </v-btn>
                            <span>Excel</span>
                        </v-tooltip>
                    </h3>
                    <v-divider></v-divider>
                    <dl>
                        <dt class="caption grey--text">Paid ({{result.posTransactionSummary.paid_count}}) </dt>
                        <dd class="ml-3 font-weight-bold green--text">P {{parseFloat(result.posTransactionSummary.paid_total || 0).toFixed(2)}}</dd>

                        <dt class="caption grey--text">Unpaid ({{result.posTransactionSummary.unpaid_count}})</dt>
                        <dd class="ml-3">P {{parseFloat(result.posTransactionSummary.unpaid_total || 0).toFixed(2)}}</dd>

                        <dt class="caption grey--text">Total ({{result.posTransactionSummary.total_count}})</dt>
                        <dd class="ml-3 font-weight-bold">P {{parseFloat(result.posTransactionSummary.total_price || 0).toFixed(2)}}</dd>

                        <dt class="caption grey--text">Collections ({{result.posCollections.total_count}})
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mr-0" @click="printPosCollection" :loading="posCollectionPrinting">
                                    <v-icon small>print</v-icon>
                                </v-btn>
                                <span>Print collections</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-btn slot="activator" icon small class="mx-0"  :loading="excelDownloading == 'pos-collections'" @click="excelDownload('pos-collections')">
                                    <v-avatar size="18">
                                        <img src="/img/excel-btn.png" alt="">
                                    </v-avatar>
                                </v-btn>
                                <span>Excel</span>
                            </v-tooltip>
                        </dt>
                        <dd class="ml-3 font-weight-bold">P {{parseFloat(result.posCollections.total_price || 0).toFixed(2)}}</dd>
                        <dd class="ml-3"> <span class="font-italic">Including transactions from yesterday and other days</span></dd>
                    </dl> -->

                    <!-- <h3 class="grey--text mt-3">RFID Tap Card
                        <v-tooltip top>
                            <v-btn small icon slot="activator" :loading="rfidPrinting == 'all'" @click="printRfid('all')">
                                <v-icon>print</v-icon>
                            </v-btn>
                            <span>Print</span>
                        </v-tooltip>
                        <v-tooltip top>
                            <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'rfid-transactions'" @click="excelDownload('rfid-transactions')">
                                <v-avatar size="24">
                                    <img src="/img/excel-btn.png" alt="">
                                </v-avatar>
                            </v-btn>
                            <span>Excel</span>
                        </v-tooltip>
                    </h3>
                    <v-divider></v-divider>
                    <dl>
                        <dt class="caption grey--text">Users card:
                            <v-tooltip top>
                                <v-btn small icon slot="activator" :loading="rfidPrinting == 'u'" @click="printRfid('u')">
                                    <v-icon small>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                        </dt>
                        <dd class="ml-3 font-weight-bold green--text">P {{parseFloat(result.rfidCardTransactionSummary.users_card || 0).toFixed(2)}}</dd>

                        <dt class="caption grey--text">Customers card:
                            <v-tooltip top>
                                <v-btn small icon slot="activator" :loading="rfidPrinting == 'c'" @click="printRfid('c')">
                                    <v-icon small>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                        </dt>
                        <dd class="ml-3">P {{parseFloat(result.rfidCardTransactionSummary.customers_card || 0).toFixed(2)}}</dd>
                    </dl> -->

                    <!-- <h3 class="grey--text mt-3">RFID Load Transactions
                            <v-tooltip top>
                                <v-btn small icon slot="activator" :loading="rfidLoadPrinting" @click="printRfidLoadTransactions">
                                    <v-icon>print</v-icon>
                                </v-btn>
                                <span>Print</span>
                            </v-tooltip>
                        <v-tooltip top>
                            <v-btn slot="activator" icon small class="mx-0" :loading="excelDownloading == 'rfid-load-transactions'" @click="excelDownload('rfid-load-transactions')">
                                <v-avatar size="24">
                                    <img src="/img/excel-btn.png" alt="">
                                </v-avatar>
                            </v-btn>
                            <span>Excel</span>
                        </v-tooltip>
                    </h3>
                    <v-divider></v-divider>
                    <dl>
                        <dt class="caption grey--text">Total count:</dt>
                        <dd class="ml-3">{{parseInt(result.rfidLoadTransactionSummary.total_count || 0)}} Transaction(s)</dd>

                        <dt class="caption grey--text">Total price:</dt>
                        <dd class="ml-3 font-weight-bold green--text">P {{parseFloat(result.rfidLoadTransactionSummary.total_price || 0).toFixed(2)}}</dd>
                    </dl> -->
                </v-card-text>
            </div>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close" round>close</v-btn>
                <v-btn @click="printAll" round>print</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import UsedProducts from './dailysummary/UsedProducts.vue'
import UsedServices from './dailysummary/UsedServices.vue';
import ScarpaCleanings from './dailysummary/ScarpaCleanings.vue';
import Lagoon from './dailysummary/Lagoon.vue';
import JobOrders from './dailysummary/JobOrders.vue';
import RfidTransactions from './dailysummary/RfidTransactions.vue';
import RfidLoad from './dailysummary/RfidLoad.vue';
import Collections from './dailysummary/Collections.vue';
import Cashless from './dailysummary/Cashless.vue';
import Expenses from './dailysummary/Expenses.vue';
import Discounts from './dailysummary/Discounts.vue';
import TotalSales from './dailysummary/TotalSales.vue';
import Deposit from './dailysummary/Deposit.vue';
import Customers from './dailysummary/Customers.vue';

export default {
    components: {
        UsedProducts,
        UsedServices,
        ScarpaCleanings,
        Lagoon,
        JobOrders,
        RfidTransactions,
        RfidLoad,
        Collections,
        Cashless,
        Expenses,
        Discounts,
        TotalSales,
        Deposit,
        Customers
    },
    props: [
        'value', 'date'
    ],
    data() {
        return {
            view: 'simplified',
            loading: false,
            excelDownloading: null,
            rfidPrinting: null,
            rfidLoadPrinting: null,
            exportingPosTransactions: false,
            posCollectionPrinting: false,
            posTransactionsPrinting: false,
            posSummary: null,
            rfidCard: null,
            rfidLoad: null,
            collections: null,
            cashless: null,
            expenses: null,
            discounts: null,
            usedProducts: null,
            usedServices: null,
            usedScarpa: null,
            usedLagoon: null,
            newCustomers: 0,
            totalSales: null,
            deposit: 0
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.loading = true;
            axios.get(`/api/sales-report/${this.date}/summary`).then((res, reh) => {
                this.posSummary = res.data.posSummary;
                this.rfidCard = res.data.rfidCard;
                this.rfidLoad = res.data.rfidLoad;
                this.collections = res.data.collections;
                this.cashless = res.data.cashless;
                this.expenses = res.data.expenses;
                this.discounts = res.data.discounts;
                this.usedProducts = res.data.usedProducts;
                this.usedServices = res.data.usedServices;
                this.usedScarpa = res.data.usedScarpa;
                this.usedLagoon = res.data.usedLagoon;
                this.newCustomers = res.data.newCustomers;
                this.totalSales = res.data.totalSales;
                this.deposit = res.data.totalDeposit;
            }).finally(() => {
                this.loading = false;
            });
        },
        printRfid(cardType) {
            this.rfidPrinting = cardType;
            this.$store.dispatch('printer/rfidTransactions', {
                date: this.date,
                cardType
            }).finally(() => {
                this.rfidPrinting = null;
            });
        },
        printPosCollection() {
            this.posCollectionPrinting = true;
            this.$store.dispatch('printer/posCollections', {
                date: this.date
            }).finally(() => {
                this.posCollectionPrinting = false;
            });
        },
        printPosTransactions() {
            this.posTransactionsPrinting = true;
            this.$store.dispatch('printer/posTransactions', {
                date: this.date
            }).finally(() => {
                this.posTransactionsPrinting = false;
            });
        },
        printRfidLoadTransactions() {
            this.rfidLoadPrinting = true;
            this.$store.dispatch('printer/rfidLoadTransactions', {
                date: this.date
            }).finally(() => {
                this.rfidLoadPrinting = false;
            });
        },
        excelDownload(uri) {
            this.excelDownloading = uri;
            console.log(uri);
            this.$store.dispatch('exportdownload/download', {
                uri,
                params: {
                    date: this.date
                }
            }).finally(() => {
                this.excelDownloading = null;
            });
        },
        printAll() {
            this.$store.dispatch('printer/printDailySale', this.date).then((res, rej) => {
                console.log(res.data);
            })
        }
    },
    watch:{
        value(val) {
            if(val && this.date) {
                this.load();
            }
        }
    }
}
</script>
