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
                            <v-text-field :error-messages="errors.get('ownerId')" :label="`Search ${formData.cardType == 'c' ? 'customer' : 'user'}`"  outline @keyup="search" v-model="keyword"></v-text-field>
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
                            <v-text-field label="RFID" v-model="formData.rfid" :error-messages="errors.get('rfid')" outline></v-text-field>
                        </v-card-text>
                    </v-flex>
                </v-layout>
                <v-card-actions>
                    <v-btn round class="primary" type="submit" :loading="saving">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn round @click="openUnregisteredCardDialog = true">
                        browse unregistered card
                    </v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <unregistered-card-dialog v-model="openUnregisteredCardDialog" @select="selectCard" />
    </v-dialog>
</template>

<script>
import UnregisteredCardDialog from './UnregisteredCardDialog.vue';

export default {
    components: {
        UnregisteredCardDialog
    },
    props: [
        'value', 'rfidCard',
    ],
    data() {
        return {
            formData: {
                rfid: null,
                cardType: 'c',
                ownerId: null
            },
            mode: 'insert',
            results: [],
            loading: false,
            cancelSource: null,
            openUnregisteredCardDialog: false,
            keyword: null,
            currentOwner: null
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
        },
        search() {
            this.$store.commit('rfidcard/clearErrors');
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
            this.formData.rfid = rfid.rfid;
        },
        selectOwner(item) {
            this.formData.ownerId = item.id;
            this.currentOwner = item;
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
            } else {
                this.mode = 'insert';
                this.formData.rfid = null;
                this.formData.cardType = 'c';
                this.currentOwner = null;
            }
        },
        'formData.cardType': function(val) {
            this.results = [];
        }
    }
}
</script>
