<template>
    <v-dialog :value="value" max-width="1024" persistent>
        <v-card>
            <v-card-title class="title grey--text">Service items: {{serviceName}}</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <tr>
                            <td>
                                <v-icon small v-if="props.item.saved" color="success">checked</v-icon>
                            </td>
                            <td>{{props.item.name}}</td>
                            <td>{{moment(props.item.created_at).format('MMM DD, YYYY hh:mm:ss A')}}</td>
                            <td>{{props.item.used ? moment(props.item.used).format('MMM DD, YYYY hh:mm:ss A') : ''}}</td>
                            <td>{{props.item.machine_name}}</td>
                            <td>
                                <v-btn small icon @click="deleteItem(props.item)" :loading="props.item.isDeleting">
                                    <v-icon small>delete</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table>

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
        'value', 'eluxServiceId', 'transactionId'
    ],
    data() {
        return {
            loading: false,
            items: [],
            serviceName: null,
            headers: [
                {
                    text: 'Saved',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Date Added',
                    sortable: false
                },
                {
                    text: 'Date Used',
                    sortable: false
                },
                {
                    text: 'Machine',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                },
            ]
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.loading = true;
            axios.get(`/api/transactions/${this.transactionId}/view-elux-service-items`, {
                params: {
                    eluxServiceId: this.eluxServiceId
                }
            }).then((res, rej) => {
                this.items = res.data.result;
                this.serviceName = res.data.serviceName;
            }).finally(() => {
                this.loading =  false;
            });
        },
        deleteItem(item) {
            if(confirm('Are you sure you want to remove this item?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('transaction/deleteEluxServiceItem', item.service_transaction_item_id).then((res, rej) => {
                    this.items = this.items.filter(i => i.service_transaction_item_id != res.data.eluxServiceTransactionItem.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            } else {
                this.items = [];
            }
        }
    }
}
</script>
