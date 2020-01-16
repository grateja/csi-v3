<template>
    <v-container>
        <h3 class="title grey--text">Pending services</h3>
        <v-divider class="my-3"></v-divider>

        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.customer_washes_count }}</td>
                <td>{{ props.item.customer_dries_count }}</td>
                <td>
                    <v-btn small @click="viewAll(props.item)">View all</v-btn>
                </td>
            </template>
        </v-data-table>
        <service-items-dialog v-model="openServiceItemsDialog" :customerId="activeCustomerId" />
    </v-container>
</template>

<script>
import ServiceItemsDialog from './ServiceItemsDialog.vue';

export default {
    components: {
        ServiceItemsDialog
    },
    data() {
        return {
            openServiceItemsDialog: false,
            activeCustomerId: null,
            loading: false,
            items: [],
            headers: [
                {
                    text: 'Customer Name',
                    sortable: false
                },
                {
                    text: 'Available wash',
                    sortable: false
                },
                {
                    text: 'Available dry',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/pending-services', {
                params: {

                }
            }).then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        viewAll(customer) {
            this.activeCustomerId = customer.id;
            this.openServiceItemsDialog = true;
        }
    },
    created() {
        this.load();
    }
}
</script>
