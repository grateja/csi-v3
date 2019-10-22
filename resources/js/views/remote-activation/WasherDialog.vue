<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <!-- <v-card v-if="!!washer">
            <v-card-title class="grey--text title">{{washer.name}}</v-card-title>
            <v-divider></v-divider>

            <v-card-text>
                <span class="red--text" v-if="errors.has('machineId')">{{errors.get('machineId')}}</span>
                <vuetify-autocomplete v-model="customerName" url="/api/autocomplete/customers/self" label="Search customer name" @select="selectCustomer" :error-messages="errors.get('customerId')"></vuetify-autocomplete>
            </v-card-text>

            <v-card-text v-if="loading">
                Connecting to {{washer.name}} ...
            </v-card-text>

            <v-card-text v-if="retreivingToken">
                <p>Retrieving available services. Please wait...
                    <v-progress-linear indeterminate></v-progress-linear>
                </p>
            </v-card-text>
            <v-card-text v-if="!!availedServices && !loading">
                <h4 class="title grey--text">Purchased services</h4>
                <v-divider></v-divider>
                <v-list>
                    <v-list-tile v-for="availedService in availedServices" :key="availedService.id">
                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{availedService.service.name}}
                            </v-list-tile-title>
                            <div class="caption grey--text">{{availedService.available}} available</div>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-btn small flat outline @click="use(availedService)">use</v-btn>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="close">Close</v-btn>
            </v-card-actions>
        </v-card> -->
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'washer'
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
                machineId: this.washer.id,
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
                machineId: this.washer.id,
                formData: {
                    customerId: this.activeCustomer.id,
                    serviceId: service.id
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
                        machineTypeId: this.washer.machine_type_id
                    }
                }).then((res, rej) => {
                    this.availedServices = res.data.customer.completed_services;
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
