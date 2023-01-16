<template>
    <v-dialog persistent :value="value" max-width="480px">
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title>
                    <span class="title grey--text">Input KG</span>
                    <v-spacer></v-spacer>
                    <v-btn icon small @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text v-if="!!item">
                    <v-layout>
                        <v-flex xs5><span class="data-term">Service Name:</span></v-flex>
                        <v-flex xs7><span class="data-value">{{item.name}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term">Price Per Kilo:</span></v-flex>
                        <v-flex xs7><span class="data-value">{{item.price_per_kilo}}</span></v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs5><span class="data-term">Total Price:</span></v-flex>
                        <v-flex xs7><span class="data-value">{{totalPrice}}</span></v-flex>
                    </v-layout>

                    <v-text-field class="round-input mt-3" outline v-model="kg" label="KG" ref="kg" :error-messages="error"></v-text-field>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round>Ok</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'item',
        'value'
    ],
    data() {
        return {
            kg: 0,
            error: null
        }
    },
    methods: {
        close() {
            this.$emit('input', false)
        },
        submit() {
            if(parseFloat(this.kg) <= 0) {
                this.error = "Cannot be '0'"
                return
            }
            this.$emit('select', this.kg);
            this.close();
        }
    },
    computed: {
        totalPrice() {
            let price = parseFloat(this.item.price_per_kilo);
            let kilo = parseFloat(this.kg);

            return parseFloat(price * kilo | 0).toFixed(2)
        }
    },
    watch: {
        value(val) {
            if(val && this.item) {
                setTimeout(() => {
                    this.$refs.kg.$el.querySelector('input').select();
                }, 500);
            } else {
                this.kg = 0;
            }
        }
    }
}
</script>