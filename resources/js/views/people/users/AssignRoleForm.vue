<template>
    <form @submit.prevent="submit">
        <v-card>
            <v-card-title class="title grey--text">Assign user's role</v-card-title>

            <v-card-text v-if="user">
                <h4 class="grey--text">{{ user.fullname }}</h4>

                <span class="red--text" v-if="errors.has('roleId')">{{errors.get('roleId')}}</span>

                <v-radio-group v-model="roleId">
                    <template v-for="role in roles">
                        <div :key="role.id">
                            <v-radio :label="role.name" v-model="role.id"></v-radio>
                            <span class="grey--text caption">{{ role.description }}</span>
                        </div>
                    </template>
                </v-radio-group>

                <h4 class="grey--text">Select branch</h4>

                <p class="red--text" v-if="errors.has('branchesId')">{{errors.get('branchesId')}}</p>

                <v-list v-if="branches.length">
                    <v-list-tile v-for="branch in branches" :key="branch.id" @click="selectDeselect(branch)">
                        <v-list-tile-action>
                            <v-checkbox v-model="selectedBranches" :value="branch.id"></v-checkbox>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ branch.name }}</v-list-tile-title>
                            <div class="caption grey--text">
                                {{branch.address}}
                                <span v-if="branch.city_municipality">
                                    {{ branch.city_municipality.name }}
                                </span>
                            </div>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>

                <v-btn v-else class="warning" to="/branch/manage">Create branch</v-btn>

                <v-text-field v-model="password" type="password" label="Password" hint="Owner's password is required to apply changes" :error-messages="errors.get('password')"></v-text-field>
            </v-card-text>

            <v-card-actions>
                <v-btn type="submit" class="primary" :loading="loading">Ok</v-btn>
                <v-btn @click="cancel">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </form>
</template>

<script>
export default {
    props: [
        'user'
    ],
    data() {
        return {
            roleId: null,
            password: null,
            selectedBranches: []
        }
    },
    methods: {
        cancel() {
            this.password = null;
            this.$store.commit('user/clearErrors');
            this.$emit('cancel');
        },
        submit() {
            this.$emit('submit', {
                roleId: this.roleId,
                branchesId: this.selectedBranches,
                password: this.password
            });
            this.password = null;
        },
        get() {
            this.$store.dispatch('user/getRoles');
        },
        selectDeselect(branch) {
            if(this.selectedBranches.some(b => b == branch.id)) {
                this.selectedBranches = this.selectedBranches.filter(b => b != branch.id);
                console.log(this.selectedBranches);
                console.log(branch);
            } else {
                this.selectedBranches.push(branch.id);
            }
        }
    },
    computed: {
        roles() {
            return this.$store.getters['user/getRoles'];
        },
        branches() {
            return this.$store.getters['user/getBranches'];
        },
        loading() {
            return this.$store.getters['user/isSaving'];
        },
        errors() {
            return this.$store.getters['user/getErrors'];
        }
    },
    created() {
        this.get();
    },
    watch: {
        user(val) {
            if(val) {
                let role = this.roles.find(r => r.name == val.roles[0]);
                if(role) {
                    this.roleId = role.id;
                } else {
                    this.roleId = null;
                }

                if(val.branches.length) {
                    this.selectedBranches = val.branches.map(b => b.id);
                } else {
                    this.selectedBranches = [];
                }
            }
        }
    }
}
</script>
