<template>
    <v-dialog :value="value" persistent max-width="640">
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Discount details</v-card-title>
                <v-divider class="py-3"></v-divider>

                <v-card-text>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" label="Name"></v-text-field>
                    <!-- <v-text-field v-model="formData.discountType" label="Discount type" :error-messages="errors.get('discountType')" ></v-text-field> -->
                    <v-combobox :items="discountTypes" item-text="text" item-value="id" :error-messages="errors.get('discountType')" v-model="formData.discountType" label="Discount type" menu-props="id"></v-combobox>
                    <v-text-field v-model="formData.percentage" label="Percentage" :error-messages="errors.get('percentage')" ></v-text-field>
                </v-card-text>

                <v-card-actions>
                    <v-btn type="submit" class="primary" :loading="loading">
                        save
                    </v-btn>
                    <v-btn @click="cancel">
                        Cancel
                    </v-btn>
                </v-card-actions>
            </v-card>
        </form>

    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'discount'
    ],
    data() {
        return {
            formData: {
                name: '',
                discountType: null,
                percentage: 0,
            },
            mode: 'insert',
            discountTypes: [
                {
                    id: 'c',
                    text: 'Cash'
                },
                {
                    id: 'p',
                    text: 'Percentage'
                }
            ]
        }
    },
    methods: {
        cancel() {
            this.$store.commit('discount/clearErrors');
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`discount/${this.mode}Discount`, {
                discountId: this.discount ? this.discount.id : null,
                formData: this.formData
            }).then((res, rej) => {
                console.log(res.data.discount);
                this.$emit('save', {
                    discount: res.data.discount,
                    mode: this.mode
                });
                this.$emit('input', false);
            });
        }
    },
    computed: {
        loading() {
            return this.$store.getters['discount/isSaving'];
        },
        errors() {
            return this.$store.getters['discount/getErrors'];
        }
    },
    watch: {
        discount(val) {
            if(val) {
                this.mode = 'update';
                this.formData.name = val.name;
                this.formData.discountType = val.discount_type == 'c' ? 'Cash' : 'Percentage';
                this.formData.percent = val.percent;
            } else {
                this.mode = 'insert';
                this.formData.name = '';
                this.formData.discountType = null;
                this.formData.percent = 0;
            }
        }
    }
}
</script>
