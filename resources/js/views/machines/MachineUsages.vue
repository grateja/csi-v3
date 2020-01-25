<template>
    <v-dialog :value="value" max-width="720" persistent>
        <v-card v-if="machine">
            <v-card-title class="title grey--text">Machine usages for {{machine.machine_name}}</v-card-title>
            <date-navigator v-model="date" />
            <v-divider></v-divider>

            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                <template v-slot:items="props">
                    <tr>
                        <td>{{ moment(props.item.created_at).format('hh:mm:ss A') }}</td>
                        <td>{{ props.item.customer_name }}</td>
                        <td>{{ props.item.minutes }}</td>
                        <td>{{ parseFloat(props.item.price).toFixed(2) }}</td>
                        <td>{{ props.item.activation_type }}</td>
                        <td>
                            <v-btn icon small @click="deleteUsage(props.item)" :loading="props.item.isDeleting" v-if="isOwner">
                                <v-icon small>delete</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
            </v-data-table>

            <v-card-actions>
                <v-btn round @click="close">close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import DateNavigator from '../shared/DateNavigator.vue';

export default {
    components: {
        DateNavigator
    },
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            date: moment().format('YYYY-MM-DD'),
            loading: false,
            items: [],
            headers: [
                {
                    text: 'Time',
                    sortable: false
                },
                {
                    text: 'Customer name',
                    sortable: false
                },
                {
                    text: 'Minutes',
                    sortable: false
                },
                {
                    text: 'Price',
                    sortable: false
                },
                {
                    text: 'Activation type',
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
        close() {
            this.$emit('input', false);
        },
        load() {
            this.items = [];
            this.loading = true;
            axios.get(`/api/machines/${this.machine.id}/history`, {
                params: {
                    date: this.date
                }
            }).then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        deleteUsage(item) {
            if(confirm('Delete this item?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('machine/deleteUsage', {
                    usageId: item.id
                }).then((res, rej) => {
                    this.items = this.items.filter(m => m.id != item.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                });
            }
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    },
    watch: {
        date(val) {
            if(val && this.machine) {
                this.load();
            }
        },
        machine(val) {
            if(val) {
                this.date = moment().format('YYYY-MM-DD');
                this.load();
            }
        }
    }
}
</script>
