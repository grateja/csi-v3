<template>
    <div>
        <h3 class="title grey--text">RFID Service prices</h3>
        <v-divider class="my-3"></v-divider>
        <v-card>
            <v-card-text>
                <v-data-table hide-actions :items="items" :headers="headers">
                    <template v-slot:items="props">
                        <td>{{props.item.machine_type.name}}</td>
                        <td>{{props.item.name}}</td>
                        <td>{{props.item.price}}</td>
                        <td>{{props.item.minutes}}</td>
                        <td>{{props.item.enabled ? 'Enabled' : 'Disabled'}}</td>
                        <td>
                            <v-btn icon small @click="edit(props.item)">
                                <v-icon small>settings</v-icon>
                            </v-btn>
                        </td>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
        <service-price-dialog v-model="openEditPrice" :rfidServicePrice="activeItem" @save="update"></service-price-dialog>
    </div>
</template>

<script>
import ServicePriceDialog from './EditServicePrice.vue';

export default {
    components: {
        ServicePriceDialog
    },
    data() {
        return {
            openEditPrice: false,
            activeItem: null,
            items: [],
            headers: [
                {
                    sortable: false,
                    text: 'Machine type'
                },
                {
                    sortable: false,
                    text: 'Name'
                },
                {
                    sortable: false,
                    text: 'Price'
                },
                {
                    sortable: false,
                    text: 'Minutes'
                },
                {
                    sortable: false,
                    text: 'Status'
                },
                {
                    sortable: false,
                    text: 'Actions'
                }
            ]
        }
    },
    methods: {
        load() {
            axios.get('/api/all/rfid/service-prices/self').then((res, rej) => {
                console.log(res.data);
                this.items = res.data.servicePrices;
            });
        },
        edit(item) {
            this.activeItem = item;
            this.openEditPrice = true;
        },
        update(item) {
            this.activeItem.name = item.name;
            this.activeItem.minutes = item.minutes;
            this.activeItem.price = item.price;
            this.activeItem.enabled = item.enabled;
        }
    },
    created() {
        this.load();
    }
}
</script>
