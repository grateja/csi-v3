<template>
<div>
    <v-layout>
        <v-flex offset-md3 md9>
            <!-- <v-expand-transition>
                <div v-if="showDetails">
                    <event-form class="my-3" v-model="event" @submit="submit"></event-form>
                </div>
            </v-expand-transition> -->
        </v-flex>
    </v-layout>
    <v-layout row wrap class="my-4">
        <v-flex xs12>
            <v-tabs v-model="activeTab" color="blue" active-class="white">
                <v-tab><v-icon left>perm_media</v-icon> Slide show</v-tab>
                <v-tab><v-icon left>video_library</v-icon> Video</v-tab>
                <v-tab><v-icon left>volume_mute</v-icon> Audio</v-tab>
                <!-- <v-tab><v-icon left>subscriptions</v-icon> Youtube link</v-tab> -->

                <v-tab-item>
                    <v-card class="pa-3">
                        <v-layout row wrap>
                            <v-flex md2 lg3>
                                <div class="text-xs-center">
                                    <v-btn class="primary" @click="addSlide">
                                        <v-icon left>add</v-icon> add new slide
                                    </v-btn>
                                </div>
                                <div v-if="event.slides" class="slides">
                                    <v-card v-for="slide in event.slides" :key="slide.id" class="pa-2" @click="selectedSlide = slide.order - 1">
                                        <v-responsive :aspect-ratio="16/9">
                                            <v-img :src="slide.source"></v-img>
                                        </v-responsive>
                                        <v-card-actions>
                                            <v-tooltip top>
                                                <v-btn @click="changedOrder($event, slide)" icon slot="activator">
                                                    <v-icon>swap_vert</v-icon>
                                                </v-btn>
                                                <span>Change Order</span>
                                            </v-tooltip>
                                            <v-tooltip top>
                                                <v-btn icon slot="activator" @click="changePicture($event, slide)">
                                                    <v-icon>edit</v-icon>
                                                </v-btn>
                                                <span>Change Picture</span>
                                            </v-tooltip>
                                            <v-spacer></v-spacer>
                                            <v-tooltip top>
                                                <v-btn slot="activator" @click="removeSlide($event, slide)" icon :loading="slide.isDeleting">
                                                    <v-icon>delete</v-icon>
                                                </v-btn>
                                                <span>Remove Slide</span>
                                            </v-tooltip>
                                        </v-card-actions>
                                    </v-card>
                                </div>
                            </v-flex>
                            <v-flex sm12 md10 lg9>
                                <v-card class="mx-2">
                                    <v-responsive :aspect-ratio="16/9">
                                        <slide-show :images="event.slides" :selected-slide="selectedSlide" :cycle="cycle" />
                                    </v-responsive>
                                    <v-card-actions>
                                        <v-btn fab flat @click="cycle = !cycle">
                                            <v-icon large>{{cycle ? 'pause_circle_outline' : 'play_circle_outline'}}</v-icon>
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-card>
                </v-tab-item>
                <v-tab-item>
                    <video-panel :event="event" @updateVideo="updateVideo"></video-panel>
                </v-tab-item>
                <v-tab-item>
                    <v-card>
                        <h3>For Audio</h3>
                        <v-card-text>

                            <template v-if="event.audio">
                                <audio ref="audio" controls>
                                    <source :src="event.audio.source" />
                                </audio>
                            </template>

                            <v-btn @click="openAudioDialog = true">Select audio</v-btn>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item>
                    <h3>For youtube</h3>
                </v-tab-item>
            </v-tabs>
        </v-flex>
    </v-layout>

    <event-dialog v-model="showDetails" :event="event"></event-dialog>
    <change-order-dialog v-model="openChangeOrder" :slide="activeSlide" @ok="changeOrderContinue"></change-order-dialog>
    <!-- <change-picture-dialog v-model="openChangePicture" :slide="activeSlide" @ok="changePictureContinue" /> -->
    <slide-dialog v-model="openSlideDialog" :slide="activeSlide" @ok="selectPictures" />
    <picture-browser v-if="activeSlide" v-model="openChangePicture" :url="activeSlide.source" @ok="changePictureContinue" />
    <audio-dialog v-model="openAudioDialog" :eventId="event.id" />
</div>

        <!-- <v-flex sm12 md2 lg3>
            <div class="text-xs-center">
                <v-btn class="primary">
                    <v-icon left>add</v-icon> add new slide
                </v-btn>
            </div>
            <div v-if="event.slides" class="slides">
                <v-card v-for="slide in event.slides" :key="slide.id" class="pa-2" @click="selectedSlide = slide.order - 1">
                    <v-responsive>
                        <v-img :src="slide.source"></v-img>
                    </v-responsive>
                </v-card>
            </div>
        </v-flex>
        <v-flex sm12 md10 lg9>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-btn><v-icon left>slideshow</v-icon> slide show</v-btn>
                    <v-btn><v-icon left>videocam</v-icon> video</v-btn>
                    <v-btn><v-icon left>text_fields</v-icon> text</v-btn>
                    <v-btn><v-icon left>link</v-icon> youtube url</v-btn>
                </v-flex>
                <v-flex xs12>
                    <v-card class="mx-2">
                        <v-responsive :aspect-ratio="16/9">
                            <slide-show :images="event.slides" :selected-slide="selectedSlide" :cycle="cycle" />
                        </v-responsive>
                        <v-card-actions>
                            <v-btn fab flat @click="cycle = !cycle">
                                <v-icon large>{{cycle ? 'pause_circle_outline' : 'play_circle_outline'}}</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                    <event-form class="mx-2" @submit="submit" v-model="event"></event-form>
                </v-flex>
            </v-layout>
        </v-flex> -->
</template>
<script>
// import EventForm from './EventForm.vue';
import SlideShow from '../../shared/SlideShow.vue';
import EventDialog from './EventDialog.vue';
import ChangeOrderDialog from './slides/ChangeOrderDialog.vue';
import ChangePictureDialog from './slides/ChangePictureDialog.vue';
import SlideDialog from './slides/SlideDialog.vue';
import AudioDialog from './audio/AudioDialog.vue';
import VideoPanel from './videos/VideoPanel.vue';
import PictureBrowser from '../../shared/PictureBrowser.vue';

export default {
    components: {
        // EventForm,
        SlideShow,
        EventDialog,
        ChangeOrderDialog,
        ChangePictureDialog,
        SlideDialog,
        AudioDialog,
        VideoPanel,
        PictureBrowser
    },
    data() {
        return {
            event: {},
            selectedSlide: 0,
            cycle: false,
            activeTab: 0,
            showDetails: false,
            openChangeOrder: false,
            openSlideDialog: false,
            openChangePicture: false,
            openAudioDialog: false,
            activeSlide: null
        }
    },
    methods: {
        // submit(data) {
        //     this.$store.dispatch(`event/${data.mode}Event`, data.request).then((res, rej) => {
        //         this.$router.push('/events/' + res.data.id);
        //         this.event = res.data;
        //     });
        // },
        getEvent() {
            if(this.$route.params.id) {
                axios.get(`/api/events/${this.$route.params.id}`).then((res, rej) => {
                    this.event = res.data.event;
                    this.activeTab = res.data.event.event_type_id - 1;
                    console.log(this.event);
                }).catch(err => {
                    if(err.response.status == 404) {
                        alert('Event doesn`t exist');
                        window.location = '/events';
                    }
                    console.log(err.response.status);
                    // if(err.response.statusCode)
                });
            }
        },
        changedOrder(event, slide) {
            event.stopPropagation();
            this.activeSlide = slide;
            this.openChangeOrder = true;
        },
        changeOrderContinue(data) {
            this.event.slides.find(slide => slide.id == data.slide.id).order = data.newOrder;
        },
        changePicture(e, slide) {
            e.stopPropagation();
            this.activeSlide = slide;
            this.openChangePicture = true;
        },
        changePictureContinue(formData) {
            this.$store.dispatch('file/changePicture', {
                formData,
                slideId: this.activeSlide.id
            }).then((res, rej) => {
                console.log(res)
            })
        },
        addSlide() {
            this.activeSlide = null;
            this.openSlideDialog = true;
        },
        selectPictures(formData) {
            this.$store.dispatch('file/uploadSlides', {
                formData,
                eventId: this.event.id
            }).then((res, rej) => {
                // console.log(res);
                this.event = res.data.event;
            })
            console.log('formData', formData);
        },
        removeSlide(event, slide) {
            event.stopPropagation();
            if(confirm('Remove this slide?')) {
                Vue.set(slide, 'isDeleting', true);
                this.$store.dispatch('event/removeSlide', slide.id).then((res, rej) => {
                    this.event.slides = this.event.slides.filter(s => s.id != slide.id);
                }).finally(() => {
                    Vue.set(slide, 'isDeleting', false);
                });
            }
        },
        updateVideo(video) {
            this.event.video = video;
        }
    },
    created() {
        this.getEvent();
    }
}
</script>

<style scoped>
.slides {
    max-height: 60vh;
    overflow-y: scroll;
}
</style>
