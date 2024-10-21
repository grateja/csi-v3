<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card v-if="!!machine">
            <v-card-title class="grey--text"> <span class="title"> Select customer for {{machine.machine_name}}</span>
                <v-spacer></v-spacer>
                <!-- <div>
                    <v-progress-circular v-if="ping.requesting" size="24" indeterminate />
                    <div v-else>
                        <v-tooltip left v-if="ping.ms < 5000 && ping.ms > 0">
                            <v-icon class="pointer" slot="activator" :class="ping.color">rss_feed</v-icon>
                            <span class="caption">{{ping.ms}} ms </span>
                        </v-tooltip>
                        <v-tooltip left v-else>
                            <v-icon class="pointer red--text" slot="activator">rss_feed</v-icon>
                            <span class="caption">{{machine.machine_name}} failed to respond</span>
                        </v-tooltip>
                    </div>
                </div> -->
                <v-btn small round @click="testConnection" >{{testing ? 'Cancel' : 'Test Connection'}}
                    <v-progress-circular v-if="testing" indeterminate height="5" class="ml-1"></v-progress-circular>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field v-model="keyword" append-icon="search" label="Filter customer name" :loading="loading" @keyup="loadCustomers" ref="keyword"></v-text-field>
                <div v-if="loading">
                    Loading...
                </div>
                <div v-else-if="customers.length == 0">
                    No customer with services
                </div>
                <v-btn class="primary" @click="openOSLDialog = true">OSL</v-btn>
                <v-list v-if="customers.length" style="max-height: 60vh; overflow-y: auto">
                    <v-list-tile v-for="customer in customers" :key="customer.id" @click="selectCustomer(customer)">
                        <v-list-tile-content>
                            <v-list-tile-title>{{customer.name}}</v-list-tile-title>
                            <v-list-tile-sub-title v-if="customer.customer_washes_count">{{customer.customer_washes_count}} available wash</v-list-tile-sub-title>
                            <v-list-tile-sub-title v-if="customer.customer_dries_count">{{customer.customer_dries_count}} available dry</v-list-tile-sub-title>
                            <v-list-tile-sub-title v-if="customer.elux_tokens_count">{{customer.elux_tokens_count}} available</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
                <v-btn v-if="allowRework" @click="$emit('rework', machine)" round :disabled="testing">Rework</v-btn>
            </v-card-actions>
        </v-card>
        <service-browser v-model="openServiceBrowser" :customer="activeCustomer" :machine="machine" @activated="machineActivated" />
        <o-s-l-dialog v-model="openOSLDialog" :machine="machine" @activated="machineActivated" />
    </v-dialog>
</template>

<script>
import ServiceBrowser from './ServiceBrowser.vue';
import OSLDialog from './OSLDialog.vue';

export default {
    components: {
        ServiceBrowser,
        OSLDialog
    },
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            openOSLDialog: false,
            customers: [],
            cancelSource: null,
            keyword: null,
            loading: false,
            testing: false,
            openServiceBrowser: false,
            activeCustomer: null,
            ping: {
                requesting: false,
                color: 'grey--text',
                ms: 0,
                xhttp: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.cancel();
        },
        loadCustomers() {
            this.loading = true;
            axios.get('/api/pending-services/customers', {
                params: {
                    keyword: this.keyword,
                    machineType: this.machine.machine_type,
                    model: this.machine.model
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
            this.cancel();
        },
        machineActivated(data) {
            this.$emit('machineActivated', data);
            this.close();
        },
        testConnection() {
            if(this.testing) {
                this.cancel();
                return;
            }
            if(this.machine) {
                this.testing = true;
                this.cancelSource = axios.CancelToken.source();

                axios.get(`/api/remote/machines/test-connection/${this.machine.id}`, {
                    cancelToken: this.cancelSource.token
                }).then((res, rej) => {

                }).finally(() => {
                    this.testing = false;
                });

            }
        },
        cancel() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        requestPing() {
            this.ping.requesting = true;
            let startTime = new Date().getTime();
            this.ping.xhttp = new XMLHttpRequest();
            this.ping.xhttp.timeout = 5000;
            this.ping.xhttp.onreadystatechange = (e) => {
                e.currentTarget.onload = () => {
                    console.log('loaded');
                    this.ping.ms = new Date().getTime() - startTime;
                    if(this.ping.ms < 1000) {
                        this.ping.color = 'green--text';
                    } else if(this.ping.ms < 2000) {
                        this.ping.color = 'yellow--text';
                    } else if(this.ping.ms < 3000) {
                        this.ping.color = 'orange--text';
                    } else {
                        this.ping.color = 'red--text';
                    }
                };
                e.currentTarget.ontimeout = () => {
                    console.log('timeout');
                    this.ping.ms = new Date().getTime() - startTime;
                };
                e.currentTarget.onloadend = () => {
                    this.ping.requesting = false;
                    this.ping.xhttp = null;
                }
                // if (e.currentTarget.readyState == 4 && e.currentTarget.status == 200) {
                //     ping.ms = new Date().getTime() - startTime;
                //     if(ping.ms < 1000) {
                //         ping.color = 'green--text';
                //     } else if(ping.ms < 2000) {
                //         ping.color = 'yellow--text';
                //     } else if(ping.ms < 3000) {
                //         ping.color = 'orange--text';
                //     } else {
                //         ping.color = 'red--text';
                //     }
                // } else if(e.currentTarget.readyState == 4 && e.currentTarget.status <= 0) {
                //     ping.ms = 5000;
                //     ping.color = 'red--text';
                // }
            };
            this.ping.xhttp.open("GET", 'http://' + this.machine.ip_address + '/details', true);
            this.ping.xhttp.send();

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
    watch: {
        value(val) {
            if(val && this.machine) {
                this.loadCustomers();
                setTimeout(() => {
                    this.$refs.keyword.$el.querySelector('input').select();
                }, 500);
                //this.requestPing();
            } else {
                this.customers = [];
                if(this.ping.xhttp) {
                    this.ping.xhttp.abort();
                }
            }
        }
    },
    computed: {
        allowRework() {
            return this.$store.getters.isReworkAllowed;
        }
    }
}
</script>
