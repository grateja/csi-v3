<template>
    <v-dialog persistent max-width="480px" :value="value">
        <v-card v-if="!!slide">
            <v-card-title class="title">Change order</v-card-title>
            <v-responsive>
                <v-img :src="slide.source"></v-img>
            </v-responsive>
            <v-card-text>
                <p>Change order from {{slide.order}} to:</p>
                <v-text-field type="number" v-model="order"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="ok">Ok</v-btn>
                <v-btn @click="$emit('input', false)">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'slide'
    ],
    data() {
        return {
            order: 0
        }
    },
    methods: {
        ok() {
            this.$emit('ok', {
                newOrder: this.order,
                slide: this.slide
            });
            this.$emit('input', false);
        }
    },
    watch: {
        slide(val) {
            if(val) {
                this.order = val.order;
            } else {
                this.order = 0;
            }
        }
    }
}
</script>
