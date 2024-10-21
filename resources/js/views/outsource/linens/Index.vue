<template>
    <v-container>
        <h3 class="title white--text">Linens</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 primary" @click="addLinen" round v-if="isOwner">
            <v-icon left>add</v-icon> add linen
        </v-btn>

        <v-layout row wrap>
            <v-flex  xs6 sm4 lg3 xl2 v-for="linen in items" :key="linen.id">
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 rounded-card translucent" :elevation="hover ? 12 : 2">
                        <v-card-text>
                            <div class="text-xs-center ma-1">
                                <div>
                                    {{linen.name}}
                                </div>
                                <div class="font-italic grey--text">{{linen.dry_weight}}kg</div>
                            </div>
                            <v-progress-linear v-if="linen.isAdding" indeterminate height="4" class="my-0"></v-progress-linear>
                            <v-divider v-else class="my-2"></v-divider>
                            <div class="text-xs-center title ma-2">
                                P {{ parseFloat(linen.regular_price).toFixed(2)}}
                            </div>
                            <v-divider></v-divider>
                            <div>
                                <v-chip color="info">P {{ parseFloat(linen.with_stain_light).toFixed(2)}}</v-chip>
                                <v-chip color="warning">P {{ parseFloat(linen.with_stain_medium).toFixed(2)}}</v-chip>
                                <v-chip color="red">P {{ parseFloat(linen.with_stain_heavy).toFixed(2)}}</v-chip>
                            </div>
                        </v-card-text>
                        <v-card-actions v-if="isOwner">
                            <v-spacer></v-spacer>
                            <v-btn icon @click="edit(linen)">
                                <v-icon>edit</v-icon>
                            </v-btn>
                            <v-btn icon @click="deleteLinen(linen)">
                                <v-icon>delete</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>

        <!-- <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.item.category}}</td>
                        <td>{{props.item.name}}</td>

                        <td v-if="!props.item.regular_price">
                            FREE
                        </td>
                        <td v-else>P {{ parseFloat(props.item.regular_price).toFixed(2) }}</td>

                        <td v-if="!props.item.with_stain_light">
                            FREE
                        </td>
                        <td v-else>P {{ parseFloat(props.item.with_stain_light).toFixed(2) }}</td>

                        <td v-if="!props.item.with_stain_medium">
                            FREE
                        </td>
                        <td v-else>P {{ parseFloat(props.item.with_stain_medium).toFixed(2) }}</td>

                        <td v-if="!props.item.with_stain_heavy">
                            FREE
                        </td>
                        <td v-else>P {{ parseFloat(props.item.with_stain_heavy).toFixed(2) }}</td>

                        <td>{{props.item.dry_weight}} kg</td>
                        <td>
                            <template v-if="isOwner">
                                <v-btn small @click="edit(props.item)" class="mx-0" round outline>
                                    <v-icon left small>edit</v-icon> edit
                                </v-btn>
                                <v-btn small @click="deleteLinen(props.item)" class="mx-0" outline :loading="props.item.isDeleting" round>
                                    <v-icon left small>delete</v-icon> delete
                                </v-btn>
                            </template>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card> -->
        <linen-dialog :linen="activeLinen" @save="save" v-model="openLinenDialog" :outSourceId="outSourceId" />
    </v-container>
</template>

<script>
import LinenDialog from './LinenDialog.vue';

export default {
    components: {
        LinenDialog
    },
    data() {
        return {
            loading: false,
            openLinenDialog: false,
            activeLinen: null,
            items: [],
            headers: [
                {
                    text: 'Category',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Regular price',
                    sortable: false
                },
                {
                    text: 'Light',
                    sortable: false
                },
                {
                    text: 'Medium',
                    sortable: false
                },
                {
                    text: 'Heavy',
                    sortable: false
                },
                {
                    text: 'Dry weight',
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
        },
        outSourceId() {
            return this.$route.params.outSourceId;
        }
    },
    methods: {
        addLinen() {
            this.activeLinen = null;
            this.openLinenDialog = true;
        },
        edit(item) {
            this.activeLinen = item;
            this.openLinenDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.linen)
                this.activeLinen = data.linen;
            } else {
                this.activeLinen.category = data.linen.category;
                this.activeLinen.name = data.linen.name;
                this.activeLinen.unit_price = data.linen.unit_price;
                this.activeLinen.dry_weight = data.linen.dry_weight;
            }
        },
        load() {
            this.loading = true;
            axios.get(`/api/out-source/linens/${this.outSourceId}`).then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        deleteLinen(item) {
            if(confirm('Do you really want to delete this linen?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('outsourcelinen/deleteLinen', item.id).then((res, rej) => {
                    this.items = this.items.filter(p => p.id != res.data.linen.id);
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
