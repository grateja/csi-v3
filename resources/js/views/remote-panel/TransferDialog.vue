<template>
    <v-dialog :value="value" persistent :max-width="machines.length * 150 || 480">
        <v-form @submit.prevent="save">
            <v-card>
                <v-card-title>
                    <span class="title gray--text">Transfer to another machine</span>
                    <v-spacer />
                    <v-btn icon small @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-progress-circular indeterminate v-if="loading"></v-progress-circular>
                    <v-spacer></v-spacer>
                </v-card-actions>
                <v-card-text>
                    <!-- <v-expand-transition>
                        <v-card-text v-if="machine && washDry">
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Job Order:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{washDry.job_order}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Name:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{machine.customer.name}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Service name:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{washDry.service_name}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Machine name:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{washDry.dryer_name || washDry.washer_name}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Minutes:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{washDry.minutes}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Price:</div></v-flex>
                                <v-flex xs8><div class="ma-1">P {{parseFloat(washDry.price).toFixed(2)}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Time activated:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{moment(washDry.used).format('LLL')}}</div></v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex xs4><div class="text-xs-right ma-1">Account name:</div></v-flex>
                                <v-flex xs8><div class="ma-1">{{washDry.staff_name}}</div></v-flex>
                            </v-layout>

                        </v-card-text>
                    </v-expand-transition> -->
                    <wash-dry-info :machine="tempMachine"></wash-dry-info>
                    <v-layout>
                        <machine-tile v-for="m in machines" :key="m.id" :machine="m" @open="open" />
                    </v-layout>
                    <v-expand-transition>
                        <v-card v-if="!!activeMachine">
                            <v-card-text>
                                <v-textarea v-model="remarks" ref="remarks" label="Reason for transfer" outline></v-textarea>
                                <!-- <pre>{{activeMachine}}</pre> -->
                            </v-card-text>
                        </v-card>
                    </v-expand-transition>
                </v-card-text>
                <v-card-actions>
                    <v-btn round class="primary" type="submit" v-if="activeMachine" :loading="saving">Confirm</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>

import MachineTile from './MachineTile.vue';
import WashDryInfo from './WashDryInfo.vue';

export default {
    components: {
        MachineTile,
        WashDryInfo
    },
    props: ['value', 'machine'],
    data() {
        return {
            machines: [],
            loading: false,
            remarks: null,
            tempMachine: null
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        getMachines() {
            if(this.machine) {
                this.loading = true;
                axios.get(`/api/machines/${this.machine.machine_type}`).then((res, rej) => {
                    this.machines = res.data.result;
                }).finally(() => {
                    this.loading = false;
                });
            }
        },
        open(machine) {
            if(machine.is_running) {
                alert('Cannot transfer to running machine')
                return;
            }
            this.machines.forEach(m => {
                Vue.set(m, 'selected', false);
            });
            Vue.set(machine, 'selected', true);
            setTimeout(() => {
                this.$refs.remarks.$el.querySelector('textarea').select();
            }, 100);
        },
        save() {
            this.$store.dispatch('remote/transfer', {
                from: this.machine.id,
                to: this.activeMachine.id,
                formData: {
                    remarks: this.remarks
                }
            }).then((res, rej) => {
                this.$emit('transfered', res.data);
                this.close();
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['remote/getErrors'];
        },
        activeMachine() {
            return this.machines.find(m => m.selected);
        },
        saving() {
            return this.$store.getters['remote/isReactivating'];
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.getMachines();
                if(val && !!this.machine) {
                    this.tempMachine = this.machine;
                } else {
                    // this.washDry = null;
                    this.tempMachine = null;
                    this.remarks = null;
                    this.$store.commit('remote/clearErrors');
                }
            } else {
                this.machines = [];
            }
        }
    }
}
</script>
