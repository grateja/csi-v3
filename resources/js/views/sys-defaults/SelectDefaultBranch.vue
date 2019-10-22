<template>
    <div>
        <h3 class="grey--text title">Select default branch</h3>
        <v-progress-linear v-if="loading" indeterminate></v-progress-linear>
        <v-divider class="my-4" v-else></v-divider>

        <p v-if="!loading && branches.length == 0">You are not assigned to any branch.</p>

        <v-layout row wrap v-else>
            <v-flex xs12 sm6 md4 lg3 v-for="branch in branches" :key="branch.id">
                <v-card class="ma-2" @click="selectBranch(branch)">
                    <v-card-actions class="ma-0 pa-0">
                        <v-card-title class="title grey--text">
                            {{branch.name}}
                        </v-card-title>
                    </v-card-actions>
                    <v-card-text class="caption">
                        {{branch.address}}
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    computed: {
        branches() {
            return this.$store.getters['branch/getBranches'];
        },
        loading() {
            return this.$store.getters['branch/isLoading'];
        }
    },
    created() {
        //if(this.branches.length == 0) {
            this.$store.dispatch('branch/loadBranches');
        //}
    },
    methods: {
        selectBranch(branch) {
            this.$store.dispatch('branch/setDefaultBranch', branch.id).then((res, rej) => {
                history.back();
            }).catch(err => {
            });
        }
    }
}
</script>
