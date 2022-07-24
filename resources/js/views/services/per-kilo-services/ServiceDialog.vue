<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Service details</v-card-title>

                <v-responsive v-if="service && service.img_path" :aspect-ratio="16/9" max-height="300">
                    <v-img :src="service.img_path"></v-img>
                </v-responsive>
                <v-card-actions v-if="mode == 'update'">
                    <v-btn @click="openPictureDialog = true" class="ml-0" round>select picture</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="removePicture" v-if="!!service && service.img_path"> <v-icon>delete</v-icon></v-btn>
                </v-card-actions>

                <v-card-text>
                    <v-text-field label="Name" v-model="formData.name" :error-messages="errors.get('name')" outline ref="name"></v-text-field>
                    <v-text-field label="Delicate Price" v-model="formData.delicatePrice" :error-messages="errors.get('delicatePrice')" outline></v-text-field>
                    <v-text-field label="Warm Price" v-model="formData.warmPrice" :error-messages="errors.get('warmPrice')" outline></v-text-field>
                    <v-text-field label="Hot Price" v-model="formData.hotPrice" :error-messages="errors.get('hotPrice')" outline></v-text-field>
                    <v-text-field label="Superwash Price" v-model="formData.superwashPrice" :error-messages="errors.get('superwashPrice')" outline></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" round :loading="saving" type="submit">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <picture-dialog v-if="service" :url="service.img_path" v-model="openPictureDialog" @ok="savePicture" />
    </v-dialog>
</template>
<script>
import PictureDialog from '../../shared/PictureBrowser.vue';
export default {
    components: {
        PictureDialog
    },
    props: [
        'value', 'service'
    ],
    data() {
        return {
            mode: 'insert',
            openPictureDialog: false,
            formData: {
                name: null,
                delicatePrice: 0,
                warmPrice: 0,
                hotPrice: 0,
                superwashPrice: 0,
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('perkiloservices/clearErrors');
        },
        submit() {
            this.$store.dispatch(`perkiloservices/${this.mode}Service`, {
                serviceId: this.service ? this.service.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    service: res.data.service
                });
                // this.close();
            });
        },
        savePicture(formData) {
            this.$store.dispatch('perkiloservices/setPicture', {
                serviceId: this.service.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('setPicture', res.data.img_path);
            });
        },
        removePicture() {
            if(confirm('Delete this picture?')) {
                this.$store.dispatch('perkiloservices/removePicture', this.service.id).then((res, rej) => {
                    this.$emit('setPicture', '');
                });
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['perkiloservices/getErrors'];
        },
        saving() {
            return this.$store.getters['perkiloservices/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.service) {
                this.mode = 'update';
                this.formData.name = this.service.name;
                this.formData.delicatePrice = this.service.delicatePrice;
                this.formData.warmPrice = this.service.warmPrice;
                this.formData.hotPrice = this.service.hotPrice;
                this.formData.superwashPrice = this.service.superwashPrice;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.delicatePrice = "";
                this.formData.warmPrice = "";
                this.formData.hotPrice = "";
                this.formData.superwashPrice = "";
            }
            setTimeout(() => {
                this.$refs.name.$el.querySelector('input').select();
            }, 500);
        },
        service(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
