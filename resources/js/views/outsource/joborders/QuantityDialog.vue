<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card" v-if="linen">
                <v-card-title class="title grey--text">{{mode}} item</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-text-field dense outline label="Item name" readonly :value="linen.name"></v-text-field>
                    <v-text-field dense outline label="Quantity" type="number" v-model="quantity" :error-messages="errors.get('quantity')"></v-text-field>
                    <v-text-field dense outline label="Price" type="text" readonly :value="price" />
                    <v-checkbox v-model="with_stain" label="With stain"></v-checkbox>

                    <v-expand-transition>
                        <v-layout v-if="with_stain">
                            <v-flex xs4>
                                <v-btn :class="{primary: degree_of_soil == 'with_stain_light'}" block @click="degree_of_soil = 'with_stain_light'">light: P {{ parseFloat(linen.with_stain_light).toFixed(2) }}</v-btn>
                            </v-flex>
                            <v-flex xs4>
                                <v-btn :class="{primary: degree_of_soil == 'with_stain_medium'}" block @click="degree_of_soil = 'with_stain_medium'">light: P {{ parseFloat(linen.with_stain_medium).toFixed(2) }}</v-btn>
                            </v-flex>
                            <v-flex xs4>
                                <v-btn :class="{primary: degree_of_soil == 'with_stain_heavy'}" block @click="degree_of_soil = 'with_stain_heavy'">light: P {{ parseFloat(linen.with_stain_heavy).toFixed(2) }}</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-expand-transition>
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
        'jobOrderId',
        'linen',
    ],
    data() {
        return {
            mode: 'insert',
            quantity: 1,
            with_stain: false,
            degree_of_soil: 'regular_price'
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('outsourcejoborder/clearErrors');
        },
        submit() {
            const formData = {
                quantity: parseInt(this.quantity),
                linenId: this.linen.id,
                with_stain: this.with_stain,
                degree_of_soil: this.degree_of_soil
            }

            this.$store.dispatch(`outsourcejoborder/${this.mode}Linen`, {
                jobOrderId: this.jobOrderId,
                formData
            }).then((res, rej) => {
                // this.close();
                this.$emit('save', {
                    mode: this.mode,
                    linen: res.data.jobOrderLinen
                });
                this.close();
            });
        }
    },
    computed: {
        saving() {
            return this.$store.getters['outsourcejoborder/isSaving'];
        },
        errors() {
            return this.$store.getters['outsourcejoborder/getErrors'];
        },
        itemName() {
            return this.linen ? this.linen.name : this.jobOrderLinen ? this.jobOrderLinen.name : ''
        },
        price() {
            let price = 0;
            if(this.with_stain) {
                price = this.linen[this.degree_of_soil]
            } else {
                price = this.linen.regular_price;
            }
            return `P ${parseFloat(price * this.quantity).toFixed(2)}`
        }
    },
    watch: {
        value(val) {
            if(!!val && this.jobOrderLinen) {
                this.mode = 'update';
                this.quantity = this.jobOrderLinen.quantity;
                this.with_stain = this.jobOrderLinen.with_stain
            } else {
                this.mode = 'insert';
                this.quantity = 1;
                this.with_stain = false;
            }
        },
        with_stain(val) {
            if(!val) {
                this.degree_of_soil = 'regular_price';
            }
        }
    }
}
</script>
