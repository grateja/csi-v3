<template>
    <v-dialog :value="value" persistent max-width="1020px">
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="title grey--text">Machine details</v-card-title>
                <v-card-text v-if="!loading && machinesRegistered">
                    <h3>There are machines already set up</h3>
                    <v-divider></v-divider>
                    <v-layout>
                        <v-flex xs3 sm2 md2 class="text-xs-right mr-1">Washers:</v-flex>
                        <v-flex xs1>{{machines.washers.length}}</v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs3 sm2 md2 class="text-xs-right mr-1">Dryers:</v-flex>
                        <v-flex xs1>{{machines.dryers.length}}</v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs3 sm2 md2 class="text-xs-right mr-1">Titan Washers:</v-flex>
                        <v-flex xs1>{{machines.titan_washers.length}}</v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs3 sm2 md2 class="text-xs-right mr-1">Titan Dryers:</v-flex>
                        <v-flex xs1>{{machines.titan_dryers.length}}</v-flex>
                    </v-layout>
                </v-card-text>
                <v-card-text>
                    <v-layout row>
                        <v-flex>
                            <v-text-field v-model="formData.washerCount" outline label="Washer count"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wStartIp" outline label="Start IP"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wInitialTime" outline label="Delicate time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wAdditionalTime" outline label="Regular time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wInitialPrice" outline label="Delicate price"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wAdditionalPrice" outline label="Delicate price"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex>
                            <v-text-field v-model="formData.dryerCount" outline label="Dryer count"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dStartIp" outline label="Start IP"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dInitialTime" outline label="Initial time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dAdditionalTime" outline label="Additional time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dInitialPrice" outline label="Initial price"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dAdditionalPrice" outline label="Additional price"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex>
                            <v-text-field v-model="formData.twasherCount" outline label="Titan Washer count"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twStartIp" outline label="Start IP"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twInitialTime" outline label="Delicate time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twAdditionalTime" outline label="Regular time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twInitialPrice" outline label="Delicate price"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twAdditionalPrice" outline label="Regular price"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex>
                            <v-text-field v-model="formData.tdryerCount" outline label="Titan Dryer count"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.tdStartIp" outline label="Start IP"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.tdInitialTime" outline label="Initial time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.tdAdditionalTime" outline label="Additional time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.tdInitialPrice" outline label="Initial price"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.tdAdditionalPrice" outline label="Additional price"></v-text-field>
                        </v-flex>
                    </v-layout>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn type="submit" class="primary" round :loading="saving">Submit</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value'
    ],
    data() {
        return {
            loading: false,
            machines: null,
            formData: {
                gateWay: '192.168.210',
                washerCount: 5,
                wStartIp: '11',
                wInitialTime: 24,
                wAdditionalTime: 12,
                wInitialPrice: 40,
                wAdditionalPrice: 20,
                dryerCount: 5,
                dStartIp: '31',
                dInitialTime: 10,
                dAdditionalTime: 10,
                dInitialPrice: 15,
                dAdditionalPrice: 15,
                twasherCount: 0,
                twStartIp: '21',
                twInitialTime: 24,
                twAdditionalTime: 12,
                twInitialPrice: 60,
                twAdditionalPrice: 40,
                tdryerCount: 0,
                tdStartIp: '41',
                tdInitialTime: 10,
                tdAdditionalTime: 25,
                tdInitialPrice: 10,
                tdAdditionalPrice: 25
            }
        }
    },
    methods: {
        save() {
            this.$store.dispatch('client/setUpMachines', this.formData).then((res, rej) => {
                this.$emit('saved', res.data);
                this.close();
                this.$router.push('/remote-panel')
            });
        },
        checkSetUp() {
            this.loading = true;
            axios.get(`/api/machines`).then((res, rej) => {
                this.machines = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        close() {
            this.$emit('input', false);
        }
    },
    computed: {
        saving() {
            return this.$store.getters['client/settingUpMachine'];
        },
        machinesRegistered() {
            return this.machines != null &&
                (this.machines.dryers.length > 0 || this.machines.washers.length > 0 || this.machines.titan_dryers.length > 0 || this.machines.titan_washers.length > 0);
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.checkSetUp();
            }
        }
    }
}
</script>
