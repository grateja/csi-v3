<template>
    <v-container>
        <h3 class="title white--text">Out-source services</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 primary" @click="addService" round v-if="isOwner">
            <v-icon left>add</v-icon> add service
        </v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.item.name}}</td>
                        <td>{{props.item.pulse_count}}</td>
                        <td>{{props.item.description}}</td>
                        <td>{{props.item.minutes}}</td>
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
        <service-dialog :service="activeService" @save="save" v-model="openServiceDialog" />
    </v-container>
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
            openServiceDialog: false,
            activeService: null,
            items: [],
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Pulse count',
                    sortable: false
                },
                {
                    text: 'Description',
                    sortable: false
                },
                {
                    text: 'Minutes',
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
                this.activeService.name = data.outSource.name;
                this.activeService.pulse_count = data.outSource.pulse_count;
                this.activeService.description = data.outSource.description;
                this.activeService.minutes = data.outSource.minutes;
            }
        },
        load() {
            this.loading = true;
            axios.get('/api/out-source/services').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        deleteService(item) {
            if(confirm('Do you really want to delete this service?')) {
                Vue.set(item, 'isDeleting', true);
                this.$store.dispatch('outsourceservice/deleteService', item.id).then((res, rej) => {
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
