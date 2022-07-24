<template>
    <v-dialog persistent :value="value" max-width="640px">
        <v-card>
            <v-card-title class="title">Slide detail</v-card-title>

            <div id="kme">
                <template v-for="(source, i) in sources">
                    <v-layout row wrap  :key="i">
                        <v-flex xs4>
                            <v-responsive :aspect-ratio="16/9" v-if="source">
                                <v-img :src="source.url"></v-img>
                            </v-responsive>
                        </v-flex>
                        <v-flex xs8>
                            <v-text-field label="Order" type="number" v-model="source.order"></v-text-field>
                            <div class="caption">size: {{source.size}}</div>
                            <v-btn @click="removeSource(source)">remove</v-btn>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="`div-${i}`"></v-divider>
                </template>
            </div>

            <v-card-text class="text-xs-center">
                <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setPicture" accept="image/*" multiple>
                <v-btn @click="browsePicture" class="primary"><v-icon left>photo</v-icon> {{sources.length ? 'change picture' : 'selecte picture'}}</v-btn>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="ok">ok</v-btn>
                <v-btn @click="$emit('input', false)">close</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
// import FileSizeHelper from  '../../helpers/FileSizeHelper.js';
import FileSizeHelper from '../../../../helpers/FileSizeHelper.js';

export default {
    // components: {
    //     FileSizeHelper
    // },
    props: [
        'value',
        'slide',
        'lastOrder'
    ],
    data() {
        return {
            title: '',
            description: '',
            order: '',
            sources: []
        }
    },
    methods: {
        ok() {
            let formData = new FormData();

            for (let index = 0; index < this.sources.length; index++) {
                const element = this.sources[index].file;
                formData.append('files['+index+']', element);
                formData.append('order['+index+']', this.sources[index].order);
                console.log('element', element);
            }

            // axios.post('/api/files/upload-slides',
            //     formData,
            //     {
            //         headers: {
            //         'Content-Type': 'multipart/form-data'
            //     }
            // }).then((res, rej) => {
            //     console.log(res.data);
            // });
            // return ;
            this.$emit('ok', formData);
            this.$emit('input', false);
        },
        browsePicture() {
            this.$refs.inputFile.click();
        },
        setPicture(e) {
            if(e.target.files.length) {
                // this.files = e.target.files;
                this.sources = [...e.target.files].map((file, index) => {
                    return {
                        file,
                        url: URL.createObjectURL(file),
                        order: index + 1,
                        size: FileSizeHelper.humanFileSize(file.size)
                    }
                });
            }
        },
        removeSource(source) {
            this.sources = this.sources.filter(s => s.file != source.file);
        }
    },
    computed: {
        imageSources() {
            let i = 0;
            return [...this.files].map(file => {
                return {
                    url: URL.createObjectURL(file),
                    file,
                    order: i++,
                    size: file.size
                }
            });
        }
    },
    watch: {
        slide(val) {
            if(val) {
                this.title = val.title;
                this.description = val.description;
                this.order = val.order;
                this.source = val.source;
            } else {
                this.title = '';
                this.description = '';
                this.order = this.lastOrder;
                this.source = '';
            }
        }
    }
}
</script>

<style scoped>
#inputFile {
    display: none;
}

#kme {
    max-height: 70vh;
    overflow-y: auto;
}
</style>
