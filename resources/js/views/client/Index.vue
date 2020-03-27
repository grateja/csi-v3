<template>
    <v-container>
        <v-btn @click="reset" :loading="reseting" round>reset</v-btn>
        <v-btn @click="setUpUser" round>Set up user</v-btn>
        <v-btn @click="setUpClient" round v-if="!!user">Set up client</v-btn>
        <v-btn @click="setUpMachines" round>Set up machines</v-btn>
        <v-divider></v-divider>
        <pre>{{user}}</pre>
        <pre>{{client}}</pre>
        <pre>{{machines}}</pre>
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
