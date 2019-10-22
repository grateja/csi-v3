<template>
    <form @submit.prevent="submit">
        <v-card>
            <v-card-text class="title grey--text">Service details</v-card-text>

            <v-progress-linear indeterminate v-if="retreiving" height="2"></v-progress-linear>
            <v-divider v-else></v-divider>

            <v-card-text>

                <v-layout row wrap>
                    <!-- <v-flex xs3 v-if="mode == 'insert'">
                        <h4 class="subtitle grey--text">Branches</h4>
                        <v-list dense v-if="branches">
                            <v-list-tile v-for="branch in branches" :key="branch.id">
                                <v-list-tile-action>
                                    <v-checkbox v-model="selectedBranches" :value="branch.id"></v-checkbox>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        {{branch.name}}
                                    </v-list-tile-title>
                                    <div class="caption grey--text">
                                        {{branch.address}}
                                    </div>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                        <v-progress-circular v-else indeterminate></v-progress-circular>
                    </v-flex> -->
                    <v-flex>
                        <!-- <pre>{{serviceTypes}}</pre>
                        <pre>{{loadingServiceTypes}}</pre> -->

                        <v-combobox v-model="formData.serviceType" label="Service type" :items="serviceTypes" item-text="name" :error-messages="errors.get('serviceType')"></v-combobox>

                        <v-text-field v-model="formData.name" label="Name" :error-messages="errors.get('name')" @input="errors.clear('name')"></v-text-field>
                        <v-text-field v-model="formData.description" label="Description" :error-messages="errors.get('description')" @input="errors.clear('description')"></v-text-field>
                        <v-text-field v-model="formData.barcode" label="Barcode" :error-messages="errors.get('barcode')" @input="errors.clear('barcode')"></v-text-field>

                        <template v-if="mode == 'insert'">
                            <!-- <v-text-field v-if="formData.serviceType && formData.serviceType.id == 1" v-model="formData.addSuperWash" label="Add super wash" :error-messages="errors.get('addSuperWash')" @input="errors.clear('addSuperWash')" hint="Minutes to be added in regular wash" type="number"></v-text-field> -->
                            <v-checkbox v-model="formData.addSuperWash" label="Add super wash" v-if="formData.serviceType && formData.serviceType.id == 1"></v-checkbox>
                            <v-text-field v-model="formData.pulseCount" label="Pulse count" :error-messages="errors.get('pulseCount')" @input="errors.clear('pulseCount')" :hint="tapHint" type="number"></v-text-field>
                            <v-text-field v-model="formData.minutesPerPulse" label="Minutes per pulse" :error-messages="errors.get('minutesPerPulse')" @input="errors.clear('minutesPerPulse')" :hint="tapHint" type="number"></v-text-field>
                            <v-text-field v-model="formData.fullServicePrice" label="Full service price" :error-messages="errors.get('fullServicePrice')" @input="errors.clear('fullServicePrice')"></v-text-field>
                            <v-text-field v-model="formData.selfServicePrice" label="Self service price" :error-messages="errors.get('selfServicePrice')" @input="errors.clear('selfServicePrice')"></v-text-field>
                            <v-text-field v-model="formData.points" label="Points" :error-messages="errors.get('points')" @input="errors.clear('points')"></v-text-field>
                        </template>
                    </v-flex>
                </v-layout>


            </v-card-text>

            <v-card-actions>
                <v-btn class="primary" type="submit" :loading="loading">Save</v-btn>
                <v-btn class="default" type="button" @click="cancel">Cancel</v-btn>
            </v-card-actions>

        </v-card>
    </form>
</template>

<script>
import BranchBrowser from '../../shared/BranchBrowser.vue';

export default {
    components: {
        BranchBrowser
    },
    data() {
        return {
            formData: {
                name: '',
                description: '',
                barcode: '',
                fullServicePrice: 0,
                selfServicePrice: 0,
                points: 0.5,
                serviceType: null,
                pulseCount: 0,
                minutesPerPulse: 0,
                addSuperWash: 0
            },
            mode: 'insert',
            activeBranch: null,
            retreiving: false,
            serviceId: null,
            branches: null,
            selectedBranches: []
        }
    },
    methods: {
        submit() {
            if(this.loading) return;

            this.formData.branchesIds = this.selectedBranches;

            this.$emit('submit', {
                serviceId: this.serviceId,
                formData: this.formData,
                mode: this.mode
            });
        },
        cancel() {
            this.$emit('cancel');
        },
        selectBranch(branch) {
            this.activeBranch = branch;
        },
        loadBranches() {
            if(!this.branches && !this.loadingBranches) {
                this.$store.dispatch('branch/loadBranches').then((res, rej) => {
                    this.branches = res.data.branches;
                    if(this.currentUser) {
                        this.selectedBranches = [this.currentUser.active_branch_id]; //res.data.branches.map(b => b.id);
                    }
                });
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['service/getErrors'];
        },
        loading() {
            return this.$store.getters['service/isSaving'];
        },
        loadingBranches() {
            return this.$store.getters['branch/isLoading'];
        },
        serviceTypes() {
            return this.$store.getters['servicetype/getServiceTypes'];
        },
        loadingServiceTypes() {
            return this.$store.getters['servicetype/isLoading'];
        },
        tapHint() {
            if(this.formData.serviceType != null) {
                if(this.formData.serviceType.id == 1) {
                    return ((this.formData.minutesPerPulse * this.formData.pulseCount) /*+ parseInt(this.formData.addSuperWash)*/) + ' minutes per wash.';
                } else if(this.formData.serviceType.id == 2) {
                    return this.formData.minutesPerPulse * this.formData.pulseCount + ' minutes per dry.';
                } else {
                    return null;
                }
            } else {
                return this.formData.minutesPerPulse * this.formData.pulseCount + ' minutes per activate.';
            }
        },
        currentUser() {
            return this.$store.getters.getCurrentUser;
        }
    },
    created() {
        this.serviceId = this.$route.params.id;
        this.$store.dispatch('servicetype/loadServiceTypes');
        if(this.serviceId) {
            this.retreiving = true;


            axios.get(`/api/services/${this.serviceId}`).then((res, rej) => {
                this.retreiving = false;
                this.formData.name = res.data.service.name;
                this.formData.description = res.data.service.description;
                this.formData.barcode = res.data.service.barcode;
                this.formData.serviceType = res.data.service.service_type;
                this.mode = 'update';
            }).catch(err => {
                this.retreiving = false;
                this.mode = 'insert';
            });
        } else {
            this.loadBranches();
        }
    }
}
</script>
