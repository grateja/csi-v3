<template>
    <div>
        <menus />
        <!-- <v-divider class="my-4"></v-divider> -->
        <daily-report></daily-report>
        <v-content>
            <breadcrumbs></breadcrumbs>
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
        </v-content>
    </div>
</template>

<script>
import Menus from './layout/Menu.vue';
import Breadcrumbs from './layout/Breadcrumbs.vue';
import DailyReport from './dailyreport/Index.vue';
export default {
    components: {
        Menus,
        Breadcrumbs,
        DailyReport
    },
    methods: {
        // test() {
        //     window.axios.defaults.headers.common['Authorization'] = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE0Mjc5ZmEwYTA5OGQyN2Y4MWJiMzQ5Y2YyNWFhNTU1MzA5MzAzZjYwOGYyNjFkMmQ4NjY4OGRiZjIyOTc2NzhkOTI5MDg1ZmMxYjQxNWYxIn0.eyJhdWQiOiIxIiwianRpIjoiYTQyNzlmYTBhMDk4ZDI3ZjgxYmIzNDljZjI1YWE1NTUzMDkzMDNmNjA4ZjI2MWQyZDg2Njg4ZGJmMjI5NzY3OGQ5MjkwODVmYzFiNDE1ZjEiLCJpYXQiOjE1NTM5OTE3MjAsIm5iZiI6MTU1Mzk5MTcyMCwiZXhwIjoxNTg1NjE0MTIwLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.46QDhRH-lD_UUwlndxigvtcBaiQwoCKJBO3FZ-GIDMwTRj34O1WBrrIkAmttKacGadHzVOjW7Dz6QanGNncdnlroUo19SybzLi_wdoHF37Eol5tX8ro3_8Ol3Efu4QdAn3gB5JZEZzyihdGNMHfVC8B2mEFfpp1KHawGuthNEuQ7klRCGFBs4rVRe1k0Rsq_rCEBDX9R00VdNyRo-rEzUSTPpb72sS4QPGRgziwghY7fgTEjG_U2zRSemNDW-zCFIRoa-DC18-FB2FhK2OH0u67fQiAqDL_0tt1MKQGi2mQ6JJ918XmVNEDFwBooYAL1Kr6riRitjDQjKj4rf53nnmCW6GzmRiRRcYc5KLzhbRPgu6yEc3uZRPMHO7vLvrbUCoRrtW5nFwsLD0SxI9CW9GCcBK1-O-MeaBjjPqQTTJv3TngLHDuckBZYBbjzka-QwKaCpXvGw5c6A0P4GumWFBDAfwmCAEVXcOwpFKDGNpJW25nlHzF7XG_fisiYJ7O2YtutynIeO2o-2W7JH9Xm3M0wYcswA4hZFB5Ce5YtU6iUu8vfyvVbuI8LykRkr_9MULeAWmL7tpTxGcYxemQIvwF3MeAFewZGXPr--jYUhNzZ3C560sWgGReZ-Abk1zKUFy019bv60A3mssXhib5BqTixpf1iS6CbgjwPDImu9EI";
        //     axios.get('/api/branch/add/1').then((res, data) => {
        //         console.log(res);
        //     }).catch(err => {
        //         // console.log(err);
        //     })
        // }
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        }
    }
}
</script>
