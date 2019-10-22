<template>
    <div>
        <h4 class="title grey--text">Service list</h4>
        <v-divider class="my-3"></v-divider>

        <v-btn class="ml-0 white--text green" to="/items/services/add"><v-icon left>add</v-icon>Add service</v-btn>

        <v-card>
            <v-card-text>
                <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{props.item.service.name}}</td>
                        <td>{{props.item.service.description}}</td>
                        <td>{{props.item.service.barcode}}</td>
                        <td>P {{ props.item.full_service_price }}
                            <v-tooltip top v-if="isOwner">
                                <v-btn small icon slot="activator" @click="editPrice({type: 'full', service: props.item})">
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit full service price<br>(Applies to this specific branch only)</span>
                            </v-tooltip>
                        </td>
                        <td>P {{ props.item.self_service_price }}
                            <v-tooltip top v-if="isOwner">
                                <v-btn small icon slot="activator" @click="editPrice({type: 'self', service: props.item})">
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit self service price<br>(Applies to this specific branch only)</span>
                            </v-tooltip>
                        </td>
                        <td>
                            <span v-if="props.item.add_super_wash">
                                {{ props.item.minutes_per_pulse /*+ props.item.add_super_wash */ }} Minutes
                            </span>
                            <span v-else>
                                {{ props.item.pulse_count * props.item.minutes_per_pulse }} Minutes
                            </span>

                            <v-tooltip top v-if="isOwner">
                                <v-btn small icon slot="activator" @click="editBranchservice(props.item)">
                                    <v-icon small>access_time</v-icon>
                                </v-btn>
                                <span>Edit minutes<br>(Applies to this specific branch only)</span>
                            </v-tooltip>
                        </td>
                        <!-- <td>
                            {{props.item.add_super_wash}} Minutes
                        </td> -->
                        <td>
                            <v-tooltip top>
                                <v-btn small slot="activator" :to="`/items/services/${props.item.service_id}`" class="mx-0">
                                    <v-icon small>public</v-icon>
                                    <v-icon small>edit</v-icon> edit
                                </v-btn>
                                <span>Edit global info <br>Service name,<br>description, <br>barcode...<br>(Applies accross all branches)</span>
                            </v-tooltip>
                        </td>
                    </template>
                </v-data-table>

                <v-divider class="my-2"></v-divider>

                <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination>
            </v-card-text>
        </v-card>
        <edit-price-dialog v-model="openEditPrice" :service="activeService" :serviceType="activeServiceType" @ok="updatePrice"></edit-price-dialog>
        <edit-branch-service v-model="openBranchServiceDialog" :service="activeBranchService" @ok="updateBranchService"></edit-branch-service>
    </div>
</template>
<script>
import EditPriceDialog from './EditPriceDialog.vue';
import EditBranchService from './EditBranchService.vue';
export default {
    components: {
        EditPriceDialog,
        EditBranchService
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            loading: false,
            totalPage: 0,
            activeService: null,
            activeServiceType: null,
            activeBranchService: null,
            openEditPrice: false,
            openBranchServiceDialog: false,
            items: [],
            headers: [
                {
                    text: 'Service name',
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
                    text: 'Full service price',
                    sortable: false
                },
                {
                    text: 'Self service price',
                    sortable: false
                },
                {
                    text: 'Minutes',
                    sortable: false
                },
                // {
                //     text: 'Add super wash',
                //     sortable: false
                // },
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

            axios.get('/api/search/services/self', {
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
        editPrice(data) {
            this.activeService = data.service;
            this.activeServiceType = data.type;
            this.openEditPrice = true;
        },
        updatePrice(service) {
            this.activeService.full_service_price = service.full_service_price;
            this.activeService.self_service_price = service.self_service_price;
        },
        editBranchservice(service) {
            this.activeBranchService = service;
            this.openBranchServiceDialog = true;
            console.log(service);
        },
        updateBranchService(data) {
            this.load();
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
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
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
