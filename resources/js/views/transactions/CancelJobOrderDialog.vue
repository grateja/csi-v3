<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    <span class="title grey--text">Cancel Job Order</span>
                    <v-spacer></v-spacer>
                    <v-btn small icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-textarea label="Remarks" v-model="remarks" :error-messages="errorMessage" outline ref="remarks"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round>Confirm</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
    ],
    data() {
        return {
            remarks: null,
            errorMessage: null
        }
    },
    methods : {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.errorMessage = null
            if(this.remarks == null) {
                this.errorMessage = "Please state your reason why you need to cancel this Job order!"
                return;
            }
            this.$emit('confirm', this.remarks)
            this.close()
        }
    },
    watch: {
        value(val) {
            this.remarks = null
        }
    }
}
</script>