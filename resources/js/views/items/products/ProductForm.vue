<template>
    <form @submit.prevent="submit">
        <v-card>
            <v-card-text class="title grey--text">Product details</v-card-text>

            <v-progress-linear indeterminate v-if="retreiving" height="2"></v-progress-linear>
            <v-divider v-else></v-divider>

            <v-card-text>


                <v-layout row wrap>
                    <!-- <v-flex xs3 v-if="mode == 'insert'">
                        <h4 class="subtitle grey--text">Branches</h4>
                        <v-list dense v-if="branches">
                            <v-list-tile v-for="branch in branches" :key="branch.id">
                                <v-list-tile-action>
                                    <v-checkbox v-model="selectedBranches" :value="branch.id"></v-checkbox>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        {{branch.name}}
                                    </v-list-tile-title>
                                    <div class="caption grey--text">
                                        {{branch.address}}
                                    </div>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                        <v-progress-circular v-else indeterminate></v-progress-circular>
                    </v-flex> -->
                    <v-flex>
                        <v-text-field v-model="formData.name" label="Name" :error-messages="errors.get('name')" @input="errors.clear('name')"></v-text-field>
                        <v-text-field v-model="formData.description" label="Description" :error-messages="errors.get('description')" @input="errors.clear('description')"></v-text-field>
                        <v-text-field v-model="formData.barcode" label="Barcode" :error-messages="errors.get('barcode')" @input="errors.clear('barcode')"></v-text-field>

                        <template v-if="mode == 'insert'">
                            <v-text-field v-model="formData.unitPrice" label="Unit price" :error-messages="errors.get('unitPrice')" @input="errors.clear('unitPrice')"></v-text-field>
                            <v-text-field type="number" v-model="formData.minimumStock" label="Minimum stock" :error-messages="errors.get('minimumStock')" @input="errors.clear('minimumStock')"></v-text-field>
                            <v-text-field type="number" v-model="formData.initialStock" label="Initial stock" :error-messages="errors.get('initialStock')" @input="errors.clear('initialStock')"></v-text-field>
                        </template>
                    </v-flex>
                </v-layout>


            </v-card-text>

            <v-card-actions>
                <v-btn class="primary" type="submit" :loading="loading">Save</v-btn>
                <v-btn class="default" type="button" @click="cancel">Cancel</v-btn>
            </v-card-actions>

        </v-card>
    </form>
</template>

<script>
import BranchBrowser from '../../shared/BranchBrowser.vue';

export default {
    components: {
        BranchBrowser
    },
    data() {
        return {
            formData: {
                name: '',
                description: '',
                barcode: '',
                minimumStock: 10,
                initialStock: 0,
                unitPrice: 0
            },
            mode: 'insert',
            activeBranch: null,
            retreiving: false,
            productId: null,
            branches: null,
            selectedBranches: []
        }
    },
    methods: {
        submit() {
            if(this.loading) return;

            this.formData.branchesIds = this.selectedBranches;

            this.$emit('submit', {
                productId: this.productId,
                formData: this.formData,
                mode: this.mode
            });
        },
        cancel() {
            this.$emit('cancel');
        },
        selectBranch(branch) {
            this.activeBranch = branch;
        },
        loadBranches() {
            if(!this.branches && !this.loadingBranches) {
                this.$store.dispatch('branch/loadBranches').then((res, rej) => {
                    this.branches = res.data.branches;
                    if(this.currentUser) {
                        this.selectedBranches = [this.currentUser.active_branch_id]; //res.data.branches.map(b => b.id);
                    }
                });
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['product/getErrors'];
        },
        loading() {
            return this.$store.getters['product/isSaving'];
        },
        loadingBranches() {
            return this.$store.getters['branch/isLoading'];
        },
        currentUser() {
            return this.$store.getters.getCurrentUser;
        }
    },
    created() {
        this.productId = this.$route.params.id;
        if(this.productId) {
            this.retreiving = true;
            axios.get(`/api/products/${this.productId}`).then((res, rej) => {
                this.retreiving = false;
                this.formData.name = res.data.product.name;
                this.formData.description = res.data.product.description;
                this.formData.barcode = res.data.product.barcode;
                this.formData.minimuStock = res.data.product.minimum_stock;
                this.formData.initialStock = res.data.product.initial_stock;
                this.mode = 'update';
            }).catch(err => {
                this.retreiving = false;
                this.mode = 'insert';
            });
        } else {
            this.loadBranches();
        }
    }
}
</script>
