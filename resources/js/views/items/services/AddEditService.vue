<template>
    <v-card>
        <service-form @submit="submit" @cancel="cancel"></service-form>
    </v-card>
</template>

<script>
import ServiceForm from './ServiceForm.vue';
export default {
    components : {
        ServiceForm
    },
    methods: {
        submit(data) {
            this.$store.dispatch(`service/${data.mode}Service`, data).then((res, rej) => {
                this.$router.push('/items/service-list');
                console.log(res.data);
            });
            console.log('data', data);
        },
        cancel() {
            this.$router.push('/items/service-list');
        }
    }
}
</script>
