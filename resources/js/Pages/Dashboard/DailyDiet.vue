<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { reactive } from 'vue';

import { fetchData, Toaster } from '@/tools';

import { Link, Head } from '@inertiajs/vue3';

const storage = reactive({
    plan: []
}) 

async function getDiet(){
    try{
        const data = await fetchData(route('plan.today'));
        console.log(data);
        storage.plan = data;
    } catch (e) {
        Toaster.error(e);
    }
}

const meal_time_translations = {
    "breakfast": "Śniadanie",
    "brunch": "Drugie śniadanie",
    "lunch": "Obiad",
    "snack": "Przekąska",
    "dinner": "Kolacja",
}

getDiet()

</script>

<template>

    <Head title="Dzisiejsza dieta"/>
    <AuthenticatedLayout>
        <div id="no-data" v-if="storage.plan.length === 0">
            Brak planu dla wybranej diety na dzień dzisiejszy.
        </div>

        <div id="content" v-else>
            <h1>Dzisiejsza dieta</h1>

            <template v-for="plan in storage.plan">
                <template v-if="plan == null"></template>
                <template v-else>
                    <h2> {{ meal_time_translations[plan.meal_time] }} </h2>

                    <div class="meal">
                        <div class="img-holder">
                            <img :src="plan.thumbnail" :alt="plan.title">
                        </div>
                        <div class="details">
                            <h3><Link :href="route('meal', plan.id)"> {{plan.title}} </Link></h3>
                            <p>Ilość kalorii: {{ plan.calories }}</p>
                            <p>Szacowany czas przygotowania: ~{{ plan.time }}min </p>
                        </div>
                    </div>
                </template>
            </template>
        </div>

    </AuthenticatedLayout>

</template>

<style scoped>

    #no-data{
        background-color: white;
        padding: 3rem 1rem;
        width: 80%;
        display: grid;
        place-items: center;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 2rem;
    }

    #content{
        background-color: white;
        padding: 2rem 1rem;
        width: 90%;
        margin-top: 2rem;
        border-radius: 2rem;
        margin: auto;
        margin-top: 2rem;
    }

    h1{
        text-align: center;
    }

    h2{
        font-size: 2.5rem;
    }

    .meal{
        position: relative;
        width: 100%;
        display: flex;
    }

    .meal .img-holder img{
        width: 25vw;
        max-height: 40vh;
        float: right;
        margin-right: 2rem;
    }
    
    .meal .img-holder{
        width: 50%;
    }

</style>