<template>
    <div id="app">
        <div class="locale-changer">
            <select v-model="$i18n.locale">
                <option
                    v-for="(lang, i) in langs"
                    :key="`Lang${i}`"
                    :value="lang"
                >
                    {{ lang }}
                </option>
            </select>
        </div>
        <div id="nav">
            <router-link to="/">{{ $t('Home') }}</router-link>
            |
            <router-link to="/about">{{ $t('About') }}</router-link>
        </div>
        <router-view />
    </div>
</template>
<script>
export default {
    data: () => ({
        langs: ['es', 'en']
    })
};
</script>

<style lang="scss">
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
}

.locale-changer {
    position: absolute;
    top: 10px;
    right: 10px;
}

#nav {
    padding: 30px;

    a {
        font-weight: bold;
        color: #2c3e50;

        &.router-link-exact-active {
            color: #42b983;
        }
    }
}
</style>
