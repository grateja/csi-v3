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
                    <v-text-field label="Name" v-model="formData.name" :error-messages="errors.get('name')" @keydown.native="clear('name')" outline ref="name"></v-text-field>
                    <v-text-field label="Price" v-model="formData.price" :error-messages="errors.get('price')" @keydown.native="clear('price')" outline></v-text-field>
                    <v-combobox :items="['washer', 'dryer']" label="Service type" :error-messages="errors.get('serviceType')" v-model="formData.serviceType" outline></v-combobox>
                    <v-combobox :items="models" label="Model" :error-messages="errors.get('model')" v-model="formData.model" outline></v-combobox>
                    <v-text-field label="Pulse count" v-model="formData.pulseCount" :error-messages="errors.get('pulseCount')" @keydown.native="clear('pulseCount')" outline></v-text-field>
                    <v-text-field type="number" label="Minutes" v-model="formData.minutes" :error-messages="errors.get('minutes')" outline></v-text-field>
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
        'value', 'service', 'serviceType'
    ],
    data() {
        return {
            mode: 'insert',
            openPictureDialog: false,
            formData: {
                name: null,
                price: 0,
                serviceType: this.$props.serviceType,
                model: null,
                minutes: 0,
                pulseCount: 0
            },
            models: []
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('dryingservice/clearErrors');
        },
        submit() {
            this.$store.dispatch(`eluxservice/${this.mode}Service`, {
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
            this.$store.dispatch('dryingservice/setPicture', {
                serviceId: this.service.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('setPicture', res.data.img_path);
            });
        },
        removePicture() {
            if(confirm('Delete this picture?')) {
                this.$store.dispatch('dryingservice/removePicture', this.service.id).then((res, rej) => {
                    this.$emit('setPicture', '');
                });
            }
        },
        clear(key) {
            this.$store.commit('dryingservice/clearErrors', key);
        },
        loadModels() {
            axios.get(`/api/machines/elux/models/${this.serviceType}`).then((res) => {
                this.models = res.data;
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['dryingservice/getErrors'];
        },
        saving() {
            return this.$store.getters['dryingservice/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.service) {
                this.mode = 'update';
                this.formData.name = this.service.name;
                this.formData.price = this.service.price;
                this.formData.serviceType = this.service.service_type;
                this.formData.model = this.service.model;
                this.formData.minutes = this.service.minutes;
                this.formData.pulseCount = this.service.pulse_count;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.price = 0;
                this.formData.serviceType = this.$props.serviceType;
                this.formData.model = null;
                this.formData.minutes = 0;
                this.formData.pulseCount = 0;
            }
            setTimeout(() => {
                this.$refs.name.$el.querySelector('input').select();
            }, 500);
            this.loadModels()
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
