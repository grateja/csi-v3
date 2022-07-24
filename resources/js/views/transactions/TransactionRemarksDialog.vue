<template>
    <v-dialog :value="value" persistent max-width="480">
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Add remarks</v-card-title>

                <v-card-text>
                    <v-list v-if="transaction && transaction.remarks && transaction.remarks.length">
                        <v-list-tile v-for="remarks in transaction.remarks" :key="remarks.id">
                            <v-list-tile-content>
                                <v-list-tile-title>{{remarks.remarks}}</v-list-tile-title>
                                <div class="caption grey--text">{{moment(remarks.created_at).format('LLL')}}</div>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-btn v-if="isOwner" small icon @click="deleteRemarks(remarks)" :loading="remarks.isDeleting">
                                    <v-icon small>delete</v-icon>
                                </v-btn>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>

                    <v-text-field :disabled="saving" ref="remarks" v-model="formData.remarks" :error-messages="errors.get('remarks')" placeholder="Type something and press enter" outline label="Remarks"></v-text-field>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn round @click="close">close</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'transaction', 'value'
    ],
    data() {
        return {
            saving: false,
            formData: {
                remarks: null
            }
        }
    },
    methods: {
        submit() {
            this.saving = true;
            this.$store.dispatch('transaction/addRemarks', {
                transactionId: this.transaction.id,
                formData: this.formData
            }).then((res, rej) => {
                this.transaction.remarks.push(res.data.remarks);
                this.formData.remarks = null;
            }).finally(() => {
                this.saving = false;
                this.focus();
            });
        },
        close() {
            this.$emit('input', false);
        },
        focus() {
            setTimeout(() => {
                this.$refs.remarks.$el.querySelector('input').select();
            }, 500);
        },
        deleteRemarks(remarks) {
            if(confirm('Delete remarks?')) {
                Vue.set(remarks, 'isDeleting', true);
                this.$store.dispatch('transaction/deleteRemarks', remarks.id).then((res, rej) => {
                    this.transaction.remarks = this.transaction.remarks.filter(r => r.id != remarks.id);
                }).finally(() => {
                    Vue.set(remarks, 'isDeleting', false);
                });
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['transaction/getErrors'];
        },
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    },
    watch: {
        value(val) {
            this.focus();
        }
    }
}
</script>
