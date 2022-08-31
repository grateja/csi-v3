<template>
    <v-container>
        <h3 class="title white--text">Lagoon partners</h3>

        <v-divider class="my-3"></v-divider>

        <v-btn round class="ml-0 primary" @click="addLagoonPartner">
            <v-icon left>add</v-icon>
            add Lagoon Partner
        </v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <td>{{ props.item.id }}</td>
                    <td>{{ props.item.shop_name }}</td>
                    <td>{{ props.item.address }}</td>
                    <td>{{ props.item.customers_count }}</td>
                    <td>{{ props.item.transactions_count }}</td>
                    <td>
                        <v-btn small icon @click="edit(props.item)" outline>
                            <v-icon small>edit</v-icon>
                        </v-btn>
                        <v-btn small icon @click="deleteLagoonPartner(props.item)" outline :loading="props.item.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                    </td>
                </template>
            </v-data-table>
        </v-card>
        <lagoon-partner-dialog :lagoonPartner="activeLagoonPartner" v-model="openLagoonPartnerDialog" @save="updateLagoonPartner" />
    </v-container>
</template>

<script>
import LagoonPartnerDialog from './LagoonPartnerDialog.vue';

export default {
    components: {
        LagoonPartnerDialog
    },
    data() {
        return {
            items: [],
            loading: false,
            activeLagoonPartner: null,
            openLagoonPartnerDialog: false,
            headers: [
                {
                    text: 'ID',
                    sortable: false
                },
                {
                    text: 'Shop name',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: 'Customers',
                    sortable: false
                },
                {
                    text: 'Job Orders',
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
            axios.get('/api/lagoon-partners').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        edit(lagoonPartner) {
            this.activeLagoonPartner = lagoonPartner;
            this.openLagoonPartnerDialog = true;
        },
        addLagoonPartner() {
            this.activeLagoonPartner = null;
            this.openLagoonPartnerDialog = true;
        },
        updateLagoonPartner(data) {
            if(data.mode == 'insert') {
                this.items.push(data.lagoonPartner);
                this.activeLagoonPartner = data.lagoonPartner;
            } else {
                this.activeLagoonPartner.id = data.lagoonPartner.id;
                this.activeLagoonPartner.shop_name = data.lagoonPartner.shop_name;
                this.activeLagoonPartner.address = data.lagoonPartner.address;
            }
        },
        deleteLagoonPartner(lagoonPartner) {
            if(confirm('Delete this Lagoon partner?')) {
                Vue.set(lagoonPartner, 'isDeleting', true);
                this.$store.dispatch('lagoonpartner/deleteLagoonPartner', lagoonPartner.id).then((res, rej) => {
                    this.items = this.items.filter(d => d.id != lagoonPartner.id);
                }).finally(() => {
                    Vue.set(lagoonPartner, 'isDeleting', false);
                });
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
