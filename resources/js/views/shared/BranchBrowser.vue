<template>
    <v-menu offset-y>
        <v-btn slot="activator" :loading="retreivingBranch">
            <span v-if="!!activeBranch">{{ activeBranch.name }}</span>
            <span v-else>Select branch</span>
            <v-icon>arrow_drop_down</v-icon>
        </v-btn>
        <v-list dense>
            <v-list-tile v-for="branch in branches" :key="branch.id" @click="selectBranch(branch)">
                <v-list-tile-content>
                    <v-list-tile-title>{{ branch.name }}</v-list-tile-title>
                    <div class="caption grey--text">
                        {{branch.city_municipality.name}}
                    </div>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>

<script>
export default {
    props: [
        'autoSelectFirst'
    ],
    data() {
        return {
            activeBranch: null
        }
    },
    methods: {
        getBranches() {
            this.$store.dispatch('branch/loadBranches').then((res, rej) => {
                if(res.data.branches && this.autoSelectFirst) {
                    this.activeBranch = res.data.branches.find(b => b.is_main);
                    this.$emit('select', this.activeBranch);
                }
            });
        },
        selectBranch(branch) {
            this.activeBranch = branch;
            this.$emit('select', this.activeBranch);
        }
    },
    computed: {
        retreivingBranch() {
            return this.$store.getters['branch/isLoading'];
        },
        branches() {
            return this.$store.getters['branch/getBranches'];
        }
    },
    created() {
        this.getBranches();
    }
}
</script>
