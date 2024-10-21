<template>
    <v-dialog :value="value" persistent max-width="620px">
        <v-card v-if="machine">
            <v-card-title class="grey--text"> <span class="title"> Activate {{machine.machine_name}} for OSL</span></v-card-title>
            <v-card-text v-if="!activeOsl">
                <v-list>
                    <v-list-tile v-for="item in accounts" :key="item.id" @click="selectAccount(item)">
                        <v-list-tile-content>
                            <v-list-tile-title>{{item.company_name}}</v-list-tile-title>
                            <v-list-tile-sub-title>{{ item.address }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <v-card-text v-if="activeOsl">
                <v-btn class="primary" round @click="activeOsl = null">{{ activeOsl.company_name }}</v-btn>
                <v-card  style="border: 2px solid rgb(231, 231, 231);" class="rounded-card" elevation="0">
                    <v-list v-if="selectedLinens.length > 0">
                        <v-list-tile v-for="(item, index) in selectedLinens" :key="index">
                            <v-list-tile-action>
                                <v-btn icon @click="removeItem(item)">
                                    <v-icon>close</v-icon>
                                </v-btn>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>{{item.name}}</v-list-tile-title>
                                <v-list-tile-sub-title>{{ item.quantity }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                    <v-card-text v-else>
                        baduy
                        <p>Select linen</p>
                    </v-card-text>
                    <v-card-text>
                        <form @submit.prevent="addItem">
                            <v-layout>
                                <v-flex>
                                    <v-select outline @input="errors.clear('linens')" v-model="linenItem.name" :items="availableLinens.map (i => i.name)" label="Item name" :error-messages="errors.get('linens')"></v-select>
                                </v-flex>
                                <v-flex>
                                    <v-text-field outline label="Quantity" v-model="linenItem.quantity" type="number"></v-text-field>
                                </v-flex>
                                <v-btn type="submit">Add</v-btn>
                            </v-layout>
                        </form>
                    </v-card-text>
                    </v-card>
                <v-layout>
                    <v-flex xs4 v-for="service in availableServices" :key="service.id">
                        <v-card @click="selectService(service)" class="pointer" :class="{primary: !!activeService && service.id == activeService.id}">
                            <v-card-title><span  :class="{'white--text': !!activeService && service.id == activeService.id}">{{ service.name }}</span></v-card-title>
                            <v-card-text><span  :class="{'white--text': !!activeService && service.id == activeService.id}">{{ service.description }} <br/> {{ service.minutes }} minutes.</span></v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
                <v-spacer></v-spacer>
                <v-btn v-if="!!activeOsl && !!activeService" @click="activate" :loading="activating" class="primary" round>Activate</v-btn>
                <p v-else>No selected account and/or service</p>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'machine',
        'value'
    ],
    data() {
        return {
            activating: false,
            loading: false,
            accounts: [],
            activeOsl: null,
            linenItem: {
                quantity: 0,
                name: null
            },
            availableServices: [],
            activeService: null,
            availableLinens: [],
            selectedLinens: []
        }
    },
    methods: {
        getServices() {
            axios.get('/api/out-source/services').then((res, rej) => {
                this.availableServices = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        selectService(service) {
            this.activeService = service
        },
        getAccounts() {
            this.loading = true
            axios.get('/api/out-source').then((res, rej) => {
                this.accounts = res.data.result;
            }).finally(() => {
                this.loading = false;
            })
        },
        selectAccount(account) {
            this.activeOsl = account
            this.getAvailableLinens(account.id);
        },
        removeItem(item) {
            this.selectedLinens = this.selectedLinens.filter (i => i.name != item.name)
        },
        addItem() {
            if(this.linenItem.name == null || this.linenItem.name == '' || this.linenItem.quantity == 0) return
            const exists = this.selectedLinens.find(a => a.name == this.linenItem.name)
            if(exists) {
                exists.quantity = parseInt(exists.quantity) + parseInt(this.linenItem.quantity)
            } else {
                this.selectedLinens.push({
                    name: this.linenItem.name,
                    quantity: parseInt(this.linenItem.quantity)
                });
            }
            this.linenItem.name = null
            this.linenItem.quantity = 0
        },
        getAvailableLinens(outSourceId) {
            axios.get(`/api/out-source/linens/${outSourceId}`).then((res, rej) => {
                this.availableLinens = res.data.result
            }).finally(() => {
                this.loading = false;
            })
        },
        activate() {
            this.activating = true
            this.$store.dispatch('remote/activateOsl', {
                formData: {
                    machine_id: this.machine.id,
                    out_source_id: this.activeOsl.id,
                    linens: this.selectedLinens,
                    serviceType: this.serviceType,
                    serviceId: this.serviceId
                }
            }).then(res => {
                this.close();
                this.$emit('activated', res.data);
            }).catch(err => {

            }).finally(() => {
                this.activating = false;
            });
        },
        close() {
            this.$emit('input', false);
        }
    },
    mounted() {
        this.getAccounts();
        this.getServices();
    },
    watch: {
        value(val) {
            if(!val) {
                this.loading = false
                this.activeOsl = null
                this.linenItem.quantity = 0
                this.linenItem.name = null
                this.selectedLinens = []
                this.availableLinens = []
                this.activeService = null;
            }
        }
    },
    computed: {
        serviceType() {
            if(!!this.machine) {
                if(this.machine.model != null) {
                    return 'elux';
                }
                return this.machine.machine_type[1] == 'w' ? 'washing' : 'drying';
            }
            return null;
        },
        serviceId() {
            if(!!this.activeService) {
                const svc = this.availableServices.find(s => s.name == this.activeService.name)
                return svc ? svc.id : null
            }
        },
        errors() {
            return this.$store.getters['remote/getErrors']
        }
    }
}
</script>
