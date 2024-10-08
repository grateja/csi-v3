<template>
    <v-dialog :value="value" max-width="600" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">RFID Card</v-card-title>
                <v-divider></v-divider>
                <v-layout>
                    <v-flex xs6 v-if="mode == 'insert'">
                        <v-card-text v-if="!!currentOwner">
                            <div class="caption grey--text">
                                RFID card will be enrolled to:
                            </div>
                            <v-chip close @input="currentOwner = null">{{currentOwner.name}}</v-chip>
                        </v-card-text>
                        <v-card-text v-else>
                            <v-text-field :error-messages="errors.get('ownerId')" :label="`Search ${formData.cardType == 'c' ? 'customer' : 'user'}`"  outline @keyup="search" v-model="keyword" ref="keyword"></v-text-field>

                            <v-btn v-if="formData.cardType == 'c' && currentOwner == null" class="mx-0 success" round @click="openCustomerDialog = true">Create new customer</v-btn>
                            <v-btn v-if="formData.cardType == 'c' && currentOwner == null" class="mx-0 white" round @click="openSearchFromCloud = true">Pre-registered customers</v-btn>

                            <v-card v-if="results.length">
                                <v-list dense>
                                    <v-list-tile v-for="item in results" :key="item.id" @click="selectOwner(item)">
                                        <v-list-tile-content>
                                            {{item.name}}
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </v-list>
                            </v-card>
                        </v-card-text>
                    </v-flex>
                    <v-flex grow>
                        <v-card-text>
                            <v-radio-group v-model="formData.cardType" v-if="isOwner && mode == 'insert'">
                                <v-radio value="u" label="Master card"></v-radio>
                                <v-radio value="c" label="Customer card"></v-radio>
                            </v-radio-group>
                            <v-text-field label="RFID" v-model="formData.rfid" :error-messages="errors.get('rfid')" outline ref="rfid" @keydown.native="clear('rfid')"></v-text-field>
                            <v-text-field label="Free load" v-model="formData.freeLoad" :error-messages="errors.get('freeLoad')" outline ref="freeLoad" @keydown.native="clear('freeLoad')" type="number"></v-text-field>
                        </v-card-text>
                    </v-flex>
                </v-layout>
                <v-card-actions>
                    <v-btn round class="primary" type="submit" :loading="saving">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                    <v-spacer></v-spacer>
                    <!-- <v-btn round @click="openUnregisteredCardDialog = true">
                        browse unregistered card
                    </v-btn> -->
                    <v-btn round @click="openUnregisteredCardDialog = true">
                        <v-icon>tap_and_play</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <!-- <unregistered-card-dialog v-model="openUnregisteredCardDialog" @select="selectCard" /> -->
        <rfid-terminal v-model="openUnregisteredCardDialog" @select="selectCard" />
        <customer-dialog v-model="openCustomerDialog" :initialName="keyword" @save="insertCustomer" :customer="activeCustomer" :preRegister="true"></customer-dialog>
        <customer-browser-dialog v-model="openSearchFromCloud" @selectCustomer="selectPreRegistered" />
    </v-dialog>
</template>

<script>
// import UnregisteredCardDialog from './UnregisteredCardDialog.vue';
import CustomerDialog from '../customers/CustomerDialog.vue';
import CustomerBrowserDialog from '../customers/CustomerBrowserDialog.vue';
import RfidTerminal from '../layout/RfidTerminal.vue';

export default {
    components: {
        // UnregisteredCardDialog,
        RfidTerminal,
        CustomerDialog,
        CustomerBrowserDialog
    },
    props: [
        'value', 'rfidCard',
    ],
    data() {
        return {
            formData: {
                rfid: null,
                cardType: 'c',
                ownerId: null,
                freeLoad: 0
            },
            mode: 'insert',
            results: [],
            loading: false,
            cancelSource: null,
            openUnregisteredCardDialog: false,
            openCustomerDialog: false,
            openSearchFromCloud: false,
            keyword: null,
            currentOwner: null,
            activeCustomer: null
        }
    },
    methods: {
        submit() {
            this.$store.dispatch(`rfidcard/${this.mode}Card`, {
                rfidCardId: this.rfidCard ? this.rfidCard.rfid_card_id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    rfidCard: res.data.rfidCard
                });
                this.close();
            })
        },
        close() {
            this.$emit('input', false);
            this.clear();
        },
        search() {
            this.clear();
            this.results = [];
            if(this.keyword.length == 0) {
                this.cancelSearch();
                return;
            }
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            let url = this.formData.cardType == 'c' ? 'customers' : 'users';
            this.loading = true;
            axios.get('/api/autocomplete/' + url, {
                params: {
                    keyword: this.keyword
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.results = res.data.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        selectCard(rfid) {
            this.formData.rfid = rfid;
        },
        selectOwner(item) {
            this.formData.ownerId = item.id;
            this.currentOwner = item;
        },
        clear(key) {
            this.$store.commit('rfidcard/clearErrors', key);
        },
        insertCustomer(data) {
            this.selectOwner(data.customer);
        },
        selectPreRegistered(customer) {
            this.activeCustomer = customer;
            this.openCustomerDialog = true;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['rfidcard/getErrors'];
        },
        saving() {
            return this.$store.getters['rfidcard/isSaving'];
        },
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    },
    watch: {
        value(val) {
            if(val && this.rfidCard) {
                this.mode = 'update';
                this.formData.rfid = this.rfidCard.rfid;
                this.formData.cardType = this.rfidCard.card_type;
                setTimeout(() => {
                    this.$refs.rfid.$el.querySelector('input').select();
                }, 500);
            } else {
                this.mode = 'insert';
                this.formData.rfid = null;
                this.formData.cardType = 'c';
                this.currentOwner = null;
                setTimeout(() => {
                    this.$refs.keyword.$el.querySelector('input').select();
                }, 500);
            }

        },
        'formData.cardType': function(val) {
            this.results = [];
        }
    }
}
</script>
