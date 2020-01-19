<template>
    <v-container>
        <h3 class="title grey--text">Machines</h3>


        <v-divider class="my-3"></v-divider>
        <v-btn round :class="{primary: tab == 'rw'}" @click="tab = 'rw'">Regular washers</v-btn>
        <v-btn round :class="{primary: tab == 'rd'}" @click="tab = 'rd'">Regular dryers</v-btn>
        <v-btn round :class="{primary: tab == 'tw'}" @click="tab = 'tw'">Titan washers</v-btn>
        <v-btn round :class="{primary: tab == 'td'}" @click="tab = 'td'">Titan dryers</v-btn>

        <v-divider class="my-3"></v-divider>
        <date-navigator v-model="date" />
        <v-divider class="my-3"></v-divider>

        <v-data-table :headers="headers" :items="machines" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <tr @click="view(props.item)">
                    <td>{{ props.item.machine_name }}</td>
                    <td>{{ status(props.item) }}</td>
                    <td>{{ customer(props.item) }}</td>
                    <td>{{ props.item.total_usage }}</td>
                    <td>{{ props.item.usage_today }}</td>
                </tr>
            </template>
        </v-data-table>
        <machine-usages :machine="activeMachine" v-model="openMachineDialog" />
    </v-container>
</template>

<script>
import MachineUsages from './MachineUsages.vue';
import DateNavigator from '../shared/DateNavigator.vue';

export default {
    components: {
        MachineUsages,
        DateNavigator
    },
    data() {
        return {
            date: moment().format('YYYY-MM-DD'),
            openMachineDialog: false,
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
                {
                    text: 'Customer',
                    sortable: false
                },
                {
                    text: 'Total usage',
                    sortable: false
                },
                {
                    text: 'Usage for the day',
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
    created() {
        this.tab = 'rw';
    }
}
</script>
