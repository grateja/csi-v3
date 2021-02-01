<template>
    <v-flex class="machine-tile__flex">
        <!-- <div class="text-xs-center white--text">{{machine.machine_name}}
        </div> !-->
        <v-hover v-slot:default="{ hover }" v-if="machine">
            <v-card :elevation="hover ? 12 : 2" class="ma-1 pa-3 pointer rounded-card translucent machine-tile__card" @click="open(machine)" :class="{'running': machine.is_running, 'selected': machine.selected}" >
                <!-- <div class="text-xs-center">{{machine.machine_name}}</div>
                <v-divider></v-divider> -->

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <!-- <v-img :src="`/img/dos-icons/${machine.machine_type[1]}.png`" width="50%" centered></v-img> -->
                    <span class="display-1 font-weight-bold machine-tile-name">{{machine.machine_name}}</span>
                    <v-spacer></v-spacer>
                </v-card-actions>
                <div class="text-xs-center">{{machine.user_name}}</div>
                <div class="text-xs-center" v-if="machine.is_running" :key="timeKey">{{remainingTime()}}</div>
                <!-- <div class="text-xs-center font-italic caption grey--text" v-else>Used {{moment(machine.time_ends_in).fromNow()}}</div> -->

                <!-- <div v-if="machine.is_running" class="text-xs-center">
                    <div class="caption">Current user:</div>
                    <span class="font-italic caption" :key="timeKey">{{remainingTime()}}</span>
                    <div v-if="!!machine.remarks" class="text-xs-center caption">
                        <v-divider></v-divider>
                        {{machine.remarks}}
                    </div>
                </div>
                <div v-else class="text-xs-center">
                    <div class="caption grey--text">Last user:</div>
                    <div>{{machine.user_name}}</div>
                    <span class="font-italic grey--text caption" :key="timeKey">Ends {{moment(machine.time_ends_in).fromNow()}}</span>
                    <div v-if="!!machine.remarks" class="text-xs-center caption grey--text">
                        <v-divider></v-divider>
                        {{machine.remarks}}
                    </div>
                </div> -->
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
                // var hours = Math.floor((t/(1000*60*60)) % 24);
                // var minutes = Math.ceil((t/1000/60) % 60);
                var min = t/1000/60;
                if(min <= 0) {
                    this.machine.is_running = false;
                } else if(min == 1) {
                    return '1 min left';
                } else {
                    return `${Math.ceil(min)} mins. left`;
                }
            }
            return 'keme';
        },
        progress() {
            if(!!this.machine) {
                var t = Date.parse(this.machine.time_ends_in) - Date.parse(new Date());
                var minutes = (t/1000/60);
                return Math.round(minutes / parseInt(this.machine.total_minutes) * 100);
            }
        }
    },
    computed: {
        // progress() {
        //     if(!!this.machine) {
        //         var t = Date.parse(this.machine.time_ends_in) - Date.parse(new Date());
        //         var minutes = (t/1000/60);
        //         return Math.round(minutes / parseInt(this.machine.total_minutes) * 100);
        //     }
        // }
    },
    created() {
        this.timer = setInterval(this.refreshTime, 1000);
    },
    beforeDestroy() {
        this.cancelUpdate();
    }
}
</script>
