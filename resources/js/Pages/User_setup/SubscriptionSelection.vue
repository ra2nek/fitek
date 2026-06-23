<script setup>
    import { Head, useForm } from '@inertiajs/vue3';
    import { reactive } from 'vue';
    import axios from 'axios';

    // Importing components

    import PrimaryButton from '../../Components/PrimaryButton.vue';

    // Code

    const storage = reactive({
        plans: [],
        backgroundCircles: 13,
    })

    async function fetchData( endpoint ){
        try{
            const response = await axios.get(endpoint);
            return response.data;
        } catch (error){
            console.error(`Error fetchin ${endpoint}: `, error);
            return [];
        }
    }

    async function getContent() {
        const plans = await fetchData('/avaiable-plans');

        storage.plans = Object.values(plans);

    }

    async function changeSubscription(data) {
        axios({
            method: 'put',
            url: route('subscription.update'),
            data: {
                subscription: data
            }
        }).then(function (response) {
            window.location.href = route('dashboard');
        }).catch(function (error) {
            console.log(error)
        })   
    }

    getContent();

</script>

<template>
    <Head title="nasze plany" />

        <!-- Background -->

    <div id="background">

        <div class="BG">
            <div class="BGcircle" v-for="n in storage.backgroundCircles"></div>
        </div>
        <div class="BG">
            <div class="BGcircle" v-for="n in storage.backgroundCircles"></div>
        </div>
        <div class="BG">
            <div class="BGcircle" v-for="n in storage.backgroundCircles"></div>
        </div>
        <div class="BG">
            <div class="BGcircle" v-for="n in storage.backgroundCircles"></div>
        </div>
        <div class="BG">
            <div class="BGcircle" v-for="n in storage.backgroundCircles"></div>
        </div>

    </div>
        

    <!-- Content -->

    <header> <h1> Nasze plany </h1> </header>

    <div id="holder">
        <div class="card" v-for="plan in storage.plans">
            <h2> {{ plan.name }} </h2>
            <img :src="plan.url" class="plan-logo">
            <h1>{{ plan.price }}zł / {{ plan.period }}</h1>

            <h2>Dostęp do: </h2>
            <ul>
                <li v-for="feature in JSON.parse(plan.features)" > {{ feature }} </li>
            </ul>

            <PrimaryButton class="btn" @click="changeSubscription( plan.name)"> Wybierz </PrimaryButton>
        </div>
    </div>

</template>

<style lang="css">

    /* Content */

    .background{
        background-color: var(--primary);
        height: 100%;
        width: 100%;
    }

    header{
        text-align: center
    }

    #holder{
        width: 100%;
        padding: 2rem;
        display: flex;
        justify-content: space-around;
    }

    .card{
        background-color: white;
        width: 20rem;
        height: 40rem;
        border-radius: 2rem;
        padding: 1rem;

        position: relative;

        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .plan-logo{
        width: 50%;
    }

    ul li{
        list-style: circle ;
    }

    ul{
        color: #4c4c4c;
    }

    .btn{
        position: absolute;
        bottom: 2rem;
        transform: scale(2);
    }

   

    /* Background */

    .BG{
        width: 100%;
        height: 20%;

        background-color: var(--primary);

        position: absolute;

        display: flex;
    }

    .BG:nth-of-type(5){
        filter: brightness(.6);
        bottom: 0;
        z-index: -10;
    }

    .BG:nth-of-type(4){
        filter: brightness(.7);
        bottom: 20%;
        z-index: -11;
    }

    .BG:nth-of-type(3){
        filter: brightness(.8);
        bottom: 40%;
        z-index: -12;

    }

    .BG:nth-of-type(2){
        filter: brightness(.9);
        bottom: 60%;
        z-index: -13;

    }

    .BG:nth-of-type(1){
        bottom: 80%;
        z-index: -14;
    }


    .BGcircle{
        height: 10rem;
        aspect-ratio: 1/1;
        border-radius: 10rem;

        background-color: var(--primary);
        transform: translateY(-30%);
        overflow: hidden;
    }


</style>