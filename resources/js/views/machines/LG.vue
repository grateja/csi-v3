<template>
    <div>
        <!-- <v-btn round class="ml-0 translucent" to="/machines/remarks" >Machines remarks <v-icon>chevron_right</v-icon> </v-btn> -->
        <v-btn round class="translucent" :class="{primary: tab == 'rw'}" @click="tab = 'rw'">Regular washers</v-btn>
        <v-btn round class="translucent" :class="{primary: tab == 'rd'}" @click="tab = 'rd'">Regular dryers</v-btn>
        <v-btn round class="translucent" :class="{primary: tab == 'tw'}" @click="tab = 'tw'">Titan washers</v-btn>
        <v-btn round class="translucent" :class="{primary: tab == 'td'}" @click="tab = 'td'">Titan dryers</v-btn>
        <v-btn round class="translucent" :class="{primary: tab == 'undefined'}" @click="tab = 'undefined'" v-if="isOwner || isDeveloper">Unregistered</v-btn>

        <v-divider class="my-3"></v-divider>
        <date-navigator v-model="date" />
        <v-divider class="my-3"></v-divider>

        <v-btn round class="translucent" v-if="isDeveloper" @click="add">
            <v-icon left>add</v-icon>
            add
        </v-btn>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="machines" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr @click="view(props.item)">
                        <td>
                            <div>{{ props.item.machine_name }}</div>
                            <div v-if="isDeveloper" :class="{'red--text' : ipAddressConflict(props.item)}">
                                {{ props.item.ip_address }}
                            </div>
                        </td>
                        <td>{{ status(props.item) }}</td>
                        <!-- <td>{{ customer(props.item) }}</td> -->
                        <td>{{ props.item.total_usage }}</td>
                        <td>{{ props.item.usage_today }}</td>
                        <td>P {{ parseFloat(props.item.initial_price).toFixed(2) }}</td>
                        <td>{{ props.item.additional_price ? 'P ' + parseFloat(props.item.additional_price).toFixed(2) : 'Disabled' }}</td>
                        <td>{{ props.item.initial_time }} Mins.</td>
                        <td>{{ (props.item.additional_time)? props.item.additional_time + 'Mins.' : 'Disabled' }}</td>
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
        <machine-settings :machine="activeMachine" v-model="openMachineSettings" @save="updateMachines" :mt="tab" @machineDeleted="machineDeleted" />
    </div>
</template>

<script>
import MachineUsages from './MachineUsages.vue';
import DateNavigator from '../shared/DateNavigator.vue';
import MachineSettings from './MachineSettings.vue';
export default {
    components: {
        MachineUsages,
        DateNavigator,
        MachineSettings
    },
    data() {
        return {
            date: moment().format('YYYY-MM-DD'),
            openMachineDialog: false,
            openMachineSettings: false,
            activeMachine: null,
            loading: false,
            tab: null,
            machines: [],
            headers: [
                {
                    text: 'Machine name',
                    sortable: false
                },
                {
                    text: 'Status',
                    sortable: false
                },
                // {
                //     text: 'Customer',
                //     sortable: false
                // },
                {
                    text: 'Total usage',
                    sortable: false
                },
                {
                    text: 'Usage for the day',
                    sortable: false
                },
                {
                    text: 'Initial price',
                    sortable: false
                },
                {
                    text: 'Additional price',
                    sortable: false
                },
                {
                    text: 'Initial time',
                    sortable: false
                },
                {
                    text: 'Additional time',
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
            axios.get(`/api/machines/${this.tab}`, {
                params: {
                    date: this.date
                }
            }).then((res, rej) => {
                this.machines = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        remainingTime(machine) {
            if(!!machine) {
                var t = Date.parse(machine.time_ends_in) - Date.parse(new Date());
                var hours = Math.floor((t/(1000*60*60)) % 24);
                var minutes = Math.floor((t/1000/60) % 60);
                return `${hours > 0 ? hours + ' hour and' : ''} ${minutes} minutes`;
            }
            return 'keme';
        },
        status(machine) {
            if(!machine.time_activated) {
                return 'Never used';
            } else if(machine.is_running) {
                return 'Ends in ' + this.remainingTime(machine);
            } else {
                return 'Last used  ' + moment(machine.time_ends_in).fromNow();
            }
        },
        customer(machine) {
            if(machine.is_running) {
                return machine.customer_name;
            }
        },
        view(machine) {
            this.activeMachine = machine;
            this.openMachineDialog = true;
        },
        edit(machine, event) {
            event.stopPropagation();
            console.log(machine);
            this.activeMachine = machine;
            this.openMachineSettings = true;
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
        add() {
            this.activeMachine = null;
            this.openMachineSettings = true;
        },
        machineDeleted(machine) {
            this.machines = this.machines.filter(m => m.id != machine.id);
        },
        ipAddressConflict(item) {
            return !!this.machines.find(m => m.ip_address == item.ip_address && m.id != item.id);
        }
    },
    watch: {
        tab(val) {
            this.date = moment().format('YYYY-MM-DD');
            this.items = [];
            this.load();
        },
        date(val) {
            this.load();
        }
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
    created() {
        this.tab = 'rw';
    }
}
</script>
