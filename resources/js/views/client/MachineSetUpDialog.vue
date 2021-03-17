<template>
    <v-dialog :value="value" persistent max-width="1020px">
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="title grey--text">Machine details</v-card-title>
                <v-card-text>
                    <v-layout row>
                        <v-flex>
                            <v-text-field v-model="formData.washerCount" outline label="Washer count"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wStartIp" outline label="Start IP"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wInitialTime" outline label="Initial time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wAdditionalTime" outline label="Additional time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wInitialPrice" outline label="Initial price"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.wAdditionalPrice" outline label="Additional price"></v-text-field>
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
                            <v-text-field v-model="formData.twInitialTime" outline label="Initial time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twAdditionalTime" outline label="Additional time"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twInitialPrice" outline label="Initial price"></v-text-field>
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.twAdditionalPrice" outline label="Additional price"></v-text-field>
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
            formData: {
                gateWay: '192.168.210',
                washerCount: 5,
                wStartIp: '11',
                wInitialTime: 38,
                wAdditionalTime: 12,
                wInitialPrice: 60,
                wAdditionalPrice: 20,
                dryerCount: 5,
                dStartIp: '31',
                dInitialTime: 10,
                dAdditionalTime: 10,
                dInitialPrice: 15,
                dAdditionalPrice: 15,
                twasherCount: 0,
                twStartIp: '21',
                twInitialTime: 38,
                twAdditionalTime: 12,
                twInitialPrice: 80,
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
            });
        },
        close() {
            this.$emit('input', false);
        }
    },
    computed: {
        saving() {
            return this.$store.getters['client/settingUpMachine'];
        }
    }
}
</script>
