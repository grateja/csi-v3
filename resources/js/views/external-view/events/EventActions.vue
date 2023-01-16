<template>
    <v-card-actions>
        <v-btn icon flat small>
            <v-icon>edit</v-icon>
        </v-btn>
        <v-btn icon flat small color="red">
            <v-icon>delete_outline</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-tooltip top>
            <v-btn icon slot="activator" class="mx-0" @click="slideClick($event)">
                <v-icon :color="event.event_type_id == 1 ? 'green' : 'grey lighten-2'">perm_media</v-icon>
            </v-btn>
            <span>{{event.slides && event.slides.length ? 'Edit slides' : 'Add slides'}}</span>
        </v-tooltip>

        <v-tooltip top>
            <v-btn icon slot="activator" class="mx-0" @click="videoClick($event)">
                <v-icon color="grey lighten-2">video_library</v-icon>
            </v-btn>
            <span>{{event.slides && event.slides.length ? 'Edit video' : 'Add video'}}</span>
        </v-tooltip>

        <v-tooltip top>
            <v-btn icon slot="activator" class="mx-0" @click="textClick($event)">
                <v-icon color="grey lighten-2">title</v-icon>
            </v-btn>
            <span>{{event.slides && event.slides.length ? 'Edit text' : 'Add text'}}</span>
        </v-tooltip>

        <v-tooltip top>
            <v-btn icon slot="activator" class="mx-0" @click="youtubeClick($event)">
                <v-icon color="grey lighten-2">subscriptions</v-icon>
            </v-btn>
            <span>{{event.slides && event.slides.length ? 'Edit YouTube URL' : 'Add YouTube URL'}}</span>
        </v-tooltip>

    </v-card-actions>
</template>


<script>
export default {
    props: [
        'event'
    ],
    methods: {
        slideClick($e) {
            $e.stopPropagation();
            if(this.event.event_type_id == 1) {
                // edit or add
                if(this.event.slides) {
                    // edit slides
                } else {
                    // add slides
                }
            } else {
                // activate this event type
            }
        },
        videoClick($e) {
            $e.stopPropagation();
        },
        textClick($e) {
            $e.stopPropagation();
        },
        youtubeClick($e) {
            $e.stopPropagation();
        },
        activateEvent(eventTypeId) {
            axios.post(`/api/events/${this.event.id}/change-event-type`, {
                eventTypeId: eventTypeId
            }).then((res, rej) => {

            });
        }
    }
}
</script>

<style>
</style>
