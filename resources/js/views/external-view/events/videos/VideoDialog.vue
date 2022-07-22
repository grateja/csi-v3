<template>
    <v-dialog :value="value" max-width="640" persistent>
        <v-card>
            <v-card-title class="title">
                Video details
            </v-card-title>

            <video :src="videoUrl" v-if="videoUrl" id="video" controls></video>

            <v-card-text>
                <div class="text-xs-center">
                    <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setVideo" accept="video/*">
                    <v-btn @click="browsePicture" class="primary"><v-icon left>photo</v-icon> Select video</v-btn>
                </div>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="ok">
                    ok
                </v-btn>
                <v-btn @click="close">
                    close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value'
    ],
    data() {
        return {
            source: null,
            formData: new FormData()
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.source = null;
            // this.source = null;
        },
        browsePicture() {
            this.$refs.inputFile.click();
        },
        setVideo(e) {
            if(e.target.files.length) {
                this.source = e.target.files[0];
            }
        },
        ok() {
            this.formData.append('file', this.source);
            this.$emit('ok', this.formData);
            this.close();
        }
    },
    computed: {
        videoUrl() {
            if(this.source) {
                return URL.createObjectURL(this.source);
            }
        }
    }
}
</script>

<style scoped>
#inputFile {
    display: none;
}

#video {
    width: 100%;
}
</style>
