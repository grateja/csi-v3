<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Service details</v-card-title>

                <v-responsive v-if="service && service.img_path" :aspect-ratio="16/9" max-height="300">
                    <v-img :src="service.img_path"></v-img>
                </v-responsive>

                <v-card-text>
                    <v-text-field label="Name" v-model="formData.name" :error-messages="errors.get('name')" outline ref="name"></v-text-field>
                    <v-text-field label="Additional charge" v-model="formData.additionalCharge" :error-messages="errors.get('additionalCharge')" outline></v-text-field>
                    <v-text-field label="Discount" v-model="formData.discount" :error-messages="errors.get('discount')" outline></v-text-field>
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
                additionalCharge: 0,
                discount: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('fullservice/clearErrors');
        },
        submit() {
            this.$store.dispatch(`fullservice/${this.mode}Service`, {
                serviceId: this.service ? this.service.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    service: res.data.service
                });
                this.close();
            });
        },
        savePicture(formData) {
            this.$store.dispatch('fullservice/setPicture', {
                serviceId: this.service.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('setPicture', res.data.img_path);
            });
        },
        removePicture() {
            if(confirm('Delete this picture?')) {
                this.$store.dispatch('fullservice/removePicture', this.service.id).then((res, rej) => {
                    this.$emit('setPicture', '');
                });
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['fullservice/getErrors'];
        },
        saving() {
            return this.$store.getters['fullservice/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.service) {
                this.mode = 'update';
                this.formData.name = this.service.name;
                this.formData.additionalCharge = this.service.additional_charge;
                this.formData.discount = this.service.discount;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.additionalCharge = 0;
                this.formData.discount = 0;
            }
            setTimeout(() => {
                this.$refs.name.$el.querySelector('input').select();
            }, 500);
        }
    }
}
</script>
