<template>
    <v-container>
        <client-form @submit="save" @cancel="cancel" :branch="branch"></client-form>
    </v-container>
</template>

<script>
import ClientForm from './ClientForm.vue';

export default {
    components: {
        ClientForm
    },
    data() {
        return {
            branch: null
        }
    },
    methods: {
        save(data) {
            this.$store.dispatch(`client/${data.mode}Client`, data).then((res, rej) => {
                this.$router.push('/developer/clients');
            });
        },
        get() {
            if(!this.$route.params.id) return;
            axios.get(`/api/branches/${this.$route.params.id}`).then((res, rej) => {
                console.log(res.data);
                this.branch = res.data.branch;
            });
        },
        cancel() {
            this.$router.push('/developer/clients');
        }
    },
    created() {
        this.get();
    }
}
</script>
