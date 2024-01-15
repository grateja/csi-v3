<template>
    <v-dialog :value="value" max-width="420" persistent>
        <v-card>
            <v-card-title class="title grey--text">Select service</v-card-title>
            <v-progress-linear v-if="loading" indeterminate height="1"></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-card-text v-if="!loading && services.length == 0">
                No services
            </v-card-text>
            <v-card-text v-else>
                <dl v-if="machine && customer">
                    <dt class="font-weight-bold grey--text">Customer name:</dt>
                    <dd class="ml-2">{{customer.name}}</dd>

                    <dt class="font-weight-bold grey--text">Machine name:</dt>
                    <dd class="ml-2">
                        <span class="red--text" v-if="errors.has('message')">{{errors.get('message')}}</span>
                        <span class="green--text" v-else-if="activating">Connecting to {{machine.machine_name}}...
                            <!-- <v-progress-circular indeterminate height="20px" /> -->
                        </span>
                        <span v-else>
                            {{machine.machine_name}}
                        </span>
                    </dd>
                </dl>
                <v-list>
                    <v-list-tile v-for="service in services" :key="service.id" @click="selectService(service)">
                        <v-list-tile-content>
                            <v-list-tile-title>{{service.service_name}}</v-list-tile-title>
                            <div>
                                <span class="grey--text">{{service.total_available}} available</span>
                                <span class="grey--text">({{service.minutes}} Min)</span>
                            </div>
                        </v-list-tile-content>
                        <v-list-tile-action><v-btn round :disabled="activating" :loading="service.activating">{{errors.get('message') ? 'Retry' : 'Activate'}}</v-btn></v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round :disabled="activating">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'customer', 'machine', 'additional'
    ],
    data() {
        return {
            services: [],
            loading: false
        }
    },
    methods: {
        loadServices() {
            this.loading = true;
            axios.get(`/api/pending-services/${this.serviceType}-services`, {
                params: {
                    customerId: this.customer.id,
                    machineSize: this.machineSize,
                    model: this.machine.model
                }
            }).then((res, rej) => {
                this.services = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        selectService(service) {
            if(this.activating) {
                return;
            }
            Vue.set(service, 'activating', true);
            this.$store.dispatch('remote/activateMachine', {
                formData: {
                    customerId: this.customer.id,
                    machineId: this.machine.id,
                    machineSize: this.machineSize,
                    serviceType: this.serviceType,
                    serviceName: service.service_name,
                    additional: this.additional
                }
            }).then((res, rej) => {
                this.$emit('activated', res.data);
                this.close();
            }).finally(() => {
                Vue.set(service, 'activating', false);
            });
        },
        close() {
            this.$emit('input', false);
        }
    },
    computed: {
        machineSize() {
            if(!!this.machine) {
                return this.machine.machine_type[0] == 'r' ? 'REGULAR' : 'TITAN';
            }
            return null;
        },
        serviceType() {
            if(!!this.machine) {
                console.log(this.machine);
                return this.machine.machine_type[1] == 'w' ? 'washing' : 'drying';
            }
            return null;
        },
        errors() {
            return this.$store.getters['remote/getErrors'];
        },
        activating() {
            return !!this.services.find(s => s.activating);
        }
    },
    watch: {
        value(val) {
            if(val && this.customer) {
                this.loadServices();
            } else {
                this.services = [];
                this.$store.commit('remote/clearErrors');
            }
        }
    }
}
</script>
