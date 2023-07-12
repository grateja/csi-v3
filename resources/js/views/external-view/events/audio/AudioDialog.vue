<template>
    <v-dialog v-model="value" persistent max-width="400px">
        <v-card>
            <v-card-title>
                Audio
            </v-card-title>
            <v-card-text>
                <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setAudio" accept="audio/*">
                <v-btn @click="browseAudio" class="primary"><v-icon left>volume_mute</v-icon> {{source ? 'change audio' : 'select audio'}}</v-btn>

                <template v-if="source != null">
                    <audio ref="audio" controls>
                        <source :src="source.url" />
                    </audio>
                    <pre>{{ source.size }}</pre>
                </template>

            </v-card-text>
            <v-card-actions>
                <v-btn @click="save">save</v-btn>
                <v-btn @click="close">close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import FileSizeHelper from '../../../../helpers/FileSizeHelper.js';
export default {
    props: [
        'value',
        'eventId'
    ],
    data() {
        return {
            source: null
        }
    },
    methods: {
        browseAudio() {
            this.$refs.inputFile.click();
        },
        setAudio(e) {
            console.log(e.target.files.length)
            if(e.target.files.length) {
                let audio = e.target.files[0]
                this.source = {
                    file: audio,
                    url: URL.createObjectURL(audio),
                    size: FileSizeHelper.humanFileSize(audio.size)
                }
                if(this.$refs.audio != null) {
                    this.$refs.audio.src = this.source.url
                }
                // Vue.set(this.source, "url", URL.createObjectURL(audio))
                // this.files = e.target.files;
                // this.sources = [...e.target.files].map((file, index) => {
                //     return {
                //         file,
                //         url: URL.createObjectURL(file),
                //         order: index + 1,
                //         size: FileSizeHelper.humanFileSize(file.size)
                //     }
                // });
            }
        },
        save() {
            let formData = new FormData()
            formData.append("file", this.source.file)
            this.$store.dispatch(`file/uploadAudio`, {
                eventId: this.eventId,
                formData
            }).then((res, rej) => {
                this.close();
                this.$emit('ok', {
                    slide: res.data.slide
                });
            });
        },
        close() {
            this.$emit('input', false);
        }
    }
}
</script>
