<template>
    <v-card class="ma-3 pa-4" flat color="transparent" ref="machines">

        <h4 class="title grey--text">Remote panel</h4>
        <v-progress-linear v-if="loading" indeterminate class="my-3"></v-progress-linear>
        <v-divider v-else class="my-3"></v-divider>

        <template>
            <div v-if="machines.dryers && !loading">
                <h4 class="grey--text mt-4">Regular Dryers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout row wrap>
                    <machine-tile v-for="machine in machines.dryers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>

        <template>
            <div v-if="machines.washers && !loading">
                <h4 class="grey--text mt-4">Regular Washers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout row wrap>
                    <machine-tile v-for="machine in machines.washers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>

        <template>
            <div v-if="machines.titan_dryers && !loading">
                <h4 class="grey--text mt-4">Titan Dryers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout row wrap>
                    <machine-tile v-for="machine in machines.titan_dryers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>
        <template>
            <div v-if="machines.titan_washers && !loading">
                <h4 class="grey--text mt-4">Titan Washers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout row wrap>
                    <machine-tile v-for="machine in machines.titan_washers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>

        <customer-browser v-model="openCustomerBrowserDialog" :machine="activeMachine" @machineActivated="updateMachine" />
        <machine-dialog :machine="activeMachine" v-model="openMachineDialog" @forceStop="updateMachine" @activated="updateMachine" />
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
            componentKey: 1,
            loading: false,
            timer: null
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/machines').then((res, rej) => {
                this.machines = res.data.result;
            }).finally(() => {
                this.loading = false;
                this.startTimer();
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
            this.activeMachine.customer = data.machine.customer;
            this.activeMachine.user_name = data.machine.user_name;
            this.activeMachine.total_minutes = data.machine.total_minutes;
            this.activeMachine.is_running = data.machine.is_running;
            this.activeMachine.remarks = data.machine.remarks;
        },
        getUpdate() {
            console.log('get update');
            if(this.$refs.machines) {
                axios.get('/api/machines').then((res, rej) => {
                    this.machines = res.data.result;
                    this.startTimer();
                }).catch(err => {
                    setTimeout(this.startTimer(), 4000)
                });
            }
        },
        startTimer() {
            this.timer = setTimeout(this.getUpdate, 1000);
        }
    },
    created() {
        this.load();
    }
}
</script>
