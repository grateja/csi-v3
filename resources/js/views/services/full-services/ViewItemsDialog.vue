<template>
    <v-dialog :value="value" max-width="720" persistent>
        <v-card v-if="!!service">
            <v-card-title class="title grey--text">{{service.name}}</v-card-title>

            <v-divider></v-divider>

            <v-tabs>
                <v-tab>Services</v-tab>
                <v-tab>Products</v-tab>
                <v-tab-item>
                    <v-card-text>

                        <v-btn class="primary ml-0" round @click="addService">Add Service</v-btn>

                        <v-data-table :headers="headers" :items="service.full_service_items" :loading="loading" hide-actions>
                            <template v-slot:items="props">
                                <tr>
                                    <td>{{props.item.name}}</td>
                                    <td>{{props.item.quantity}}</td>
                                    <td v-if="!props.item.price">
                                        FREE
                                    </td>
                                    <td v-else>P {{ parseFloat(props.item.price).toFixed(2) }}</td>
                                    <td>{{props.item.points}}</td>
                                    <td>
                                        <template v-if="isOwner">
                                            <v-btn small @click="edit(props.item)" class="mx-0" round>
                                                <v-icon left small>edit</v-icon> edit
                                            </v-btn>
                                            <v-btn small @click="deleteItem(props.item)" class="mx-0" round :loading="props.item.isDeleting">
                                                <v-icon left small>delete</v-icon> delete
                                            </v-btn>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>

                    </v-card-text>
                </v-tab-item>
                <v-tab-item>

                </v-tab-item>
            </v-tabs>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn round @click="close">close</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    <service-item-dialog v-model="openServiceItemDialog" :serviceItem="activeServiceItem" :fullServiceId="service ? service.id : null" @save="updateItems"/>
    </v-dialog>
</template>

<script>
import ServiceItemDialog from './ServiceItemDialog.vue';

export default {
    components: {
        ServiceItemDialog
    },
    props: [
        'service', 'value'
    ],
    data() {
        return {
            headers: [
                {
                    text: 'Name',
                    sortable:false
                },
                {
                    text: 'Quantity',
                    sortable:false
                },
                {
                    text: 'Price',
                    sortable:false
                },
                {
                    text: 'Points',
                    sortable:false
                },
                {
                    text: '',
                    sortable:false
                }
            ],
            loading:false,
            openServiceItemDialog: false,
            activeServiceItem: null
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        edit(item) {
            this.activeServiceItem = item;
            this.openServiceItemDialog = true;
        },
        addService() {
            this.activeServiceItem = null;
            this.openServiceItemDialog = true;
        },
        updatePrice() {
                this.service.price = this.service.full_service_items.reduce((sum, item) => sum + item.price, 0) + this.service.additional_charge - this.service.discount;
        },
        updateItems(data) {
            if(data.mode == 'insert') {
                this.service.full_service_items.push(data.serviceItem);
                this.activeServiceItem = data.serviceItem;
            } else {

            }
            if(this.service && this.service.full_service_items) {
                this.updatePrice();
            }
        },
        deleteItem(item) {
            if(confirm('Delete this item?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('fullserviceitem/deleteService', item.id).then((res, rej) => {
                    this.service.full_service_items = this.service.full_service_items.filter(i => i.id != item.id);
                    this.updatePrice();
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                })
            }
        }
    }
}
</script>
