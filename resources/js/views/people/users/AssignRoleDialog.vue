<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <assign-role-form :user="user" @submit="save" @cancel="$emit('input', false)"></assign-role-form>
    </v-dialog>
</template>

<script>
import AssignRoleForm from './AssignRoleForm.vue';
export default {
    props: [
        'value', 'user'
    ],
    components: {
        AssignRoleForm
    },
    methods: {
        save(formData) {
            this.$store.dispatch('user/assignRole', {
                userId: this.user.id,
                formData: formData
            }).then((res, rej) => {
                this.$emit('save', res.data);
                this.$emit('input', false);
            });
        }
    }
}
</script>
