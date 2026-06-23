<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { reactive } from 'vue';

    import Card from '@/Components/Card.vue'
    import CardTape from '@/Components/CardTape.vue';

    import { fetchData } from '../../tools.js';

    import LoadingIcon from '@/Components/LoadingIcon.vue';

    const storage = reactive({
        courses: [],
        meals: [],

        liked: [],
        likedMeals: [],
    });

    const statuses = reactive({
        courses: true,
        meals: true,
    });

    async function getContent() {
        const courses = await fetchData(route('courses.all'));

        storage.courses = Object.values(courses);

        const meals = await fetchData(route('meals.all'));
        storage.meals = Object.values(meals)

        // liked courses

        let liked = await fetchData(route("user.getCourses"));
        storage.liked = liked.Liked

        statuses.courses = false;

        // liked Meals

        let likedMeals = await fetchData(route("user.getMeals"));
        storage.likedMeals = likedMeals.Liked

        statuses.meals = false;

    }

    getContent();

    //? TESTING


    function checkIfLiked(id, isMeal = false){
        let arr = isMeal ? storage.likedMeals : storage.liked;
        return arr.includes( id.toString() )
    }
    


</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <CardTape :title="'Kursy'">
            <LoadingIcon v-if="statuses.courses"/>

            <Card v-else v-for="course in storage.courses" class="video"
                :id="course.id"
                :thumbnail="course.thumbnail_url"
                :title="course.title"
                :description="course.description"
                :url="course.video_url"
                :views="course.views"
                :likes="course.likes"
                :tags="JSON.parse(course.tags)"
                :time="course.time"
                :level="course.level"

                :isLiked="checkIfLiked(course.id)"

            ></Card>
        </CardTape>

        <CardTape :title="'Posiłki'">
            <LoadingIcon v-if="statuses.meals"/>

            <Card v-else v-for="meal in storage.meals" class="video"
            
                :isMeal="true"
                :id="meal.id"
                :title="meal.title"
                :description="meal.description"
                :thumbnail="meal.thumbnail"
                :views="meal.views"
                :likes="meal.likes"
                :time="meal.time"
                :level="meal.difficulty"

                :isLiked="checkIfLiked(meal.id, true)"
            ></Card>
        </CardTape>

    </AuthenticatedLayout>

</template>
