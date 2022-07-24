<template>
    <div>

        <v-list dense>
            <h4 class="subheader grey--text my-2 mx-3">Products</h4>
            <v-divider></v-divider>
            <v-list-tile v-for="product in products" :key="product.id" :color="product.saved ? 'green' : 'grey'">
                <v-list-tile-content>
                    <div>
                        {{product.quantity}}
                        {{product.name}}
                    </div>
                </v-list-tile-content>
                <v-list-tile-action>{{product.price}}</v-list-tile-action>
                <v-list-tile-action v-if="!product.saved">
                    <!-- <v-tooltip v-if="product.saved" top>
                        <v-btn slot="activator" small icon @click="voidProductItem(product)">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                        <span>Void</span>
                    </v-tooltip> -->
                    <v-tooltip top>
                        <v-btn slot="activator" small icon @click="removeProductItem(product)" :loading="product.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                        <span>Delete</span>
                    </v-tooltip>
                </v-list-tile-action>
            </v-list-tile>
            <v-divider></v-divider>
            <v-list-tile>
                <v-list-tile-content class="font-weight-bold">
                    {{productSummary.totalItems}} Total item(s)
                </v-list-tile-content>
                <v-list-tile-action class="font-weight-bold">P{{productSummary.totalPrice}}</v-list-tile-action>
            </v-list-tile>

            <v-divider class="mb-4"></v-divider>

            <h4 class="subheader grey--text my-2 mx-3">Services</h4>
            <v-divider></v-divider>
            <v-list-tile v-for="service in services" :key="service.id" :color="service.saved ? 'green' : 'grey'">
                <v-list-tile-content>
                    <div>
                        {{service.quantity}}
                        {{service.name}} ({{service.serviceGroup}} service)
                    </div>
                </v-list-tile-content>
                <v-list-tile-action>P{{service.price}}</v-list-tile-action>
                <v-list-tile-action v-if="!service.saved">
                    <!-- <v-tooltip v-if="service.saved" top>
                        <v-btn slot="activator" small icon @click="voidServiceItem(service)">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                        <span>Void</span>
                    </v-tooltip> -->
                    <v-tooltip top>
                        <v-btn slot="activator" small icon @click="removeServiceItem(service)" :loading="service.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                        <span>Delete</span>
                    </v-tooltip>
                </v-list-tile-action>
            </v-list-tile>
            <v-divider></v-divider>
            <v-list-tile>
                <v-list-tile-content class="font-weight-bold">
                    {{serviceSummary.totalItems}} Total item(s)
                </v-list-tile-content>
                <v-list-tile-action class="font-weight-bold">P{{serviceSummary.totalPrice}}</v-list-tile-action>
            </v-list-tile>

            <v-divider></v-divider>

            <v-list-tile>
                <v-list-tile-content class="font-weight-bold orange--text">
                    GRAND TOTAL
                </v-list-tile-content>
                <v-list-tile-action class="font-weight-bold orange--text">P{{serviceSummary.totalPrice + productSummary.totalPrice}}</v-list-tile-action>
            </v-list-tile>
        </v-list>
        <!-- <void-service-dialog v-model="openVoidService" :serviceTransactionId="serviceTransactionId"></void-service-dialog> -->
    </div>
</template>

<script>
// import VoidServiceDialog from './VoidServiceDialog.vue';

export default {
    // components: {
    //     VoidServiceDialog
    // },
    props: [
        'customerId'
    ],
    data() {
        return {
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Q',
                    sortable: false
                },
                {
                    text: 'Price',
                    sortable: false
                }
            ],
            openVoidService: false,
            serviceTransactionId: 0
        }
    },
    methods: {
        reset() {
            this.$store.commit('transaction/clearTransaction', null);
            this.$store.commit('transaction/clearProducts', null);
            this.$store.commit('transaction/clearServices', null);
        },
        removeServiceItem(item) {
            Vue.set(item, 'isDeleting', true);
            this.$store.dispatch('transaction/removeServiceItem', {
                serviceItemId: item.serviceTransactionId
            });
        },
        removeProductItem(item) {
            Vue.set(item, 'isDeleting', true);
            this.$store.dispatch('transaction/removeProductItem', {
                productItemId: item.productTransactionId
            });
        },
        voidServiceItem(serviceItem) {
            this.serviceTransactionId = serviceItem.serviceTransactionId;
            this.openVoidService = true;
            console.log('serviceItem', serviceItem);
        },
        voidProductItem(productItem) {
            console.log('productItem', productItem);
        }
    },
    computed: {
        products() {
            return this.$store.getters['transaction/getProducts'];
        },
        services() {
            return this.$store.getters['transaction/getServices'];
        },
        productSummary() {
            return this.$store.getters['transaction/getProductSummary'];
        },
        serviceSummary() {
            return this.$store.getters['transaction/getServiceSummary'];
        },
        transaction() {
            return this.$store.getters['transaction/getCurrentTransaction'];
        },
        loading() {
            return this.$store.getters['transaction/isLoading'];
        }
    },
    beforeDestroy() {
        this.reset();
    },
    beforeMount() {
        this.reset();
    }
}
</script>
