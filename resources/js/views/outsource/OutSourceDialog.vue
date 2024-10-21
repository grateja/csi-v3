<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Account details</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-text-field dense outline label="Abbr" v-model="formData.abbr" :error-messages="errors.get('abbr')" ref="abbr"></v-text-field>
                    <v-text-field dense outline label="Name" v-model="formData.company_name" :error-messages="errors.get('company_name')"></v-text-field>
                    <v-text-field dense outline label="Address" v-model="formData.address"></v-text-field>
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
        'outSource'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                abbr: null,
                company_name: null,
                address: null,
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('outsource/clearErrors');
        },
        submit() {
            this.$store.dispatch(`outsource/${this.mode}OutSource`, {
                outSourceId: this.outSource ? this.outSource.id : null,
                formData: this.formData
            }).then((res, rej) => {
                // this.close();
                this.$emit('save', {
                    mode: this.mode,
                    outSource: res.data.outSource
                });
                this.close();
            });
        }
    },
    computed: {
        saving() {
            return this.$store.getters['outsource/isSaving'];
        },
        errors() {
            return this.$store.getters['outsource/getErrors'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.outSource) {
                this.mode = 'update';
                this.formData.abbr = this.outSource.abbr;
                this.formData.company_name = this.outSource.company_name;
                this.formData.address = this.outSource.address;
            } else {
                this.mode = 'insert';
                this.formData.abbr = null;
                this.formData.company_name = null;
                this.formData.address = null;
            }
            setTimeout(() => {
                this.$refs.abbr.$el.querySelector('input').select();
            }, 500);
        }
    }
}
</script>
