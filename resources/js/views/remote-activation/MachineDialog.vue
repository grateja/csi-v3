<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card v-if="!!machine">
            <v-card-title class="grey--text title">{{machine.name}}</v-card-title>
            <v-divider></v-divider>

            <v-card-text>
                <span class="red--text" v-if="errors.has('machineId')">{{errors.get('machineId')}}</span>
                <vuetify-autocomplete v-model="customerName" url="/api/autocomplete/customers/self" label="Search customer name" @select="selectCustomer" :error-messages="errors.get('customerId')"></vuetify-autocomplete>
            </v-card-text>

            <v-card-text v-if="loading">
                Connecting to {{machine.name}} ...
            </v-card-text>

            <v-card-text v-if="retreivingToken">
                <p>Retrieving available services. Please wait...
                    <v-progress-linear indeterminate></v-progress-linear>
                </p>
                <!-- <p v-else-if="!customerToken && !!activeCustomer">
                    No purchased service
                </p>
                <template v-if="!!customerToken && !!activeCustomer">
                    <v-text-field type="number" label="Minutes" :value="totalMinutes" :error-messages="errors.get('minutes')" :hint="minuteHint" persistent-hint readonly></v-text-field>
                    <v-layout row wrap v-if="customerToken">
                        <v-flex grow>
                        </v-flex>
                        <v-flex shrink>
                            <v-btn class="ma-0" @click="formData.pulse += 1" :disabled="formData.pulse >= customerToken.pulse_count">
                                <v-icon left>add</v-icon>
                                {{customerToken.minutes_per_pulse}} mins
                            </v-btn>
                        </v-flex>
                        <v-flex shrink>
                            <v-btn class="ma-0" @click="formData.pulse -= 1" :disabled="formData.pulse <= 0">
                                <v-icon left>remove</v-icon>
                                {{customerToken.minutes_per_pulse}} mins
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </template> -->
            </v-card-text>
            <v-card-text v-if="!!availedServices && !loading && availedServices.length">
                <h4 class="title grey--text">Purchased services</h4>
                <v-divider></v-divider>
                <v-list>
                    <v-list-tile v-for="availedService in availedServices" :key="availedService.id">
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{availedService.service.name}}
                            </v-list-tile-title>
                            <div class="caption grey--text">{{availedService.totalAvailable}} available</div>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn small flat outline @click="use(availedService)">use</v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <template v-else-if="activeCustomer != null && !retreivingToken && !loading">
                <v-card-text>No service purchased.</v-card-text>
            </template>
            <!-- <v-card-text>
                <pre>{{customerToken}}</pre>
            </v-card-text> -->
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <!-- <v-btn @click="ok" class="primary" :loading="loading" :disabled="!!availedServices">Ok</v-btn> -->
                <v-btn @click="close" class="primary">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'machine'
    ],
    data() {
        return {
            formData: {
                customerId: null,
                pulse: 0
            },
            keyword: null,
            customers: [],
            activeCustomer: null,
            availedServices: null,
            retreivingToken: false,
            customerName: ''
        }
    },
    methods: {
        ok() {
            this.$store.dispatch('remote/activate', {
                machineId: this.machine.id,
                formData: this.formData
            }).then((res, rej) => {
                console.log(res);
                this.$emit('input', false);
                this.$emit('ok', res.data);
                this.close();
            });
        },
        use(service) {
            console.log('service', service);
            this.$store.dispatch('remote/activate', {
                machineId: this.machine.id,
                formData: {
                    customerId: this.activeCustomer.id,
                    serviceId: service.service_id
                }
            }).then((res, rej) => {
                console.log(res);
                // this.$emit('input', false);
                this.$emit('ok', res.data);
                this.close();
            });        },
        getCustomer() {
            if(!!this.activeCustomer) {
                this.retreivingToken = true;
                axios.get(`/api/customers/${this.activeCustomer.id}/with-services`, {
                    params: {
                        machineTypeId: this.machine.machine_type_id
                    }
                }).then((res, rej) => {
                    this.availedServices = res.data.completedServices;
                    // this.availedServices = res.data.customer.completed_services;
                    // if(res.data.customer.tokens.length) {
                    //     this.customerToken = res.data.customer.tokens[0]
                    // } else {
                    //     this.customerToken = null;
                    // }
                    this.retreivingToken = false;
                });
            }
        },
        close() {
            this.activeCustomer = null;
            this.formData.pulse = 0;
            this.$emit('input', false);
            this.$store.commit('remote/clearErrors');
            this.customerName = null;
            this.availedServices = null;
        },
        selectCustomer(customer) {
            this.customerToken = null;
            this.activeCustomer = customer;
            this.formData.customerId = customer.id;
            this.getCustomer();
        },
        removeCustomer() {
            this.activeCustomer = null;
        }
    },
    computed: {
        totalMinutes() {
            if(!!this.customerToken) {
                return this.formData.pulse * this.customerToken.minutes_per_pulse;
            }
        },
        loading() {
            return this.$store.getters['remote/isLoading'];
        },
        errors() {
            return this.$store.getters['remote/getErrors'];
        },
        minuteHint() {
            if(!!this.customerToken) {
                return (this.customerToken.minutes_per_pulse * this.customerToken.pulse_count) + ' minutes available';
            }
        },
        cannotAdd() {

        }
    }
}
</script>
