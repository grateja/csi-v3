<template>
    <div>
        <h4 class="title grey--text">Discounts</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn class="green ml-0 white--text" @click="addDiscount">
            <v-icon small left>add</v-icon>
            Create new discount
        </v-btn>
        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>


                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{ props.item.name }}</td>
                        <td>{{ props.item.discount_type == 'c' ? 'Cash' : 'Percentage' }}</td>
                        <td>{{ props.item.percentage }} %</td>
                        <td>
                            <v-tooltip top>
                                <v-btn slot="activator" small icon @click="editDiscount(props.item)">
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit details</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <v-btn slot="activator" small icon @click="deleteDiscount(props.item)" :loading="props.item.isDeleting">
                                    <v-icon small>delete</v-icon>
                                </v-btn>
                                <span>Delete item</span>
                            </v-tooltip>
                        </td>
                    </template>
                </v-data-table>

                <v-divider class="my-2"></v-divider>

                <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
            </v-card-text>
        </v-card>
        <discount-dialog v-model="openDiscountDialog" :discount="activeDiscount" @save="editContinue"></discount-dialog>
    </div>
</template>

<script>
import DiscountDialog from './DiscountDialog.vue';

export default {
    components: {
        DiscountDialog
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: parseInt(this.$route.query.page) || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeDiscount: null,
            openDiscountDialog: false,
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Discount type',
                    sortable: false
                },
                {
                    text: 'Percentage',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.load();
        },
        load() {
            if(this.loading) return;

            this.$router.push({
                query: {
                    keyword: this.keyword,
                    page: this.page
                }
            });

            this.loading = true;

            axios.get('/api/search/discounts', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        editDiscount(discount) {
            this.activeDiscount = discount;
            this.openDiscountDialog = true;
        },
        editContinue(data) {
            if(data.mode == 'insert') {
                this.items.push(data.discount);
            } else {
                this.activeDiscount.name = data.discount.name;
                this.activeDiscount.discountType = data.discount.discount_type;
                this.activeDiscount.percentage = data.discount.percentage;
            }
        },
        addDiscount() {
            this.activeDiscount = null;
            this.openDiscountDialog = true;
        },
        deleteDiscount(discount) {
            if(confirm('Are you sure you want to delete this discount?')) {
                Vue.set(discount, 'isDeleting', true);
                this.$store.dispatch('discount/deleteDiscount', {
                    discountId: discount.id
                }).then((res, rej) => {
                    this.items = this.items.filter(i => i.id != res.data.id);
                }).finally(() => {
                    Vue.set(discount, 'isDeleting', false);
                })
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
