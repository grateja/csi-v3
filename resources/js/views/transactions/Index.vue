<template>
    <v-container>
        <h3 class="title panel-title">New Job Order</h3>
        <v-progress-linear v-if="loading" height="3" indeterminate />
        <v-divider class="my-3"></v-divider>
        <v-layout>
            <v-flex xs7 lg8>
                <v-btn to="/new-transaction/services" class="ml-0 translucent" active-class="primary" round>Services</v-btn>
                <v-btn to="/new-transaction/products" class="translucent" active-class="primary" round>Products</v-btn>
                <v-btn to="/new-transaction/scarpa-cleanings" class="translucent" active-class="primary" round>Scarpa</v-btn>
                <v-btn to="/new-transaction/lagoon" class="translucent" active-class="primary" round>Lagoon</v-btn>
                <v-btn to="/scanner" class="translucent" active-class="primary" round>Scan QR Code</v-btn>
                <router-view></router-view>
            </v-flex>
            <v-flex xs5 lg4>
                <customer-panel @selectCustomer="selectCustomer" />
                <transaction-items v-if="customer" />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import CustomerPanel from './CustomerPanel.vue';
import TransactionItems from './TransactionItems.vue';
export default {
    components: {
        CustomerPanel,
        TransactionItems
    },
    methods: {
        selectCustomer(customer) {
            if(!!customer) {
                this.$store.dispatch('postransaction/checkCustomer', customer);
            }
        }
    },
    computed: {
        loading() {
            return this.$store.getters['postransaction/isLoading'];
        },
        customer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        }
    },
    beforeDestroy() {
        this.$store.commit('postransaction/clearTransaction');
        this.$store.commit('postransaction/removeCustomer');
    }
}
</script>
