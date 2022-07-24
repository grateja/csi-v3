<template>
    <div>
        <template v-if="loading">
            <v-progress-linear indeterminate></v-progress-linear>
        </template>
        <template v-else>
            <h3 class="title grey--text">Dryers</h3>
            <v-divider class="mb-3"></v-divider>
            <v-layout row wrap>
                <v-flex xs2 v-for="dryer in machines.dryers" :key="dryer.id">
                    <machine-item :machine="dryer" @activate="activateMachine"></machine-item>
                </v-flex>
            </v-layout>
            <h3 class="title grey--text mt-4">Washers</h3>
            <v-divider class="mb-3"></v-divider>
            <v-layout row wrap>
                <v-flex xs2 v-for="washer in machines.washers" :key="washer.id">
                    <machine-item :machine="washer" @activate="activateMachine"></machine-item>
                </v-flex>
            </v-layout>
            <h3 class="title grey--text mt-4">Titan Dryers</h3>
            <v-divider class="mb-3"></v-divider>
            <v-layout row wrap>
                <v-flex xs2 v-for="dryer in machines.titan_dryers" :key="dryer.id">
                    <machine-item :machine="dryer" @activate="activateMachine"></machine-item>
                </v-flex>
            </v-layout>
            <h3 class="title grey--text mt-4">Titan Washers</h3>
            <v-divider class="mb-3"></v-divider>
            <v-layout row wrap>
                <v-flex xs2 v-for="washer in machines.titan_washers" :key="washer.id">
                    <machine-item :machine="washer" @activate="activateMachine"></machine-item>
                </v-flex>
            </v-layout>
        </template>
        <machine-dialog v-model="openMachineDialog" :machine="activeMachine" @ok="ok"></machine-dialog>
        <!-- <washer-dialog v-model="openWasherDialog" :washer="activeWasher" @ok="ok"></washer-dialog> -->
    </div>
</template>

<script>
import MachineItem from './MachineItem.vue';
import MachineDialog from './MachineDialog.vue';
// import WasherDialog from './WasherDialog.vue';
import moment from 'moment';

export default {
    components: {
        moment,
        MachineItem,
        MachineDialog,
        // WasherDialog
    },
    data() {
        return {
            machines: [],
            loading: false,
            openMachineDialog: false,
            openWasherDialog: false,
            activeMachine: null,
            machineChecker: null,
            lastActiveMachine: null,
            cancelSource: null,
            isDestroyed: false
            // activeWasher: null
        }
    },
    methods: {
        get() {
            if(this.activeBranch) {
                this.loading = true;

                axios.get(`/api/machines/${this.activeBranch.id}/view-all`).then((res, rej) => {
                    console.log('machines', res.data);
                    this.machines = res.data.result;
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                    console.log(err);
                });
            }
        },
        activateMachine(machine) {
            console.log('activate', machine);
            this.activeMachine = machine;
            this.openMachineDialog = true;
        },
        // activateWasher(washer) {
        //     console.log('activate washer', washer);
        //     this.activeWasher = washer;
        //     this.openWasherDialog = true;
        // },
        ok() {
            // this.get();
        },
        remainingTime(machine) {
            let timeActivated = machine.time_activated;
            let timeEndsIn = machine.time_ends_in;
        },
        endsIn(machine) {
            if(machine.is_running) {
                return 'Ends ' + moment(machine.time_ends_in).fromNow();
            } else {
                return 'Last activated ' + moment(machine.time_ends_in).fromNow();
            }
        },
        className(machine) {
            if(machine.is_running) {
                return 'green white--text';
            }
        },
        checkMachineTap() {
            this.cancelSource = axios.CancelToken.source();

            axios.get('/api/machines/last-activated',{
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                // console.log(res.data);
                this.lastActiveMachine = res.data.machine;
            }).catch(err => {
            }).finally(() => {
                if(!this.isDestroyed) {
                    clearTimeout(this.machineChecker);
                    this.machineChecker = null;
                    this.machineChecker = setTimeout(this.checkMachineTap, 3000);
                }
            });
        }
    },
    computed: {
        activeBranch() {
            return this.$store.getters.getActiveBranch;
        }
    },
    watch: {
        lastActiveMachine(newVal, oldVal) {
            console.log('last machine', this.lastActiveMachine.updated_at);
            if(oldVal != null) {
                if(newVal.updated_at != oldVal.updated_at) {
                    console.log('new val changed', newVal);
                    this.get();
                }
            }
        }
    },
    created() {
        this.get();
        this.machineChecker = setTimeout(this.checkMachineTap, 1000);
    },
    beforeDestroy() {
        console.log('clear timeout');
        if(this.cancelSource){
            this.cancelSource.cancel();
        }
        this.isDestroyed = true;
        clearTimeout(this.machineChecker);
        this.machineChecker = null;
    }
}
</script>

