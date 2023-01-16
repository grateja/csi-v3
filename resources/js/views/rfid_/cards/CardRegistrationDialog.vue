<template>
    <v-dialog :value="value" max-width="640px" persistent>
        <card-registration-form @close="$emit('input', false)" :cardType="cardType" :rfidCard="rfidCard" @save="data => $emit('save', data)"></card-registration-form>
    </v-dialog>
</template>

<script>
import CardRegistrationForm from './CardRegistrationForm.vue';
export default {
    props: [
        'value',
        'rfidCard'
    ],
    components: {
        CardRegistrationForm
    },
    data() {
        return {
            cardType: null
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.cardType = this.$route.params.cardType;
                console.log('value', this.cardType);
            }
        }
    }
}
</script>
