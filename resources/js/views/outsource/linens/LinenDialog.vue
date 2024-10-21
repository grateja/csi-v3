<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Linen details</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-combobox :items="categories" dense outline label="Category" hint="Ex. room linen" v-model="formData.category" :error-messages="errors.get('category')" ref="category"></v-combobox>
                    <v-text-field dense outline label="Name" v-model="formData.name" :error-messages="errors.get('name')"></v-text-field>

                    <v-layout>
                        <v-flex xs6>
                            <v-text-field dense outline label="Regular price" type="decimal" v-model="formData.regular_price" :error-messages="errors.get('regular_price')"></v-text-field>
                        </v-flex>
                        <v-flex xs6>
                            <v-text-field dense outline label="Dry weight" type="decimal" v-model="formData.dry_weight" :error-messages="errors.get('dry_weight')"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <p>Price with stain</p>
                    <v-layout>
                        <v-flex xs4>
                            <v-text-field dense outline label="Light" type="decimal" v-model="formData.with_stain_light" :error-messages="errors.get('with_stain_light')"></v-text-field>
                        </v-flex>
                        <v-flex xs4>
                            <v-text-field dense outline label="Medium" type="decimal" v-model="formData.with_stain_medium" :error-messages="errors.get('with_stain_medium')"></v-text-field>
                        </v-flex>
                        <v-flex xs4>
                            <v-text-field dense outline label="Heavy" type="decimal" v-model="formData.with_stain_heavy" :error-messages="errors.get('with_stain_heavy')"></v-text-field>
                        </v-flex>
                    </v-layout>

                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" :loading="saving" round>Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'linen',
        'outSourceId',
    ],
    data() {
        return {
            categories: [],
            mode: 'insert',
            formData: {
                category: null,
                name: null,
                regular_price: 0,
                with_stain_light: 0,
                with_stain_medium: 0,
                with_stain_heavy: 0,
                dry_weight: 0,
                pre_treatment_price: 0,
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('outsourcelinen/clearErrors');
        },
        submit() {
            this.formData.out_source_id = this.outSourceId;
            this.$store.dispatch(`outsourcelinen/${this.mode}Linen`, {
                linenId: this.linen ? this.linen.id : null,
                formData: this.formData
            }).then((res, rej) => {
                // this.close();
                this.$emit('save', {
                    mode: this.mode,
                    linen: res.data.linen
                });
                this.close();
                this.getCategories();
            });
        },
        getCategories() {
            axios.get(`/api/out-source/linens/categories`).then((res, rej) => {
                this.categories = res.data
            }).finally(() => {
                this.loading = false;
            })
        }
    },
    computed: {
        saving() {
            return this.$store.getters['outsourcelinen/isSaving'];
        },
        errors() {
            return this.$store.getters['outsourcelinen/getErrors'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.linen) {
                this.mode = 'update';
                this.formData.category = this.linen.category;
                this.formData.name = this.linen.name;
                this.formData.regular_price = this.linen.regular_price;
                this.formData.dry_weight = this.linen.dry_weight;
                this.formData.with_stain_light = this.linen.with_stain_light;
                this.formData.with_stain_medium = this.linen.with_stain_medium;
                this.formData.with_stain_heavy = this.linen.with_stain_heavy;
            } else {
                this.mode = 'insert';
                this.formData.category = null;
                this.formData.name = null;
                this.formData.regular_price = 0;
                this.formData.dry_weight = 0;
                this.formData.with_stain_light = 0;
                this.formData.with_stain_medium = 0;
                this.formData.with_stain_heavy = 0;
            }
            setTimeout(() => {
                this.$refs.category.$el.querySelector('input').select();
            }, 500);
        },
        linen(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    },
    mounted() {
        this.getCategories();
    }
}
</script>
