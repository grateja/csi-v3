<template>
    <v-container>
        <h3 class="title grey--text">Products</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 primary" @click="addProduct" round>
            <v-icon left>add</v-icon> add product
        </v-btn>
        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <tr>
                    <td>
                        <v-responsive :aspect-ratio="16/9" v-if="props.item.img_path" max-height="100px">
                            <v-img :src="props.item.img_path"></v-img>
                        </v-responsive>
                    </td>
                    <td>{{props.item.name}}</td>
                    <td>{{props.item.description}}</td>
                    <td v-if="!props.item.selling_price">
                        FREE
                    </td>
                    <td v-else>P {{ parseFloat(props.item.selling_price).toFixed(2) }}</td>
                    <td>{{props.item.current_stock}}</td>
                    <td>
                        <v-btn small @click="edit(props.item)" class="mx-0" v-if="isOwner" round>
                            <v-icon left small>edit</v-icon> edit
                        </v-btn>
                        <v-btn small @click="deleteProduct(props.item)" class="mx-0" :loading="props.item.isDeleting" round>
                            <v-icon left small>delete</v-icon> delete
                        </v-btn>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <product-dialog :product="activeProduct" @save="save" v-model="openProductDialog" @setPicture="setPicture" />
    </v-container>
</template>

<script>
import ProductDialog from './ProductDialog.vue';

export default {
    components: {
        ProductDialog
    },
    data() {
        return {
            loading: false,
            openProductDialog: false,
            activeProduct: null,
            items: [],
            headers: [
                {
                    text: 'Image',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Description',
                    sortable: false
                },
                {
                    text: 'Selling price',
                    sortable: false
                },
                {
                    text: 'Stock',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    },
    methods: {
        addProduct() {
            this.activeProduct = null;
            this.openProductDialog = true;
        },
        edit(item) {
            this.activeProduct = item;
            this.openProductDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.product)
                this.activeProduct = data.product;
            } else {
                this.activeProduct.name = data.product.name;
                this.activeProduct.description = data.product.description;
                this.activeProduct.current_stock = data.product.current_stock;
                this.activeProduct.selling_price = data.product.selling_price;
            }
        },
        load() {
            this.loading = true;
            axios.get('/api/products').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        setPicture(imgPath) {
            this.activeProduct.img_path = imgPath;
        },
        deleteProduct(item) {
            if(confirm('Do you really want to delete this product?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('product/deleteProduct', item.id).then((res, rej) => {
                    this.items = this.items.filter(p => p.id != res.data.product.id);
                }).finally(() => {
                    Vue.set(item, 'isDeleting', false);
                })
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
