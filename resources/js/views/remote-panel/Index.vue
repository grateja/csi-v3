<template>
    <v-card class="ma-3 pa-4 machines-container" flat color="transparent" ref="machines">
        <h4 class="title white--text">Remote panel</h4>
        <v-progress-linear v-if="loading" indeterminate class="my-3"></v-progress-linear>
        <v-divider v-else class="my-3"></v-divider>

        <v-btn to="/remote-panel" exact active-class="primary" round>8 Kilo Capacity</v-btn>
        <v-btn to="/remote-panel/titan" active-class="primary" round exact>10 Kilo Capacity</v-btn>

        <template>
            <div v-if="machines.dryers && !loading && !titan">
                <h4 class="white--text mt-4">Dryers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout class="panel">
                    <machine-tile v-for="machine in machines.dryers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>

        <template>
            <div v-if="machines.washers && !loading && !titan">
                <h4 class="white--text mt-4">Washers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout class="panel">
                    <machine-tile v-for="machine in machines.washers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>

        <template>
            <div v-if="machines.titan_dryers && !loading && titan">
                <h4 class="white--text mt-4">Dryers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout class="panel">
                    <machine-tile v-for="machine in machines.titan_dryers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>
        <template>
            <div v-if="machines.titan_washers && !loading && titan">
                <h4 class="white--text mt-4">Washers</h4>
                <v-divider class="my-2"></v-divider>
                <v-layout  class="panel">
                    <machine-tile v-for="machine in machines.titan_washers" :key="machine.id" :machine="machine" @open="open" />
                </v-layout>
            </div>
        </template>

        <customer-browser @rework="rework" v-model="openCustomerBrowserDialog" :machine="activeMachine" @machineActivated="updateMachine" />
        <machine-dialog @rework="rework" :machine="activeMachine" v-model="openMachineDialog" @forceStop="updateMachine" @activated="updateMachine" @transfered="transfered" />
        <rework-dialog @reworkConfirm="reworkConfirm" :machine="activeMachine" v-model="openReworkDialog"></rework-dialog>
    </v-card>
</template>


<script>
import CustomerBrowser from './CustomerBrowser.vue';
import MachineTile from './MachineTile.vue';
import MachineDialog from './MachineDialog.vue';
import ReworkDialog from './ReworkDialog.vue';

export default {
    components: {
        CustomerBrowser,
        MachineTile,
        MachineDialog,
        ReworkDialog
    },
    data() {
        return {
            openCustomerBrowserDialog: false,
            openMachineDialog: false,
            openReworkDialog: false,
            activeMachine: null,
            machines: [],
            interval: null,
            componentKey: 1,
            loading: false,
            timer: null,
            lastTimeStamp: null
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
            this.activeMachine.time_activated = data.machine.time_activated;
            this.activeMachine.customer_dry_id = data.machine.customer_dry_id;
            this.activeMachine.customer_wash_id = data.machine.customer_wash_id;
        },
        reworkConfirm() {
            this.openCustomerBrowserDialog = false;
            this.openMachineDialog = false;
            console.log('done');
        },
        transfered(data) {
            // this.activeMachine.time_ends_in = data.from.time_ends_in;
            // this.activeMachine.customer = data.from.customer;
            // this.activeMachine.user_name = data.from.user_name;
            // this.activeMachine.total_minutes = data.from.total_minutes;
            // this.activeMachine.is_running = data.from.is_running;
            // this.activeMachine.remarks = data.from.remarks;
            // this.activeMachine.time_activated = data.from.time_activated;
            // this.activeMachine.customer_dry_id = data.from.customer_dry_id;
            // this.activeMachine.customer_wash_id = data.from.customer_wash_id;

            // var transferedTo =
        },
        getUpdate() {
            if(this.$refs.machines) {
                axios.get('/api/machines').then((res, rej) => {
                    this.machines = res.data.result;
                });
            }
        },
        startTimer() {
            this.timer = setTimeout(this.check, 1000);
        },
        check() {
            if(this.$refs.machines) {
                axios.get('/checker/').then((res, rej)=> {
                    this.lastTimeStamp = res.data;
                    setTimeout(this.startTimer, 1000)
                }).catch(err => {
                    setTimeout(this.startTimer, 4000)
                });
            }
        },
        rework(machine) {
            this.activeMachine = machine;
            this.openReworkDialog = true;
        }
    },
    created() {
        this.load();
    },
    watch: {
        lastTimeStamp(val) {
            if(val) {
                this.getUpdate();
            }
        }
    },
    computed: {
        titan() {
            return !!this.$route.params.mt;
        }
    }
}
</script>

<style scoped>
    .machines-container {
        border: 1px solid red;
    }
    .panel {
        overflow-y: auto;
        padding: 30px;
    }
</style>
