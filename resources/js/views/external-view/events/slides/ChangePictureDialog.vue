<template>
    <v-dialog persistent max-width="480px" :value="value">
        <v-card v-if="!!slide">
            <v-card-title class="title">Change order</v-card-title>
            <v-responsive>
                <!-- <pre v-if="source">{{source.url}}</pre> -->
                <v-img :src="source ? source.url : slide.source"></v-img>
            </v-responsive>
            <v-card-text class="text-xs-center">
                <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setPicture" accept="image/*">
                <v-btn @click="browsePicture" class="primary"><v-icon left>photo</v-icon> {{source ? 'change picture' : 'selecte picture'}}</v-btn>
            </v-card-text>
            <!-- <v-card-text>
                <p>Change order from {{slide.order}} to:</p>
                <v-text-field type="number" v-model="order"></v-text-field>
            </v-card-text> -->
            <v-card-actions>
                <v-btn @click="ok">Ok</v-btn>
                <v-btn @click="$emit('input', false)">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import FileSizeHelper from '../../../../helpers/FileSizeHelper.js';

export default {
    props: [
        'value',
        'slide'
    ],
    data() {
        return {
            source: null
        }
    },
    methods: {
        browsePicture() {
            this.$refs.inputFile.click();
        },
        setPicture(e) {
            if(e.target.files.length) {
                let img = e.target.files[0]
                console.log('img', img)
                this.source = {
                    file: img.file,
                    url: URL.createObjectURL(img),
                    size: FileSizeHelper.humanFileSize(img.size)
                }
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
        ok() {
            this.$store.dispatch(`file/changePicture`, {
                slideId: this.slide.id,
                source: this.source
            }).then((res, rej) => {
                this.close();
                this.$emit('ok', {
                    slide: res.data.slide
                });
            });
            // this.$emit('ok', {
            //     source: this.source,
            //     slide: this.slide
            // });
        },
        close() {
            this.$emit('input', false);
        }
    },
    watch: {
        slide(val) {
            if(val) {
                this.order = val.order;
            } else {
                this.order = 0;
            }
        }
    }
}
</script>
