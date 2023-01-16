<template>
    <div>
        <h4 class="title grey--text">Products list</h4>
        <v-divider class="my-3"></v-divider>

        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{props.item.name}}</td>
                        <td>{{props.item.description}}</td>
                        <td>{{props.item.barcode}}</td>
                        <td>{{ props.item.price }}</td>
                        <td>{{props.item.available}}</td>
                        <td>
                            <!-- <v-tooltip top>
                                <v-btn small slot="activator" :to="`/items/product/${props.item.id}`" class="mx-0">
                                    <v-icon small>public</v-icon>
                                    <v-icon small>edit</v-icon> edit
                                </v-btn>
                                <span>Edit global info <br>Product name,<br>description, <br>barcode...<br>(Applies accross all branches)</span>
                            </v-tooltip> -->
                        </td>
                    </template>
                </v-data-table>

                <v-divider class="my-2"></v-divider>

                <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
export default {
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeUser: null,
            openAddProductStock: false,
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
