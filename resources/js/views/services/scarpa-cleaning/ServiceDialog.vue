<template>
    <v-dialog :value="value" max-width="640" persistent>
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
                    <v-text-field label="Description" v-model="formData.description" outline></v-text-field>
                </v-card-text>

                <v-divider></v-divider>
                <v-card-actions v-if="mode == 'update'">
                    <v-spacer></v-spacer>
                    <h4 class="grey--text">Variations</h4>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="activeVariation = null, openVariationDialog = true">
                        <v-icon>add</v-icon>
                    </v-btn>
                </v-card-actions>
                <variations v-if="service" :serviceId="service.id" :variations="variations" @edit="edit" @deleteVariation="deleteVariation" />

                <v-card-actions>
                    <v-btn class="primary" round :loading="saving" type="submit">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <picture-dialog v-if="service" :url="service.img_path" v-model="openPictureDialog" @ok="savePicture" />
        <variation-dialog v-if="service" v-model="openVariationDialog" :serviceId="service.id" @save="updateList" :variation="activeVariation" />
    </v-dialog>
</template>
<script>
import PictureDialog from '../../shared/PictureBrowser.vue';
import Variations from './Variations.vue';
import VariationDialog from './VariationDialog.vue';

export default {
    components: {
        PictureDialog,
        Variations,
        VariationDialog
    },
    props: [
        'value', 'service'
    ],
    data() {
        return {
            activeVariation: null,
            variations: [],
            mode: 'insert',
            openVariationDialog: false,
            openPictureDialog: false,
            formData: {
                name: null,
                description: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('scarpacleaning/clearErrors');
        },
        submit() {
            this.$store.dispatch(`scarpacleaning/${this.mode}Service`, {
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
            this.$store.dispatch('scarpacleaning/setPicture', {
                serviceId: this.service.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('setPicture', res.data.img_path);
            });
        },
        removePicture() {
            if(confirm('Delete this picture?')) {
                this.$store.dispatch('scarpacleaning/removePicture', this.service.id).then((res, rej) => {
                    this.$emit('setPicture', '');
                });
            }
        },
        loadVariations() {
            axios.get(`/api/services/scarpa-cleanings/variations/${this.service.id}`).then((res, rej) => {
                this.variations = res.data.variations;
            });
        },
        updateList(data) {
            if(data.mode == 'insert') {
                this.variations.push(data.variation);
            } else {
                this.activeVariation.color = data.variation.color;
                this.activeVariation.selling_price = data.variation.selling_price;
                this.activeVariation.size = data.variation.size;
            }
        },
        edit(variation) {
            this.activeVariation = variation;
            this.openVariationDialog = true;
        },
        deleteVariation(variation) {
            if(confirm("Delete this variation?")) {
                Vue.set(variation,'isDeleting',  true);
                this.$store.dispatch('scarpacleaning/deleteVariation', variation.id).then((res, rej) => {
                    this.variations = this.variations.filter(p => p.id != res.data.variation.id);
                }).finally(() => {
                    Vue.set(variation,'isDeleting',  false);
                })
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['scarpacleaning/getErrors'];
        },
        saving() {
            return this.$store.getters['scarpacleaning/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.service) {
                this.mode = 'update';
                this.formData.name = this.service.name;
                this.formData.description = this.service.description;
                this.loadVariations();
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.description = null;
                this.variations = [];
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
