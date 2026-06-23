<script setup>

    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

    import CardTape from '@/Components/CardTape.vue';
    import Card from '@/Components/Card.vue';
    import { Head } from '@inertiajs/vue3';

    import axios from 'axios';
    import { reactive } from 'vue';

    import { fetchData } from '../../tools.js';


    const storage = reactive({
        breakfasts: [], 
        lunchs: [], 
        dinners: []
    })

    async function getContent(params) {
        storage.breakfasts = await fetchData(route('meals.time', 'breakfast'));
        storage.lunchs = await fetchData(route('meals.time', 'lunch'));
        storage.dinners = await fetchData(route('meals.time', 'dinner'));
    }

    getContent()



</script>

<template>

    <Head title="posiłki" />

    <AuthenticatedLayout>
        <CardTape title="Śniadania">
            <Card
                v-for="meal in storage.breakfasts"

                :isMeal="true"

                :id="meal.id"
                :title="meal.title"
                :description="meal.description"
                :thumbnail="meal.thumbnail"
                :views="meal.views"
                :likes="meal.likes"
                :time="meal.time"
                :level="meal.difficulty"
                
            />
        </CardTape>
        <CardTape title="Obiady">
            <Card
                v-for="meal in storage.lunchs"

                :isMeal="true"

                :id="meal.id"
                :title="meal.title"
                :description="meal.description"
                :thumbnail="meal.thumbnail"
                :views="meal.views"
                :likes="meal.likes"
                :time="meal.time"
                :level="meal.difficulty"
                
            />
        </CardTape>
        <CardTape title="Kolacje">
            <Card
                v-for="meal in storage.dinners"

                :isMeal="true"

                :id="meal.id"
                :title="meal.title"
                :description="meal.description"
                :thumbnail="meal.thumbnail"
                :views="meal.views"
                :likes="meal.likes"
                :time="meal.time"
                :level="meal.difficulty"
                
            />
        </CardTape>
    </AuthenticatedLayout>

</template>

<style scoped>



</style>