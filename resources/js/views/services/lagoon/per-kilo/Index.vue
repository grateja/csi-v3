<template>
    <div>
        <v-btn class="ml-0 primary" @click="addService" round v-if="isOwner">
            <v-icon left>add</v-icon> add service
        </v-btn>
        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>
                            <v-responsive :aspect-ratio="16/9" v-if="props.item.img_path" max-height="100px">
                                <v-img :src="props.item.img_path"></v-img>
                            </v-responsive>
                        </td>
                        <td>{{props.item.name}}</td>
                        <td v-if="!props.item.price_per_kilo">
                            FREE
                        </td>
                        <td v-else>P {{ parseFloat(props.item.price_per_kilo).toFixed(2) }}</td>
                        <td>
                            <template v-if="isOwner">
                                <v-btn small @click="edit(props.item)" class="mx-0" round outline>
                                    <v-icon left small>edit</v-icon> edit
                                </v-btn>
                                <v-btn small @click="deleteService(props.item)" class="mx-0" outline :loading="props.item.isDeleting" round>
                                    <v-icon left small>delete</v-icon> delete
                                </v-btn>
                            </template>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <service-dialog v-model="openServiceDialog" :service="activeService" @save="save" @setPicture="setPicture" />
    </div>
</template>
<script>
import ServiceDialog from './ServiceDialog.vue';

export default {
    components: {
        ServiceDialog
    },
    data() {
        return {
            loading: false,
            activeService: null,
            openServiceDialog: false,
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
                    text: 'Price /Kilo',
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
        addService() {
            this.activeService = null;
            this.openServiceDialog = true;
        },
        edit(item) {
            this.activeService = item;
            this.openServiceDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                this.items.push(data.service)
                this.activeService = data.service;
            } else {
                this.activeService.name = data.service.name;
                this.activeService.price_per_kilo = data.service.price_per_kilo;
            }
        },
        load() {
            this.loading = true;
            axios.get('/api/services/lagoon-per-kilo').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        setPicture(imgPath) {
            this.activeService.img_path = imgPath;
        },
        deleteService(item) {
            if(confirm('Do you really want to delete this service?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('lagoonperkilo/deleteService', item.id).then((res, rej) => {
                    this.items = this.items.filter(p => p.id != res.data.service.id);
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
