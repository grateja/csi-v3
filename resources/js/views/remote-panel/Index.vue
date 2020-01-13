<template>
    <v-card class="ma-3 pa-4" flat color="transparent">

        <h4 class="title grey--text">Remote panel</h4>
        <v-divider class="my-3"></v-divider>

        <h4 class="grey--text mt-4">Regular Dryers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <v-flex xs4 sm3 md2 xl1 v-for="machine in machines.dryers" :key="machine.id">
                <v-hover v-slot:default="{ hover }">
                    <v-card :elevation="hover ? 12 : 2" class="ma-1 pointer" @click="open(machine)">
                        <v-card-title><v-spacer />{{machine.machine_name}}<v-spacer /></v-card-title>
                        <v-card-text>
                            Ends in {{ moment(machine.time_ends_in).fromNow()}}
                        </v-card-text>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>

        <h4 class="grey--text mt-4">Regular Washers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <v-flex xs4 sm3 md2 xl1 v-for="machine in machines.washers" :key="machine.id">
                <v-hover v-slot:default="{ hover }">
                    <v-card :elevation="hover ? 12 : 2" class="ma-1 pointer" @click="open(machine)">
                        <v-card-title><v-spacer />{{machine.machine_name}}<v-spacer /></v-card-title>
                        <v-card-text>
                            Ends in {{ moment(machine.time_ends_in).fromNow()}}
                        </v-card-text>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>

        <h4 class="grey--text mt-4">Titan Dryers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <v-flex xs4 sm3 md2 xl1 v-for="machine in machines.titan_dryers" :key="machine.id">
                <v-hover v-slot:default="{ hover }">
                    <v-card :elevation="hover ? 12 : 2" class="ma-1 pointer" @click="open(machine)">
                        <v-card-title><v-spacer />{{machine.machine_name}}<v-spacer /></v-card-title>
                        <v-card-text>
                            Ends in {{ moment(machine.time_ends_in).fromNow()}}
                        </v-card-text>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>

        <h4 class="grey--text mt-4">Titan Washers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <v-flex xs4 sm3 md2 xl1 v-for="machine in machines.titan_washers" :key="machine.id">
                <v-hover v-slot:default="{ hover }">
                    <v-card :elevation="hover ? 12 : 2" class="ma-1 pointer" @click="open(machine)">
                        <v-card-title><v-spacer />{{machine.machine_name}}<v-spacer /></v-card-title>
                        <v-card-text>
                            Ends in {{ moment(machine.time_ends_in).fromNow()}}
                        </v-card-text>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>

        <customer-browser v-model="openCustomerBrowserDialog" :machine="activeMachine" @selectCustomer="selectCustomer" />
    </v-card>
</template>


<script>
import CustomerBrowser from './CustomerBrowser.vue';

export default {
    components: {
        CustomerBrowser
    },
    data() {
        return {
            openCustomerBrowserDialog: false,
            activeMachine: null,
            machines: []
        }
    },
    methods: {
        load() {
            axios.get('/api/machines').then((res, rej) => {
                this.machines = res.data.result;
            });
        },
        open(machine) {
            this.activeMachine = machine;
            if(machine.is_running) {
                // show options instead
            } else {
                // open customer browser
                this.openCustomerBrowserDialog = true;
            }
        },
        selectCustomer(customer) {
        }
    },
    created() {
        this.load();
    }
}
</script>
