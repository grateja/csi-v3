<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card v-if="!!machine">
            <v-card-title class="grey--text title">Select customer for {{machine.machine_name}}</v-card-title>
            <v-card-text>
                <v-text-field v-model="keyword" append-icon="search" label="Filter customer name" :loading="loading" @keyup="loadCustomers" ref="keyword"></v-text-field>
                <div v-if="loading">
                    Loading...
                </div>
                <div v-else-if="customers.length == 0">
                    No customer with services
                </div>

                <v-list v-if="customers.length">
                    <v-list-tile v-for="customer in customers" :key="customer.id" @click="selectCustomer(customer)">
                        <v-list-tile-content>
                            <v-list-tile-title>{{customer.name}}</v-list-tile-title>
                            <v-list-tile-sub-title v-if="customer.customer_washes_count">{{customer.customer_washes_count}} available wash</v-list-tile-sub-title>
                            <v-list-tile-sub-title v-if="customer.customer_dries_count">{{customer.customer_dries_count}} available dry</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
            </v-card-actions>
        </v-card>
        <service-browser v-model="openServiceBrowser" :customer="activeCustomer" :machine="machine" @activated="machineActivated" />
    </v-dialog>
</template>

<script>
import ServiceBrowser from './ServiceBrowser.vue';

export default {
    components: {
        ServiceBrowser
    },
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            customers: [],
            keyword: null,
            loading: false,
            openServiceBrowser: false,
            activeCustomer: null
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        loadCustomers() {
            this.loading = true;
            axios.get('/api/pending-services/customers', {
                params: {
                    keyword: this.keyword,
                    machineType: this.machine.machine_type
                }
            }).then((res, rej) => {
                this.customers = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        selectCustomer(customer) {
            this.activeCustomer = customer;
            this.openServiceBrowser = true;
        },
        machineActivated(data) {
            this.$emit('machineActivated', data);
            this.close();
        }
    },
    // computed: {
    //     machineSize() {
    //         if(!!this.machine) {
    //             return this.machine.machine_type[0] == 'r' ? 'REGULAR' : 'TITAN';
    //         }
    //         return null;
    //     },
    //     serviceType() {
    //         if(!!this.machine) {
    //             console.log(this.machine);
    //             return this.machine.machine_type[1] == 'w' ? 'washing' : 'drying';
    //         }
    //         return null;
    //     }
    // },
    watch: {
        value(val) {
            if(val && this.machine) {
                this.loadCustomers();
                setTimeout(() => {
                    this.$refs.keyword.$el.querySelector('input').select();
                }, 500);
            } else {
                this.customers = [];
            }
        }
    }
}
</script>
