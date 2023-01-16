<template>
    <form @submit.prevent="submit">
        <v-card>
            <v-card-title class="grey--text title">
                {{mode == 'insert' ? 'Register' : 'Update'}} {{cardType == 'c' ? 'Customer' : 'Master'}} Card
            </v-card-title>
            <v-card-text>
                <template v-if="mode == 'insert'">
                    <vuetify-autocomplete v-if="cardType == 'c'" url="/api/autocomplete/customers/self" @select="selectCustomer" label="Search customer here" append-icon="search" :error-message="errors.get('customerId')"></vuetify-autocomplete>
                    <vuetify-autocomplete v-if="cardType == 'u'" url="/api/autocomplete/users/self" @select="selectUser" data_field="fullname" label="Search user here" append-icon="search" :error-message="errors.get('userId')"></vuetify-autocomplete>
                </template>
                <v-text-field label="RFID" v-model="formData.rfid" :error-messages="errors.get('rfid')"></v-text-field>
                <v-checkbox v-if="cardType == 'u'" label="Unlimited" v-model="formData.unlimited"></v-checkbox>
                <!-- <v-text-field v-if="mode == 'insert'" label="Initial balance" v-model="formData.initialBalance" :error-messages="errors.get('initialBalance')"></v-text-field> -->
            </v-card-text>
            <v-card-actions>
                <v-btn type="submit" class="primary" :loading="loading">Ok</v-btn>
                <v-btn @click="close">close</v-btn>
            </v-card-actions>
        </v-card>
    </form>
</template>

<script>
export default {
    props: [
        'cardType',
        'rfidCard'
    ],
    data() {
        return {
            formData: {
                rfid: '',
                customerId: '',
                userId: '',
                unlimited: false
                // initialBalance: 0
            },
            mode: 'insert'
        }
    },
    methods: {
        close() {
            this.$emit('close', false);
            this.formData.userId = null;
            this.formData.customerId = null;
            // this.formData.initialBalance = null;
        },
        submit() {
            this.formData.cardType = this.cardType;
            this.$store.dispatch(`rfidcard/${this.mode}Card`,{
                formData: this.formData,
                rfidCardId: this.rfidCard ? this.rfidCard.id : null
            }).then((res, rej) => {
                this.$emit('save', {
                    rfidCard: res.data.rfidCard,
                    mode: this.mode
                });
                this.close();
            })
        },
        selectCustomer(customer) {
            console.log('customer', customer);
            this.formData.customerId = customer.id;
            this.formData.userId = null;
        },
        selectUser(user) {
            console.log('user', user);
            this.formData.userId = user.id;
            this.formData.customerId = null;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['rfidcard/getErrors'];
        },
        loading() {
            return this.$store.getters['rfidcard/isSaving'];
        }
    },
    watch: {
        rfidCard(val) {
            if(val) {
                this.formData.rfid = val.rfid;
                this.formData.unlimited = val.unlimited;
                this.mode = 'update';
            } else {
                this.mode = 'insert';
                this.formData.rfid = '';
                this.formData.unlimited = false;
            }
        }
    }
}
</script>
