<template>
    <v-card class="ma-3 pa-4" flat color="transparent">

        <h4 class="title grey--text">Remote panel</h4>
        <v-divider class="my-3"></v-divider>

        <h4 class="grey--text mt-4">Regular Dryers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <machine-tile v-for="machine in machines.dryers" :key="machine.id" :machine="machine" @open="open" />
        </v-layout>

        <h4 class="grey--text mt-4">Regular Washers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <machine-tile v-for="machine in machines.washers" :key="machine.id" :machine="machine" @open="open" />
        </v-layout>

        <h4 class="grey--text mt-4">Titan Dryers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <machine-tile v-for="machine in machines.titan_dryers" :key="machine.id" :machine="machine" @open="open" />
        </v-layout>

        <h4 class="grey--text mt-4">Titan Washers</h4>
        <v-divider class="my-2"></v-divider>
        <v-layout row wrap>
            <machine-tile v-for="machine in machines.titan_washers" :key="machine.id" :machine="machine" @open="open" />
        </v-layout>

        <customer-browser v-model="openCustomerBrowserDialog" :machine="activeMachine" @machineActivated="updateMachine" />
        <machine-dialog :machine="activeMachine" v-model="openMachineDialog" @forceStop="updateMachine" />
    </v-card>
</template>


<script>
import CustomerBrowser from './CustomerBrowser.vue';
import MachineTile from './MachineTile.vue';
import MachineDialog from './MachineDialog.vue';

export default {
    components: {
        CustomerBrowser,
        MachineTile,
        MachineDialog
    },
    data() {
        return {
            openCustomerBrowserDialog: false,
            openMachineDialog: false,
            activeMachine: null,
            machines: [],
            interval: null,
            componentKey: 1
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
                this.openMachineDialog = true;
            } else {
                // open customer browser
                this.openCustomerBrowserDialog = true;
            }
        },
        updateMachine(data) {
            this.activeMachine.time_ends_in = data.machine.time_ends_in;
            this.activeMachine.customer_name = data.machine.customer_name;
            this.activeMachine.total_minutes = data.machine.total_minutes;
            this.activeMachine.is_running = data.machine.is_running;
        }
    },
    created() {
        this.load();
    }
}
</script>
