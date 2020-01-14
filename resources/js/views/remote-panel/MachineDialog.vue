<template>
    <v-dialog :value="value" max-width="500" persistent>
        <v-card v-if="machine">
            <v-card-title class="title grey--text">{{machine.machine_name}}</v-card-title>

            <v-divider></v-divider>

            <v-card-text>

                <dl>
                    <dt class="caption grey--text font-weigth-bold">Customer name: </dt>
                    <dd class="ml-3">{{machine.customer_name}}</dd>
                    <dt class="caption grey--text font-weigth-bold">Remaining time: </dt>
                    <dd class="ml-3">{{remainingTime}}</dd>
                </dl>

                <pre>{{machine}}</pre>
            </v-card-text>

            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
                <v-spacer></v-spacer>
                <v-btn round @click="forceStop">
                    <v-icon small left>warning</v-icon>
                    force stop
                </v-btn>
            </v-card-actions>
        </v-card>
        <force-stop-dialog v-model="openForceStopDialog" :machine="machine" @forceStop="forceStopContinue" />
    </v-dialog>
</template>

<script>
import ForceStopDialog from './ForceStopDialog.vue';

export default {
    components: {
        ForceStopDialog
    },
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            openForceStopDialog: false
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
        }
    },
    computed: {
        remainingTime() {
            if(!!this.machine) {
                var t = Date.parse(this.machine.time_ends_in) - Date.parse(new Date());
                var hours = Math.floor((t/(1000*60*60)) % 24);
                var minutes = Math.floor((t/1000/60) % 60);
                return `Ends in ${hours > 0 ? hours + ' hour and' : ''} ${minutes} minutes`;
            }
            return 'keme';
        }
    }
}
</script>
