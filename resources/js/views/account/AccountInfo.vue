<template>
    <v-container>
        <v-btn to="/account/edit" class="white" active-class="primary">Edit account info</v-btn>
        <v-btn to="/account/update-email" class="white" active-class="primary">Update email</v-btn>
        <v-btn to="/account/change-password" class="white" active-class="primary">Change password</v-btn>
        <!-- <pre>{{accountInfo}}</pre> -->
    </v-container>
</template>

<script>
export default {
    computed: {
        accountInfo() {
            return this.$store.getters.getCurrentUser;
        }
    }
}
</script>
