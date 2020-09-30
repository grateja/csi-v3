<template>
    <v-container>
        <v-btn to="/account/edit" class="white" active-class="primary" round>Edit account info</v-btn>
        <v-btn to="/account/update-email" class="white" active-class="primary" round>Update email</v-btn>
        <v-btn to="/account/change-password" class="white" active-class="primary" round>Change password</v-btn>
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
