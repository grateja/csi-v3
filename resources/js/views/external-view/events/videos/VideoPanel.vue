<template>
    <div>
        <v-layout>
            <v-flex>
                <v-card>
                    <span v-if="uploading">{{humanFileSize(getUploadProgress.loaded)}}</span>
                    <v-progress-linear v-if="uploading" :value="getUploadProgress.percent"></v-progress-linear>
                    <video v-if="source" :src="source" controls id="video"></video>
                    <v-card-text class="text-xs=center">
                        <v-btn @click="deleteVideo" v-if="event.video">Delete</v-btn>
                        <v-btn @click="browseVideo" v-else>Add Video</v-btn>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <video-dialog v-model="openBrowseVideo" @ok="saveVideo"></video-dialog>
    </div>
</template>
<script>
import VideoDialog from './VideoDialog.vue';
export default {
    components: {
        VideoDialog
    },
    props: [
        'event'
    ],
    data() {
        return {
            openBrowseVideo: false
        }
    },
    methods: {
        browseVideo() {
            this.openBrowseVideo = true;
        },
        saveVideo(formData) {
            this.$store.dispatch('file/uploadVideo', {
                formData,
                eventId: this.event.id
            }).then((res, rej) => {
                this.$emit('updateVideo', res.data.video);
                console.log(res.data);
            });
        },
        deleteVideo() {
            if(confirm('Are you sure you want remove this video?')) {
                this.$store.dispatch('video/deleteVideo', this.event.video.id).then((res, rej) => {
                    this.$emit('updateVideo', null);
                });
            }
        },
        humanFileSize(bytes, si=false, dp=1) {
            const thresh = si ? 1000 : 1024;

            if (Math.abs(bytes) < thresh) {
                return bytes + ' B';
            }

            const units = si
                ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
                : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
            let u = -1;
            const r = 10**dp;

            do {
                bytes /= thresh;
                ++u;
            } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);

            if(bytes) return bytes.toFixed(dp) + ' ' + units[u];
            return '';
        }
    },
    computed: {
        source() {
            if(this.event && this.event.video) {
                return this.event.video.source;
            }
        },
        getUploadProgress() {
            return this.$store.getters['file/getVidUploadProgress'];
        },
        uploading() {
            return this.$store.getters['file/getUploadingState'];
        }
    }
}
</script>

<style scoped>
#video {
    width: 100%;
}
</style>
