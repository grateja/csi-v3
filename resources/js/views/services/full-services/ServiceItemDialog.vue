<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Service item details</v-card-title>

                <v-card-text>
                    <v-combobox :items="results" label="Search service" @input.native="search($event)" ref="keyword" item-text="name" @change="select"></v-combobox>

                    <v-text-field label="Name" v-model="formData.name" :error-messages="errors.get('name')" outline></v-text-field>
                    <v-text-field label="Quantity" v-model="formData.quantity" :error-messages="errors.get('quantity')" outline></v-text-field>
                    <v-text-field label="Unit Price" v-model="formData.price" :error-messages="errors.get('price')" outline></v-text-field>
                    <v-text-field label="Total price" readonly outline :value="formData.price * formData.quantity" hint="Automatically generated" persistentHint></v-text-field>
                    <v-text-field label="Points" v-model="formData.points" :error-messages="errors.get('points')" outline></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" round :loading="saving" type="submit">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value', 'serviceItem', 'fullServiceId'
    ],
    data() {
        return {
            mode: 'insert',
            openPictureDialog: false,
            formData: {
                name: null,
                quantity: 0,
                points: 0,
                price: 0,
                fullServiceId: null
            },
            results: []
        }
    },
    methods: {
        search(e) {
            this.formData.id = null;
            axios.get('/api/services/all', {
                params: {
                    keyword: e.target.value
                }
            }).then((res, rej) => {
                this.results = res.data.result;
            });
        },
        select(item) {
            if(!!item && item.id) {
                this.formData.name = item.name;
                this.formData.quantity = 1;
                this.formData.points = item.points || 0;
                this.formData.price = item.price;
            }
        },
        close() {
            this.$emit('input', false);
            this.$store.commit('fullserviceitem/clearErrors');
        },
        submit() {
            this.$store.dispatch(`fullserviceitem/${this.mode}Service`, {
                serviceItemId: this.serviceItem ? this.serviceItem.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    serviceItem: res.data.serviceItem
                });
                this.close();
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['fullserviceitem/getErrors'];
        },
        saving() {
            return this.$store.getters['fullserviceitem/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.serviceItem) {
                this.mode = 'update';
                this.formData.name = this.serviceItem.name;
                this.formData.quantity = this.serviceItem.quantity;
                this.formData.points = this.serviceItem.points;
                this.formData.price = this.serviceItem.price;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.quantity = 0;
                this.formData.points = 0;
                this.formData.price = 0;
            }
            setTimeout(() => {
                this.$refs.keyword.$el.querySelector('input').select();
            }, 500);
        },
        fullServiceId(val) {
            this.formData.fullServiceId = val;
        }
    }
}
</script>
