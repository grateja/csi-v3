<template>
    <div>
        <v-btn round class="translucent" :class="{primary: machineType == 'washer'}" @click="machineType = 'washer'">Regular washers</v-btn>
        <v-btn round class="translucent" :class="{primary: machineType == 'dryer'}" @click="machineType = 'dryer'">Regular dryers</v-btn>
        <v-divider class="my-3"></v-divider>
        <date-navigator v-model="date" />
        <v-divider class="my-3"></v-divider>
        <v-btn round class="translucent" v-if="isDeveloper" @click="add">
            <v-icon left>add</v-icon>
            add
        </v-btn>
        <v-card class="rounded-card translucent-table" v-if="machines ">
            <v-data-table :headers="headers" :items="machines " :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr @click="view(props.item)">
                        <td>{{props.item.machine_name}}</td>
                        <td>
                            <div>{{ props.item.model }}</div>
                            <div v-if="isDeveloper" :class="{'red--text' : ipAddressConflict(props.item)}">
                                {{ props.item.ip_address }}
                            </div>
                        </td>
                        <td>{{ props.item.total_usage }}</td>
                        <td>{{ props.item.usage_today }}</td>
                        <td>
                            <v-tooltip top v-if="isOwner || isDeveloper">
                                <v-btn slot="activator" small icon @click="edit(props.item, $event)" outline>
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit prices and minutes</span>
                            </v-tooltip>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <machine-usages :machine="activeMachine" v-model="openMachineDialog" :activeDate="date" />
        <elux-machine-settings :machine="activeMachine" v-model="openMachineSettings" @save="updateMachines" :machineType="machineType" @machineDeleted="machineDeleted"/>

    </div>
</template>
<script>
import MachineUsages from './MachineUsages.vue';
import DateNavigator from '../shared/DateNavigator.vue';
import EluxMachineSettings from './EluxMachineSettings.vue';
export default {
    components: {
        MachineUsages,
        DateNavigator,
        EluxMachineSettings
    },
    data() {
        return {
            machineType: 'washer',
            date: moment().format('YYYY-MM-DD'),
            activeMachine: null,
            openMachineSettings: false,
            openMachineDialog: false,
            loading: false,
            machines : [],
            dryers: [],
            headers: [
                {
                    text: 'Machine name',
                    sortable: false
                },
                {
                    text: 'Model',
                    sortable: false
                },
                {
                    text: 'Total usage',
                    sortable: false
                },
                {
                    text: 'Usage for the day',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get(`/api/machines/elux/${this.machineType}`, {
                params: {
                    date: this.date
                }
            }).then((res, rej) => {
                this.machines  = res.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        add() {
            this.activeMachine = null;
            this.openMachineSettings = true;
        },
        view(machine) {
            this.activeMachine = machine;
            this.openMachineDialog = true;
        },
        edit(item, event) {
            event.stopPropagation();
            this.activeMachine = item;
            this.openMachineSettings = true;
        },
        ipAddressConflict(item) {
            return !!this.machines.find(m => m.ip_address == item.ip_address && m.id != item.id);
        },
        updateMachines(data) {
            if(data.applyToAll) {
                this.load();
                // this.machines = data.result;
            } else {
                if(data.action == 'update') {
                    this.activeMachine.machine_name = data.result.machine_name;
                    this.activeMachine.initial_time = data.result.initial_time;
                    this.activeMachine.additional_time = data.result.additional_time;
                    this.activeMachine.initial_price = data.result.initial_price;
                    this.activeMachine.additional_price = data.result.additional_price;
                    this.activeMachine.ip_address = data.result.ip_address;
                } else {
                    console.log(data);
                    this.machines.push(data.result);
                }
            }
        },
        machineDeleted(machine) {
            this.machines = this.machines.filter(m => m.id != machine.id);
        },
    },
    created() {
        this.load();
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        },
        isDeveloper() {
            return this.$store.getters.isDeveloper;
        }
    },
    watch: {
        machineType(val) {
            this.date = moment().format('YYYY-MM-DD');
            this.machines = [];
            this.load();
        },
        date(val) {
            this.load();
        }
    }
}
</script>
