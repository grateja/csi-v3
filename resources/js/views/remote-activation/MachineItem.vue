<template>
    <v-card v-if="!!machine" @click="activateDryer" :class="className">
        <v-card-title class="title">{{machine.name}}</v-card-title>

        <v-card-text>
            <p v-if="machine.is_running && machine.customer">{{machine.customer.name}}</p>
            <span class="caption">
                {{endsIn}}
            </span>
            <div class="block caption">{{machine.usage_count}} time(s) used today</div>
        </v-card-text>
    </v-card>
</template>

<script>
import moment from 'moment';
export default {
    components: {
        moment
    },
    props: [
        'machine'
    ],
    data() {
        return {
            endsIn: '...',
            className: '',
            interval: null,
        }
    },
    methods: {
        activateDryer() {
            this.$emit('activate', this.machine);
        },
        updateTime() {
            if(this.isRunning) {
                // console.log('running');
                this.endsIn = 'Ends ' + moment(this.machine.time_ends_in).fromNow();
                this.className = 'blue white--text';
            } else {
                this.endsIn = 'Last activated ' + moment(this.machine.time_ends_in).fromNow();
                this.className = 'white black--text';
                // console.log('not running');
            }
        }
    },
    computed: {
        isRunning() {
            return moment(this.machine.time_activated).add(this.machine.total_minutes, 'minutes') > moment();
        }
    },
    created() {
        this.updateTime();
        this.interval = setInterval(this.updateTime, 5000);
    },
    destroyed() {
        clearInterval(this.interval);
    }
}
</script>
