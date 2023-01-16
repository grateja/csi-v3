<template>
    <div>
        <v-divider class="my-3"></v-divider>
        <v-layout row wrap>
            <v-flex sm6 lg4 x3 v-for="service in services" :key="service.id">
                <v-card class="ma-1">
                    <v-card-text>
                        <h3 class="subtitle text-no-wrap text-truncate">{{service.service.name}}</h3>
                        <v-divider></v-divider>
                        <span class="caption">{{service.description}}</span>
                        <v-btn block @click="selectService('full', service)" outline color="grey" flat>Full service (P{{service.full_service_price}})</v-btn>
                        <v-btn block @click="selectService('self', service)" outline color="grey" flat>Self service (P{{service.self_service_price}})</v-btn>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <quantity-prompt v-model="openQuantityPrompt" @ok="ok" :loading="addingItem"></quantity-prompt>
    </div>
</template>

<script>
import QuantityPrompt from './QuantityPrompt.vue';

export default {
    components: {
        QuantityPrompt
    },
    data() {
        return {
            openQuantityPrompt: false,
            selectedService: null,
            serviceGroup: null // full | self
        }
    },
    computed: {
        services() {
            return this.$store.getters['service/getServices'];
        },
        addingItem() {
            return this.$store.getters['transaction/addingItem'];
        },
        customer() {
            return this.$store.getters['customer/getCustomer'];
        },
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        }
    },
    methods: {
        selectService(serviceGroup, service) {
            if(this.customer == null) {
                // alert('Select customer first.');
                this.$store.commit('setFlash', {
                    message: 'Select customer first',
                    color: 'warning'
                });
                return;
            }

            this.serviceGroup = serviceGroup;
            this.selectedService = service;
            this.openQuantityPrompt = true;
        },
        ok(quantity) {
            this.$store.dispatch('transaction/addService', {
                transactionId: this.transaction ? this.transaction.id : null,
                formData: {
                    customerId: this.customer.id,
                    serviceId: this.selectedService.id,
                    quantity,
                    serviceGroup: this.serviceGroup
                }
            }).then((res, rej) => {
                this.openQuantityPrompt = false;
                // this.$store.commit('service/deductProduct', {
                //     productId: this.selectedProduct.id,
                //     quantity
                // });
            });
        }
    }
}
</script>
