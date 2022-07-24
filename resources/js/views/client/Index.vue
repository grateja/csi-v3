<template>
    <v-container>
        <v-btn @click="reset" :loading="reseting" round>reset</v-btn>
        <v-btn @click="setUpUser" round>Set up owner account</v-btn>
        <v-btn @click="setUpClient" round v-if="!!user">Set up shop details</v-btn>
        <v-btn @click="setUpMachines" round>Set up machines</v-btn>
        <v-divider class="my-3"></v-divider>

        <v-card v-if="!!user" class="translucent rounded-card my-2" max-width="580px">
            <v-card-title class="grey--text title">Owner details</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Owner ID:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ user.id }}</span>
                        <div class="caption grey--text font-italic">Do not modify owner ID once set up</div>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Name:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ user.name }}</span>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Email:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ user.email }}</span>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn icon @click="setUpUser">
                    <v-icon>edit</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>

        <v-card v-if="!!client" class="translucent rounded-card my-2" max-width="580px">
            <v-card-title class="grey--text title">Shop details</v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Shop name:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ client.shop_name }}</span>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Shop Email:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ client.shop_email }}</span>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Contact number:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ client.shop_number }}</span>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs3 class="text-xs-right">
                        <span class="grey--text caption mr-2">Address:</span>
                    </v-flex>
                    <v-flex xs9>
                        <span>{{ client.address }}</span>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn icon @click="setUpClient">
                    <v-icon>edit</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>


        <!-- <pre>{{user}}</pre> -->
        <!-- <pre>{{client}}</pre> -->
        <!-- <pre>{{machines}}</pre> -->
        <user-dialog v-model="openUserDialog" :user="user" @save="updateUser" />
        <client-dialog v-model="openClientDialog" :client="client" @save="updateClient" />
        <machine-set-up-dialog v-model="openMachineDialog" />
    </v-container>
</template>

<script>
import UserDialog from './UserDialog.vue';
import ClientDialog from './ClientDialog.vue';
import MachineSetUpDialog from './MachineSetUpDialog.vue';

export default {
    components: {
        UserDialog,
        ClientDialog,
        MachineSetUpDialog
    },
    data() {
        return {
            client: null,
            user: null,
            machines: [],
            reseting: false,
            openUserDialog: false,
            openClientDialog: false,
            openMachineDialog: false
        }
    },
    methods: {
        load() {
            axios.get('/api/developer/client').then((res, rej) => {
                this.client = res.data.client;
                this.user = res.data.user;
                this.machines = res.data.machines;
            });
        },
        reset() {
            if(confirm('reset?')) {
                this.reseting = true;
                axios.post('/api/developer/reset').then((res, rej) => {
                    this.client = null;
                    this.user = null;
                    this.machines = [];
                }).finally(() => {
                    this.reseting = false;
                });
            }
        },
        setUpUser() {
            this.openUserDialog = true;
        },
        setUpClient() {
            this.openClientDialog = true;
        },
        setUpMachines() {
            this.openMachineDialog = true;
        },
        updateUser(data) {
            this.user = data.user;
        },
        updateClient(data) {
            this.client = data.client;
        },
        updateMachines(data) {
            this.machines = data.machines;
        }
    },
    created() {
        this.load();
    }
}
</script>
