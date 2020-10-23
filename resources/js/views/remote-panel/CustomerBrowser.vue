<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card v-if="!!machine">
            <v-card-title class="grey--text"> <span class="title"> Select customer for {{machine.machine_name}}</span>
                <v-spacer></v-spacer>
                <div>
                    <v-progress-circular v-if="ping.requesting" size="24" indeterminate />
                    <v-icon v-else :class="ping">rss_feed</v-icon> <span class="caption" :class="ping.color">{{ping.ms}} ms</span>
                </div>
            </v-card-title>
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
            activeCustomer: null,
            ping: {
                requesting: false,
                color: 'grey--text',
                ms: 0
            }
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
        },
        requestPing() {
            let startTime = new Date().getTime();
            let ping = this.ping;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    ping.ms = new Date().getTime() - startTime;
                    if(ping.ms < 1000) {
                        ping.color = 'green--text';
                    } else if(ping.ms < 2000) {
                        ping.color = 'yellow--text';
                    } else if(ping.ms < 3000) {
                        ping.color = 'orange--text';
                    } else {
                        ping.color = 'red--text';
                    }
                }
            };
            xhttp.open("GET", this.machine.ip_address, true);
            xhttp.send();

            // this.ping.requesting = true;
            // axios.get('http://' + this.machine.ip_address, {timeout: 3000, crossdomain: true}).then((res, rej) => {
            //     this.ping.requesting = false;
            //     console.log('done');
            // }).catch(err => {
            //     this.ping.requesting = false;
            //     console.log('error');
            // });
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
                this.requestPing();
            } else {
                this.customers = [];
            }
        }
    }
}
</script>
