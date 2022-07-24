<template>
    <v-container>
        <h3 class="title white--text">Discounts</h3>

        <v-divider class="my-3"></v-divider>

        <v-btn round class="ml-0 primary" @click="addDiscount">
            <v-icon left>add</v-icon>
            add discount
        </v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.percentage }} %</td>
                    <td>
                        <v-btn small icon @click="edit(props.item)" outline>
                            <v-icon small>edit</v-icon>
                        </v-btn>
                        <v-btn small icon @click="deleteDiscount(props.item)" outline :loading="props.item.isDeleting">
                            <v-icon small>delete</v-icon>
                        </v-btn>
                    </td>
                </template>
            </v-data-table>
        </v-card>
        <discount-dialog :discount="activeDiscount" v-model="openDiscountDialog" @save="updateDiscounts" />
    </v-container>
</template>

<script>
import DiscountDialog from './DiscountDialog.vue';

export default {
    components: {
        DiscountDialog
    },
    data() {
        return {
            items: [],
            loading: false,
            activeDiscount: null,
            openDiscountDialog: false,
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Percentage',
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
            axios.get('/api/discounts').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        edit(discount) {
            this.activeDiscount = discount;
            this.openDiscountDialog = true;
        },
        addDiscount() {
            this.activeDiscount = null;
            this.openDiscountDialog = true;
        },
        updateDiscounts(data) {
            if(data.mode == 'insert') {
                this.items.push(data.discount);
                this.activeDiscount = data.discount;
            } else {
                this.activeDiscount.name = data.discount.name;
                this.activeDiscount.percentage = data.discount.percentage;
            }
        },
        deleteDiscount(discount) {
            if(confirm('Delete this discount?')) {
                Vue.set(discount, 'isDeleting', true);
                this.$store.dispatch('discount/deleteDiscount', discount.id).then((res, rej) => {
                    this.items = this.items.filter(d => d.id != discount.id);
                }).finally(() => {
                    Vue.set(discount, 'isDeleting', false);
                });
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
