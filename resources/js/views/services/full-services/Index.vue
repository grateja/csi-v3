<template>
    <div>
        <v-btn class="ml-0 primary" @click="addService" round  v-if="isOwner">
            <v-icon left>add</v-icon> add service
        </v-btn>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg3 v-for="service in items" :key="service.id">
                <v-card class="ma-2">
                    <v-card-title class="title grey--">{{service.name}}</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <ol>
                            <li v-for="item in service.full_service_items" :key="item.id">{{item.name}} - <span class="font-weight-bold">P {{ parseFloat(item.price).toFixed(2)}}</span> </li>
                            <li v-for="item in service.full_service_products" :key="item.id">{{item.name}} - <span class="font-weight-bold">P {{parseFloat(item.price).toFixed(2)}}</span></li>
                            <li class="font-italic grey--text" v-if="service.additional_charge > 0">Additional charge: P {{ parseFloat(service.additional_charge).toFixed(2)}}</li>
                            <li class="font-italic grey--text" v-if="service.discount > 0">Discount: P {{ parseFloat(service.discount).toFixed(2)}}</li>
                        </ol>
                        <h3 class="font-weight-bold">Total price: P {{ parseFloat(service.price).toFixed(2)}}</h3>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions v-if="isOwner">
                        <v-btn small color="purple" dark round @click="viewItems(service)">view items</v-btn>
                        <v-spacer></v-spacer>
                        <v-btn small @click="edit(service)" icon><v-icon small>edit</v-icon></v-btn>
                        <v-btn small @click="deleteService(service)" icon :loading="service.isDeleting"><v-icon small>delete</v-icon></v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
        <service-dialog v-model="openServiceDialog" :service="activeService" @save="save" @setPicture="setPicture" />
        <view-items-dialog v-model="openViewItemsDialog" :service="activeService" />
    </div>
</template>
<script>
import ServiceDialog from './ServiceDialog.vue';
import ViewItemsDialog from './ViewItemsDialog.vue';

export default {
    components: {
        ServiceDialog,
        ViewItemsDialog
    },
    data() {
        return {
            loading: false,
            activeService: null,
            openServiceDialog: false,
            openViewItemsDialog: false,
            items: []
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    },
    methods: {
        addService() {
            this.activeService = null;
            this.openServiceDialog = true;
        },
        edit(item) {
            this.activeService = item;
            this.openServiceDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.service)
                this.activeService = data.service;
            } else {
                this.activeService.name = data.service.name;
                this.activeService.discount = data.service.discount;
                this.activeService.additional_charge = data.service.additional_charge;
                this.activeService.price = data.service.price;
            }
        },
        load() {
            this.loading = true;
            axios.get('/api/services/full-services').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        setPicture(imgPath) {
            this.activeService.img_path = imgPath;
        },
        deleteService(item) {
            if(confirm('Do you really want to delete this service?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('fullservice/deleteService', item.id).then((res, rej) => {
                    this.items = this.items.filter(p => p.id != res.data.service.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                })
            }
        },
        viewItems(service) {
            this.activeService = service;
            this.openViewItemsDialog = true;
        }
    },
    created() {
        this.load();
    }
}
</script>
