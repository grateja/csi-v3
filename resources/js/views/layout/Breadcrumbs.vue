<template>
    <v-card v-if="breadcrumbs.length" class="cyan darken-1" flat>
        <v-breadcrumbs :items="breadcrumbs">
            <li slot="item" slot-scope="props">
                <router-link exact :to="props.item.path" active-class="white--text">{{props.item.meta.breadcrumb}}</router-link>
            </li>
        </v-breadcrumbs>
    </v-card>
</template>

<script>
export default {
    computed: {
        breadcrumbs() {
            let title = this.$route.meta.breadcrumb;
            if(title) {
                document.title = title;
            }
            return this.$breadcrumbs;
        }
    }
}
</script>

<style scoped>
a {
    color: silver;
}
a.active {
    color: white;
}
li.v-breadcrumbs__divider {
    color: grey;
}
</style>
