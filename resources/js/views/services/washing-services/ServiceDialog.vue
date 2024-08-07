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
                    <v-text-field label="Name" v-model="formData.name" :error-messages="errors.get('name')" outline ref="name" @input="nameDirty = true"></v-text-field>
                    <v-text-field label="Description" v-model="formData.description" outline></v-text-field>
                    <v-text-field label="Price" v-model="formData.price" :error-messages="errors.get('price')" outline></v-text-field>
                    <v-combobox :items="['REGULAR', 'TITAN']" label="Machine type" :error-messages="errors.get('machineType')" v-model="formData.machineType" outline></v-combobox>
                    <template>
                        <v-radio-group v-model="formData.serviceType">
                            <v-radio label="Delicate Wash" value="quick" v-if="method == 'nsoft'"></v-radio>
                            <v-radio label="Warm Wash" value="regular"></v-radio>
                            <v-radio label="Cold Wash" value="more_rinse" v-if="method == 'nsoft'"></v-radio>
                            <v-radio label="Hot Wash" value="premium" v-if="method == 'nsoft'"></v-radio>
                            <v-radio label="Super Wash" value="additional"></v-radio>
                        </v-radio-group>
                    </template>
                    <v-text-field type="number" label="Regular minutes" v-model="formData.regularMinutes" :error-messages="errors.get('regularMinutes')" outline></v-text-field>
                    <!-- <v-expand-transition>
                        <v-card flat v-if="formData.serviceType == 'additional'">
                            <v-text-field type="number" label="Additional minutes" v-model="formData.additionalMinutes" :error-messages="errors.get('additionalMinutes')" outline hint="For add super wash. Set to 0 to disable."></v-text-field>
                        </v-card>
                    </v-expand-transition> -->
                    <v-text-field label="Points" v-model="formData.points" :error-messages="errors.get('points')" outline></v-text-field>
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
            nameDirty: false,
            mode: 'insert',
            openPictureDialog: false,
            formData: {
                name: null,
                description: null,
                price: 0,
                machineType: 'REGULAR',
                serviceType: null,
                regularMinutes: 38,
                // additionalMinutes: 0,
                points: 1
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('washingservice/clearErrors');
        },
        submit() {
            this.$store.dispatch(`washingservice/${this.mode}Service`, {
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
            this.$store.dispatch('washingservice/setPicture', {
                serviceId: this.service.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('setPicture', res.data.img_path);
            });
        },
        removePicture() {
            if(confirm('Delete this picture?')) {
                this.$store.dispatch('washingservice/removePicture', this.service.id).then((res, rej) => {
                    this.$emit('setPicture', '');
                });
            }
        },
        clear() {
            this.$store.commit('washingservice/clearErrors');
        },
        updateTime(serviceType) {
            switch(serviceType) {
                case 'quick':
                    if(!this.nameDirty || this.formData.name == null) {
                        this.formData.name = 'Delicate wash';
                    }
                    console.log(this.formData.name);
                    this.formData.regularMinutes = 24;
                    break;
                case 'regular':
                    if(!this.nameDirty || this.formData.name == null) {
                        this.formData.name = 'Warm Wash';
                    }
                    this.formData.regularMinutes = 38;
                    break;
                case 'more_rinse':
                    if(!this.nameDirty || this.formData.name == null) {
                        this.formData.name = 'Cold Wash';
                    }
                    this.formData.regularMinutes = 40;
                    break;
                case 'premium':
                    if(!this.nameDirty || this.formData.name == null) {
                        this.formData.name = 'Hot Wash';
                    }
                    this.formData.regularMinutes = 46;
                    break;
                case 'additional':
                    if(!this.nameDirty || this.formData.name == null) {
                        this.formData.name = 'Super Wash';
                    }
                    this.formData.regularMinutes = 52;
                    break;
            }
        },
        getServiceType(service) {
            if(service.quick_minutes > 0) {
                return 'quick';
            } else if(service.regular_minutes > 0) {
                return 'regular';
            } else if(service.more_rinse_minutes > 0) {
                return 'more_rinse';
            } else if(service.premium_minutes > 0) {
                return 'premium';
            } else if(service.additional_minutes > 0) {
                return 'additional';
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['washingservice/getErrors'];
        },
        saving() {
            return this.$store.getters['washingservice/isSaving'];
        },
        method() {
            return this.$store.getters.getMachineActivationMethod;
        }
    },
    watch: {
        value(val) {
            this.nameDirty = false;
            this.clear();
            if(!!val && this.service) {
                this.mode = 'update';
                this.formData.name = this.service.name;
                this.formData.description = this.service.description;
                this.formData.price = this.service.price;
                this.formData.machineType = this.service.machine_type;
                this.formData.regularMinutes = this.service.minutes;
                this.formData.serviceType = this.getServiceType(this.service);
                // this.formData.additionalMinutes = this.service.additional_minutes;
                this.formData.points = this.service.points;
            } else {
                this.mode = 'insert';
                // this.formData.name = null;
                this.formData.description = null;
                this.formData.price = 0;
                this.formData.machineType = 'REGULAR';
                // this.formData.regularMinutes = 38;
                // this.formData.additionalMinutes = 0;
                this.formData.points = 1;
                this.formData.serviceType = 'regular';
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
        },
        'formData.serviceType': function(val) {
            if(this.mode == 'insert')
                this.updateTime(val);
        }
    }
}
</script>
