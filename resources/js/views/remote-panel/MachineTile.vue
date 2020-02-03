<template>
    <v-flex xs4 sm3 md2 xl1>
        <v-hover v-slot:default="{ hover }" v-if="machine">
            <v-card :elevation="hover ? 12 : 2" class="ma-1 pointer" @click="open(machine)" :color="machine.is_running ? 'blue' : 'white'" :dark="machine.is_running" >
                <div class="text-xs-center">{{machine.machine_name}}</div>
                <v-divider></v-divider>

                <v-card-text>
                    <v-responsive>
                        <pre>[This is a box]</pre>
                    </v-responsive>
                </v-card-text>

                <div v-if="machine.is_running" class="text-xs-center">
                    <div class="caption">Current customer:</div>
                    <div v-if="!!machine.customer">{{machine.customer.name}}</div>
                    <span class="font-italic caption" :key="timeKey">{{remainingTime()}}</span>
                    <div v-if="!!machine.remarks" class="text-xs-center caption">
                        <v-divider></v-divider>
                        {{machine.remarks}}
                    </div>
                </div>
                <div v-else class="text-xs-center">
                    <div class="caption grey--text">Last customer:</div>
                    <div v-if="!!machine.customer">{{machine.customer.name}}</div>
                    <span class="font-italic grey--text caption" :key="timeKey">Ends {{moment(machine.time_ends_in).fromNow()}}</span>
                    <div v-if="!!machine.remarks" class="text-xs-center caption grey--text">
                        <v-divider></v-divider>
                        {{machine.remarks}}
                    </div>
                </div>
            </v-card>
        </v-hover>
    </v-flex>
</template>

<script>
export default {
    props: [
        'machine'
    ],
    data() {
        return {
            timeKey: 0,
            timer: null
        }
    },
    methods: {
        open(machine) {
            this.$emit('open', machine);
        },
        refreshTime() {
            this.timeKey += 1;
        },
        cancelUpdate() {
            clearInterval(this.timer);
        },
        remainingTime() {
            if(!!this.machine) {
                var t = Date.parse(this.machine.time_ends_in) - Date.parse(new Date());
                var hours = Math.floor((t/(1000*60*60)) % 24);
                var minutes = Math.ceil((t/1000/60) % 60);
                if(minutes <= 0) {
                    this.machine.is_running = false;
                }
                return `Ends in ${hours > 0 ? hours + ' hour and' : ''} ${minutes} minutes`;
            }
            return 'keme';
        }
    },
    created() {
        this.timer = setInterval(this.refreshTime, 1000)
    },
    beforeDestroy() {
        this.cancelUpdate();
    }
}
</script>
