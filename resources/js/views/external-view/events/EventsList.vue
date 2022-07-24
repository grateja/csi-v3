<template>
    <div>
        <v-layout row wrap class="my-4" align-baseline>
            <v-flex sm6 lg4>
                <v-card class="primary ma-1 text-xs-center">
                    <v-btn @click="createEvent">
                    <v-icon left>add</v-icon>
                    create new event</v-btn>
                </v-card>
            </v-flex>
            <v-flex sm6 lg4 v-for="event in events" :key="event.id">
                <v-card @click="view(event)" class="event ma-1">
                    <v-card-actions>
                        <v-card-title class="title pa-0 ma-0">{{event.title}}
                        </v-card-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="event.event_type">{{event.event_type.icon}}</v-icon>
                    </v-card-actions>
                    <v-responsive :aspect-ratio="16/9">
                        <v-img v-if="event.slides && event.slides[0]" :src="event.slides[0].source"></v-img>
                    </v-responsive>

                    <v-card-text>
                        <div>{{moment(event.date)}}</div>
                        <div class="caption grey--text text-no-wrap text-truncate">{{event.description}}</div>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn icon @click="editEvent($event, event)">
                            <v-icon>edit</v-icon>
                        </v-btn>
                        <v-btn icon @click="deleteEvent($event, event)">
                            <v-icon>delete_outline</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
        <event-dialog v-model="openEvent" :event="activeEvent"></event-dialog>
    </div>
</template>

<script>
import moment from 'moment';
import EventActions from './EventActions.vue';
import EventDialog from './EventDialog.vue';
export default {
    components: {
        EventActions,
        EventDialog
    },
    data() {
        return {
            events: [],
            keyword: '',
            page: 1,
            openEvent: false,
            activeEvent: null
        }
    },
    methods: {
        getAll() {
            axios.get('/api/events', {
                keyword: this.keyword,
                page: this.page
            }).then((res, rej) => {
                this.events = res.data.data;
                console.log(res);
            });
        },
        moment(date) {
            return moment(date).format('MMMM D');
        },
        view(event) {
            this.$router.push(`/external-view/events/${event.id}`);
        },
        createEvent() {
            this.activeEvent = null;
            this.openEvent = true;
        },
        editEvent($e, event) {
            $e.stopPropagation();
            this.activeEvent = event;
            this.openEvent = true;
        },
        deleteEvent($e, event) {
            $e.stopPropagation();
            if(confirm('Are you sure you want to delete this event?')) {
                this.$store.dispatch('event/deleteEvent', event.id).then((res, rej) => {
                    this.events = this.events.filter(e => e.id != res.data.eventId);
                });
            }
        }
        // slideClick($e, event) {
        //     $e.stopPropagation();
        //     console.log(event.event_type);
        // },
        // videoClick($e, event) {
        //     $e.stopPropagation();
        // },
        // textClick($e, event) {
        //     $e.stopPropagation();
        // },
        // youtubeClick($e, event) {
        //     $e.stopPropagation();
        // }
    },
    mounted() {
        this.getAll();
    }
}
</script>

<style scoped>
.event {
    cursor: pointer;
}
</style>
