<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Product details</v-card-title>
                <v-divider></v-divider>
                <v-responsive v-if="product && product.img_path" :aspect-ratio="16/9" max-height="300">
                    <v-img :src="product.img_path"></v-img>
                </v-responsive>
                <v-card-actions v-if="mode == 'update'">
                    <v-btn @click="openPictureDialog = true" class="ml-0" round>select picture</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="removePicture" v-if="!!product && product.img_path"> <v-icon>delete</v-icon></v-btn>
                </v-card-actions>
                <v-card-text>
                    <v-text-field dense outline label="Name" v-model="formData.name" :error-messages="errors.get('name')" ref="name"></v-text-field>
                    <v-text-field dense outline label="Description" v-model="formData.description"></v-text-field>
                    <v-text-field dense outline label="Selling price" v-model="formData.sellingPrice"></v-text-field>
                    <v-text-field dense outline label="Current Stock" v-model="formData.currentStock"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" :loading="saving" round>Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <picture-dialog v-if="product" :url="product.img_path" v-model="openPictureDialog" @ok="savePicture" />
    </v-dialog>
</template>

<script>
import PictureDialog from '../shared/PictureBrowser.vue';
export default {
    components: {
        PictureDialog
    },
    props: [
        'value',
        'product'
    ],
    data() {
        return {
            mode: 'insert',
            openPictureDialog: false,
            formData: {
                name: null,
                description: null,
                sellingPrice: 0,
                currentStock: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('product/clearErrors');
        },
        submit() {
            this.$store.dispatch(`product/${this.mode}Product`, {
                productId: this.product ? this.product.id : null,
                formData: this.formData
            }).then((res, rej) => {
                // this.close();
                this.$emit('save', {
                    mode: this.mode,
                    product: res.data.product
                });
            });
        },
        savePicture(formData) {
            this.$store.dispatch('product/setPicture', {
                productId: this.product.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('setPicture', res.data.img_path);
            });
        },
        removePicture() {
            if(confirm('Delete this picture?')) {
                this.$store.dispatch('product/removePicture', this.product.id).then((res, rej) => {
                    this.$emit('setPicture', '');
                });
            }
        }
    },
    computed: {
        saving() {
            return this.$store.getters['product/isSaving'];
        },
        errors() {
            return this.$store.getters['product/getErrors'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.product) {
                this.mode = 'update';
                this.formData.name = this.product.name;
                this.formData.description = this.product.description;
                this.formData.imgPath = this.product.img_path;
                this.formData.sellingPrice = this.product.selling_price;
                this.formData.currentStock = this.product.current_stock;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.description = null;
                this.formData.imgPath = null;
                this.formData.sellingPrice = 0;
                this.formData.currentStock = 0;
            }
            setTimeout(() => {
                this.$refs.name.$el.querySelector('input').select();
            }, 500);
        },
        product(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
