<template>
    <div>
        <div v-if="!!currentCustomer">
            <v-card>
                <v-card-actions>
                    <h4 class="title grey--text">CUSTOMER: </h4>
                    <h1 class="title">{{currentCustomer.name}}</h1>
                    <v-spacer></v-spacer>
                    <v-btn icon small @click="removeCustomer">
                        <v-icon small>close</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </div>
        <div v-else>
            <v-text-field dense :loading="loading" @keyup="filter" v-model="keyword" label="Search customer" append-icon="search" outline></v-text-field>
            <v-btn v-if="items.length == 0" class="primary ma-0" round @click="openCustomerDialog = true">Create new customer</v-btn>
        </div>
        <v-card v-if="items.length">
            <v-list dense>
                <v-list-tile v-for="item in items" :key="item.id" @click="selectCustomer(item)">
                    {{item.name}}
                </v-list-tile>
            </v-list>
        </v-card>
        <customer-dialog v-model="openCustomerDialog" @save="setCustomer" />
    </div>
</template>

<script>
import CustomerDialog from '../customers/CustomerDialog.vue';

export default {
    components: {
        CustomerDialog
    },
    data() {
        return {
            openCustomerDialog: false,
            loading: false,
            cancelSource: null,
            keyword: null,
            items: [],
            customerName: null
        }
    },
    methods: {
        filter() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            if(!this.keyword) {
                this.items = [];
                return;
            }

            this.loading = true;
            axios.get('/api/autocomplete/customers', {
                params: {
                    keyword: this.keyword
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.items = res.data.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        selectCustomer(customer) {
            this.$emit('selectCustomer', customer);
            this.items = [];
        },
        cancelSearch(){
            if(this.cancelSource){
                this.cancelSource.cancel();
            }
        },
        removeCustomer() {
            this.$store.commit('postransaction/removeCustomer');
        },
        createCustomer() {
            this.removeCustomer();
            this.openCustomerDialog = true;
        },
        setCustomer(data) {
            this.selectCustomer(data.customer);
        }
    },
    computed: {
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        }
    }
}
</script>
