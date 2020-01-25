<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Discount info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" outline label="Name"></v-text-field>
                    <v-text-field v-model="formData.percentage" :error-messages="errors.get('percentage')" outline label="Percentage"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'discount'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                name: null,
                percentage: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`discount/${this.mode}Discount`, {
                discountId: this.discount ? this.discount.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    discount: res.data.discount,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['discount/getErrors'];
        },
        saving() {
            return this.$store.getters['discount/isSaving'];
        }
    },
    watch: {
        discount(val) {
            if(val) {
                this.mode = 'update';
                this.formData.name = val.name;
                this.formData.percentage = val.percentage;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.percentage = 0;
            }
        }
    }
}
</script>
