<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '../Components/ApplicationLogo.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue';

import { reroute } from '@/tools';

import { convertYoutubeUrlToEmbedUrl } from '@/tools';

console.log(convertYoutubeUrlToEmbedUrl('https://youtu.be/3tSIwPUSdJs?si=qRsiNZGO68Tq5SN8'));

console.log(convertYoutubeUrlToEmbedUrl('https://www.youtube.com/watch?v=3tSIwPUSdJs&t=252'));

const { props: pageProps } = usePage();

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

if(props.canLogin && pageProps.auth?.user) reroute("dashboard");



</script>

<template>
    <Head title="przekierowanie" />
    
    <div id="main">

        <ApplicationLogo class="logo">

        </ApplicationLogo>

        <div id="options" v-if="canLogin">

            <div v-if="!$page.props.auth.user">
                <PrimaryButton class="btn" @click="reroute('login')"> Zaloguj się </PrimaryButton>
                <PrimaryButton class="btn" @click="reroute('register')"> Zarejestruj się </PrimaryButton>
            </div>

            <div v-else>
                <PrimaryButton class="btn" @click="reroute('dashboard')"> Przejdź do serwisu </PrimaryButton>
            </div>
            
            

        </div>        

    </div>

</template>

<style>

#main{
    width: 100%;
    height: 95vh;
    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.logo{
    width: 50%;
}

#options{
    width: 50%;
    height: 5%;
    position: relative;

    margin-top: 5rem;
}

#options div{
    display: flex;
    justify-content: space-around;
    width: 100%;
    height: 100%;
    position: relative;
}

.btn{
    height: 100%;
    width: 45%;
}

</style>
