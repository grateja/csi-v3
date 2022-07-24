<template>
    <div>
        <v-btn class="ml-0 primary" @click="addService(null)" round v-if="isOwner">
            <v-icon left>add</v-icon> add service
        </v-btn>
        <!-- <pre>{{groups}}</pre> -->
        <v-card class="rounded-card translucent-table" v-if="!loading" :key="indexKey">
            <div v-for="(items, i) in groups" :key="i" >
                <v-card-title>
                    <v-btn icon @click="addService(i)">
                        <v-icon>add</v-icon>
                    </v-btn>
                    <span class="title white--text">{{i}}</span>
                </v-card-title>
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent" :key="i">
                    <template v-slot:items="props">
                        <tr>
                            <td>
                                <v-responsive :aspect-ratio="16/9" v-if="props.item.img_path" max-height="100px">
                                    <v-img :src="props.item.img_path"></v-img>
                                </v-responsive>
                            </td>
                            <td>{{props.item.name}}</td>
                            <!-- <td>{{props.item.description}}</td> -->
                            <td v-if="!props.item.price">
                                FREE
                            </td>
                            <td v-else>P {{ parseFloat(props.item.price).toFixed(2) }}</td>
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
            </div>
        </v-card>
        <service-dialog v-model="openServiceDialog" :service="activeService" @save="save" @setPicture="setPicture" :category="activeCategory" />
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
            indexKey: 1,
            loading: false,
            activeService: null,
            activeCategory: null,
            openServiceDialog: false,
            groups: {},
            headers: [
                {
                    text: 'Image',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                // {
                //     text: 'Description',
                //     sortable: false
                // },
                {
                    text: 'Price',
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
        addService(category) {
            this.activeService = null;
            this.activeCategory = category;
            this.openServiceDialog = true;
        },
        edit(item) {
            this.activeCategory = item.category
            this.activeService = item;
            this.openServiceDialog = true;
        },
        save(data) {
            if(data.mode == 'insert') {
                // this.groups.forEach((e, i) => {
                //     console.log(e)
                //     console.log(i)
                // })
                // let col = this.groups.filter(e => e.category == this.category)
                // console.log(col)
                // console.log(this.groups[data.service.category])
                let group = this.groups[data.service.category];
                if(group) {
                    // console.log('found group', group)
                    // Vue.set(this.groups, 0, data.service)
                    this.indexKey++;
                    group.push(data.service)
                } else {
                    // Object.assign(this.groups, [data.service])
                    this.groups[data.service.category] = [data.service]
                }
                this.activeService = data.service;
            } else {
                this.activeService.name = data.service.name;
                this.activeService.description = data.service.description;
                this.activeService.price = data.service.price;
            }
        },
        load() {
            this.loading = true;
            axios.get('/api/services/lagoon').then((res, rej) => {
                Object.assign(this.groups, res.data.result);
                // this.groups = res.data.result;
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
                this.$store.dispatch('lagoon/deleteService', item.id).then((res, rej) => {
                    this.groups[item.category] = this.groups[item.category].filter(p => p.id != res.data.service.id)
                    // this.items = this.items.filter(p => p.id != res.data.service.id);
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
