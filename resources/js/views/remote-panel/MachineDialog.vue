<template>
    <v-dialog :value="value" max-width="500" persistent>
        <v-card v-if="machine">
            <v-card-title class="title grey--text">{{machine.machine_name}}</v-card-title>

            <v-divider></v-divider>

            <v-card-text>

                <dl>
                    <dt class="caption grey--text font-weigth-bold">Current user: </dt>
                    <dd class="ml-3">{{machine.user_name}}</dd>
                    <dt class="caption grey--text font-weigth-bold">Remaining time: </dt>
                    <dd class="ml-3">{{remainingTime}}</dd>
                </dl>

                <v-btn round @click="addDry" v-if="!!machine && machine.machine_type[1] == 'd'">
                    <v-icon left>add</v-icon>
                    Add dry</v-btn>

                <!-- <pre>{{machine}}</pre> -->
            </v-card-text>

            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
                <v-spacer></v-spacer>
                <v-btn @click="$emit('rework', machine)" round>Rework</v-btn>
                <v-btn round @click="openTransferDialog = true">Transfer</v-btn>
                <v-btn round @click="forceStop">
                    <v-icon small left>warning</v-icon>
                    End time
                </v-btn>
            </v-card-actions>
        </v-card>
        <force-stop-dialog v-model="openForceStopDialog" :machine="machine" @forceStop="forceStopContinue" />
        <service-browser v-if="machine" v-model="openServiceBrowser" :customer="machine.customer" :machine="machine" :additional="true" @activated="activated" />
        <transfer-dialog v-model="openTransferDialog" :machine="machine" @transfered="transfered"></transfer-dialog>
    </v-dialog>
</template>

<script>
import ForceStopDialog from './ForceStopDialog.vue';
import ServiceBrowser from './ServiceBrowser.vue';
import TransferDialog from './TransferDialog.vue';

export default {
    components: {
        ForceStopDialog,
        ServiceBrowser,
        TransferDialog
    },
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            openForceStopDialog: false,
            openServiceBrowser: false,
            openTransferDialog: false
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        forceStop() {
            this.openForceStopDialog = true;
        },
        forceStopContinue(machine) {
            this.$emit('forceStop', machine);
            this.close();
        },
        addDry() {
            this.openServiceBrowser = true;
        },
        activated(data) {
            this.$emit('activated', data);
            this.close();
        },
        transfered(data) {
            this.$emit('transfered', data);
            this.close();
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        },
        remainingTime() {
            if(!!this.machine) {
                var t = Date.parse(this.machine.time_ends_in) - Date.parse(new Date());
                var hours = Math.floor((t/(1000*60*60)) % 24);
                var minutes = Math.ceil((t/1000/60) % 60);
                return `Ends in ${hours > 0 ? hours + ' hour and' : ''} ${minutes} minutes`;
            }
            return 'keme';
        }
    }
}
</script>
