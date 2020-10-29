<template>
    <v-dialog :value="value" max-width="800" persistent>
        <v-card class="rounded-card">
            <v-card-title class="title grey--text">Available services</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-tabs v-model="tabIndex">
                    <v-tab>Washes</v-tab>
                    <v-tab>Dries</v-tab>
                    <v-tab-item>
                        <v-data-table :headers="headers" :items="washes" hide-actions :loading="loading">
                            <template v-slot:items="props">
                                <tr>
                                    <td>{{props.item.service_name}}</td>
                                    <td>{{props.item.machine_size}}</td>
                                    <td>{{props.item.minutes}}</td>
                                    <td>{{moment(props.item.created_at).format('LLL')}}</td>
                                    <td>
                                        <v-btn small class="font-weight-bold" :color="props.item.date_paid == null ? `primary` : 'green'" @click="previewTransaction(props.item)">
                                            {{ props.item.job_order }}
                                        </v-btn>
                                    </td>
                                    <td>
                                        <v-tooltip top v-if="isOwner">
                                            <v-btn icon slot="activator" @click="disposeWash(props.item)" :loading="props.item.isDisposing">
                                                <v-icon>delete</v-icon>
                                            </v-btn>
                                            <span>Dispose</span>
                                        </v-tooltip>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                    </v-tab-item>
                    <v-tab-item>
                        <v-data-table :headers="headers" :items="dries" hide-actions :loading="loading">
                            <template v-slot:items="props">
                                <tr>
                                    <td>{{props.item.service_name}}</td>
                                    <td>{{props.item.machine_size}}</td>
                                    <td>{{props.item.minutes}}</td>
                                    <td>{{moment(props.item.created_at).format('LLL')}}</td>
                                    <td>
                                        <v-btn small class="font-weight-bold" color="primary" @click="previewTransaction(props.item)" round>
                                            {{ props.item.job_order }}
                                        </v-btn>
                                    </td>
                                    <td>
                                        <v-tooltip top v-if="isOwner">
                                            <v-btn icon slot="activator" @click="disposeDry(props.item)" :loading="props.item.isDisposing">
                                                <v-icon>delete</v-icon>
                                            </v-btn>
                                            <span>Dispose</span>
                                        </v-tooltip>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                    </v-tab-item>
                </v-tabs>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
            </v-card-actions>
        </v-card>
        <transaction-dialog v-model="openTransactionDialog" :transactionId="transactionId" />
    </v-dialog>
</template>

<script>
import TransactionDialog from '../transaction-reports/TransactionDialog.vue';

export default {
    components: {
        TransactionDialog
    },
    props: [
        'value', 'customerId'
    ],
    data() {
        return {
            transactionId: null,
            openTransactionDialog: false,
            washes: [],
            dries: [],
            loading: false,
            tabIndex: 0,
            headers: [
                {
                    text: 'Service name',
                    sortable: false
                },
                {
                    text: 'Machine Size',
                    sortable: false
                },
                {
                    text: 'Minutes',
                    sortable: false
                },
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Job order',
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
        previewTransaction(transaction) {
            this.transactionId = transaction.transaction_id;
            this.openTransactionDialog = true;
        },
        load() {
            this.loading = true;
            axios.get(`/api/pending-services/${this.customerId}/view-all`).then((res, rej) => {
                this.washes = res.data.customerWashes;
                this.dries = res.data.customerDries;
            }).finally(() => {
                this.loading = false;
            });
        },
        close() {
            this.$emit('input', false);
        },
        disposeWash(wash) {
            if(confirm('Are you sure you want to dispose this pending wash?')) {
                Vue.set(wash, 'isDisposing', true);
                this.$store.dispatch('transaction/disposeService', {
                    serviceType: 'washing',
                    serviceId: wash.id
                }).then((res, rej) => {
                    this.washes = this.washes.filter(w => w.id != wash.id);
                }).finally(() => {
                    Vue.set(wash, 'isDisposing', false);
                })
            }
        },
        disposeDry(dry) {
            if(confirm('Are you sure you want to dispose this pending dry?')) {
                Vue.set(dry, 'isDisposing', true);
                this.$store.dispatch('transaction/disposeService', {
                    serviceType: 'drying',
                    serviceId: dry.id
                }).then((res, rej) => {
                    this.dries = this.dries.filter(w => w.id != dry.id);
                }).finally(() => {
                    Vue.set(dry, 'isDisposing', false);
                })
            }
        }
    },
    watch: {
        value(val) {
            if(val && this.customerId) {
                this.load();
            } else {
                this.washes = [];
                this.dries  = [];
            }
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    }
}
</script>
