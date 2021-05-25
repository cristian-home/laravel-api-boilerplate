<template>
    <div class="home">
        <div class="hero">
            <img
                width="200px"
                alt="Laravel logo"
                src="@js/assets/laravel-logo.svg"
            />
            <img class="plus" width="50px" src="@js/assets/plus.svg" />
            <img width="200px" alt="Vue logo" src="@js/assets/vue-logo.svg" />
        </div>
        <HelloWorld msg="Welcome to Your Vue.js App" />
    </div>
</template>

<script>
import HelloWorld from '@js/components/HelloWorld.vue';

export default {
    name: 'Home',
    components: {
        HelloWorld
    }
};
</script>

<style lang="scss" scoped>
.hero {
    display: flex;
    justify-content: center;
}
.plus{
    margin: 15px;
}
</style>
