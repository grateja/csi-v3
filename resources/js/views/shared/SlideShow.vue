<template>
    <v-layout>
        <v-flex>
            <v-responsive :aspect-ratio="16/9">
                <v-carousel v-if="images && images.length" height="auto" :interval="interval" :value="selectedSlide" :cycle="cycle">
                    <v-carousel-item v-for="(image, i) in images" :key="i" :src="image.source"></v-carousel-item>
                </v-carousel>
            </v-responsive>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    props: [
        'images',
        'interval',
        'selectedSlide',
        'cycle'
    ]
}
</script>


<style <style lang="sass" scoped>
</style>
