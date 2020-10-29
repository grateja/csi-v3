<template>
    <v-container>
        <h3 class="title white--text">Pending services</h3>
        <v-divider class="my-3"></v-divider>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{ props.item.customer_name }}</td>
                        <td>{{ props.item.customer_washes_count }}</td>
                        <td>{{ props.item.customer_dries_count }}</td>
                        <td>
                            <v-btn round small class="font-weight-bold" :color="props.item.date_paid == null ? `primary` : 'green'" @click="viewAll(props.item)">
                                {{ props.item.job_order }}
                            </v-btn>
                        </td>
                        <td>{{ moment(props.item.date).format('LLL') }}</td>
                        <!-- <td>
                            <v-btn small @click="viewAll(props.item)" round outline>View all</v-btn>
                        </td> -->
                    </tr>
                </template>
                <template slot="footer">
                    <tr>
                        <td colspan="5">
                            <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{total}}</span> result(s)</div>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
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
            total: 0,
            page: 1,
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
                    text: 'Job Order',
                    sortable: false
                },
                {
                    text: 'Date',
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
                    page: this.page
                }
            }).then((res, rej) => {
                if(this.reset) {
                    this.reset = false;
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
                this.total = res.data.result.total;
                console.log(res.data.result.total);
            }).finally(() => {
                this.loading = false;
            });
        },
        loadMore() {
            this.page += 1;
            this.load();
        },
        viewAll(transaction) {
            this.activeCustomerId = transaction.customer_id;
            this.openServiceItemsDialog = true;
        }
    },
    created() {
        this.load();
    }
}
</script>
