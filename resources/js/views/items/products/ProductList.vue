<template>
    <div>
        <h4 class="title grey--text">Products list</h4>
        <v-divider class="my-3"></v-divider>

        <v-btn class="ml-0 green white--text" to="/items/product/add"><v-icon left>add</v-icon>Add product</v-btn>

        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <tr :class="{'red--text' : props.item.available == 0, 'orange--text' : props.item.minimumStock > props.item.available && props.item.available > 0}">
                            <td>{{props.item.name}}</td>
                            <td>{{props.item.description}}</td>
                            <td>{{props.item.barcode}}</td>
                            <td>{{ props.item.price }}
                                <v-tooltip top v-if="isOwner">
                                    <v-btn small icon slot="activator" @click="editPrice(props.item)">
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <span>Edit price<br>(Applies to this specific branch only)</span>
                                </v-tooltip>
                            </td>
                            <td>{{props.item.available}}
                                <!-- <v-tooltip top v-if="isOwner">
                                    <v-btn small icon slot="activator" class="mx-0">
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <span>Edit initial stock<br>(Applies to this specific branch only)</span>
                                </v-tooltip> -->
                                <v-tooltip top>
                                    <v-btn small icon slot="activator" class="mx-0" @click="addProduct(props.item)">
                                        <v-icon small>add</v-icon>
                                    </v-btn>
                                    <span>Add item stock/Purchase<br>(Applies to this specific branch only)</span>
                                </v-tooltip>
                            </td>
                            <td>{{props.item.minimumStock}}</td>
                            <td>
                                <v-tooltip top>
                                    <v-btn small slot="activator" :to="`/items/product/${props.item.productId}`" class="mx-0">
                                        <v-icon small>public</v-icon>
                                        <v-icon small>edit</v-icon> edit
                                    </v-btn>
                                    <span>Edit global info <br>Product name,<br>description, <br>barcode...<br>(Applies accross all branches)</span>
                                </v-tooltip>
                            </td>
                        </tr>
                    </template>
                </v-data-table>

                <v-divider class="my-2"></v-divider>

                <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
            </v-card-text>
        </v-card>

        <add-product-stock v-model="openAddProductStock" :product="activeProduct" @ok="addStockContinue"></add-product-stock>
        <edit-price-dialog v-model="openEditPrice" :product="activeProduct" @ok="updatePrice"></edit-price-dialog>
    </div>
</template>
<script>
import EditPriceDialog from './EditPriceDialog.vue';
import BranchBrowser from '../../shared/BranchBrowser.vue';
import AddProductStock from './AddProductStock.vue';
export default {
    components: {
        BranchBrowser,
        AddProductStock,
        EditPriceDialog
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeUser: null,
            openAddProductStock: false,
            openEditPrice: false,
            activeProduct: null,
            headers: [
                {
                    text: 'Product name',
                    sortable: false
                },
                {
                    text: 'Description',
                    sortable: false
                },
                {
                    text: 'Barcode',
                    sortable: false
                },
                {
                    text: 'Unit price',
                    sortable: false
                },
                {
                    text: 'Available',
                    sortable: false
                },
                {
                    text: 'Minimum stock',
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
            if(this.loading || !this.activeBranch) return;

            this.$router.push({
                query: {
                    keyword: this.keyword,
                    page: this.page,
                    branchId: this.activeBranch ? this.activeBranch.id : null
                }
            });

            this.loading = true;

            axios.get('/api/search/products/self', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    branchId: this.activeBranch ? this.activeBranch.id : null
                }
            }).then((res, rej) => {
                console.log(res.data.result);
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
        addProduct(product) {
            this.activeProduct = product;
            this.openAddProductStock = true;
        },
        addStockContinue(data) {
            this.activeProduct.available = data.branchProduct.available;
        },
        editPrice(product) {
            this.activeProduct = product;
            this.openEditPrice = true;
        },
        updatePrice(product) {
            this.activeProduct.price = product.price;
        }
    },
    created() {
        this.load();
    },
    computed: {
        activeBranch() {
            return this.$store.getters.getActiveBranch;
        },
        isOwner() {
            let currentUser = this.$store.getters.getCurrentUser;
            if(currentUser) {
                return currentUser.roles.some(r => r == 'admin');
            }
        }
    },
    watch: {
        activeBranch(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
