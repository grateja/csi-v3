<template>
    <v-dialog :value="value" max-width="720px" persistent>
        <v-card>
            <v-card-title class="title grey--text">Summary for {{moment(date).format('MMMM DD, YYYY')}}</v-card-title>
            <v-progress-linear class="my-0" height="1" v-if="loading" indeterminate />
            <v-divider v-else></v-divider>
            <v-card-text v-if="result">
                <h3 class="grey--text mt-3">Job orders
                    <v-tooltip top>
                        <v-btn slot="activator" icon small class="mr-0" @click="printPosTransactions" :loading="posTransactionsPrinting">
                            <v-icon>print</v-icon>
                        </v-btn>
                        <span>Print</span>
                    </v-tooltip>
                    <v-tooltip top>
                        <v-btn slot="activator" icon small class="mx-0">
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
                            <v-btn slot="activator" icon small class="mx-0">
                                <v-avatar size="18">
                                    <img src="/img/excel-btn.png" alt="">
                                </v-avatar>
                            </v-btn>
                            <span>Excel</span>
                        </v-tooltip>
                    </dt>
                    <dd class="ml-3 font-weight-bold">P {{parseFloat(result.posCollections.total_price || 0).toFixed(2)}}</dd>
                    <dd class="ml-3"> <span class="font-italic">Including transactions from yesterday and other days</span></dd>
                </dl>

                <h3 class="grey--text mt-3">RFID Tap Card
                    <v-tooltip top>
                        <v-btn small icon slot="activator" :loading="rfidPrinting == 'all'" @click="printRfid('all')">
                            <v-icon>print</v-icon>
                        </v-btn>
                        <span>Print</span>
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
                </dl>

                <h3 class="grey--text mt-3">RFID Load Transactions
                        <v-tooltip top>
                            <v-btn small icon slot="activator" :loading="rfidLoadPrinting" @click="printRfidLoadTransactions">
                                <v-icon>print</v-icon>
                            </v-btn>
                            <span>Print</span>
                        </v-tooltip>
                    </h3>
                <v-divider></v-divider>
                <dl>
                    <dt class="caption grey--text">Total count:</dt>
                    <dd class="ml-3">{{parseInt(result.rfidLoadTransactionSummary.total_count || 0)}} Transaction(s)</dd>

                    <dt class="caption grey--text">Total price:</dt>
                    <dd class="ml-3 font-weight-bold green--text">P {{parseFloat(result.rfidLoadTransactionSummary.total_price || 0).toFixed(2)}}</dd>
                </dl>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close" round>close</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'date'
    ],
    data() {
        return {
            loading: false,
            result: null,
            rfidPrinting: null,
            rfidLoadPrinting: null,
            posCollectionPrinting: false,
            posTransactionsPrinting: false
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.loading = true;
            axios.get(`/api/sales-report/${this.date}/summary`).then((res, reh) => {
                this.result = res.data.result;
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
        excelPosCollections() {

        }
    },
    watch:{
        value(val) {
            if(val && this.date) {
                this.load();
            } else {
                this.result = null;
            }
        }
    }
}
</script>
