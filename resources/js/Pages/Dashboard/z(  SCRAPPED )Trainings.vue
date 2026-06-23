<script setup>

    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { reactive } from 'vue';

    import VideoBox from '@/Components/Card.vue'
    import CoursesHolder from '@/Components/CardTape.vue';
    import { fetchData } from '../../tools.js';


    const storage = reactive({
        courses: [],
        title: 'home'
    })

    async function getContent(type) {
        const data = await fetchData(route('courses.type', type));

        storage.courses = Object.values(data);

        console.log(Object.values(data)) 

        storage.title = type == 'gym' ? 'Siłownia' : 'Domowe';

        window.location.href = route('trainings') + '#courses';
    }


</script>

<template>
    <Head :title="'treningi'" />

    <AuthenticatedLayout>
        <div class="wrapper">
            <a class="card" @click="getContent('gym')" href="#courses">
                <img src="https://www.trec.pl/media/produkt_foto/trening-cardio-zasady-zalozenia-zalety-trening-cardio-zasady-zalozenia-zalety-k0.jpeg.720x440_q85_crop.jpg" alt="obraz#1">
                <h1>Treningi na siłowni</h1>
                <p>Poznaj nasze plany treningowe</p>
            </a>
            <a class="card" @click="getContent('home')" href="#courses">
                <img src="https://www.bcaa.pl/images/37/1007-a2.jpg" alt="obraz#2">
                <h1>Treningi w domu</h1>
                <p>Zachwyć się naszymi treningami w domowym zaciszu</p>
            </a>
        </div>
        <div class="courses" v-if="storage.courses.length != 0" id="courses">
            <div id="content">
            <CoursesHolder :title="storage.title">
                <VideoBox v-for="course in storage.courses" class="video"

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
    
                ></VideoBox>
                <!-- :isLiked="checkIfLiked(course.id)" 
                 
                -->
            </CoursesHolder>
        </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
    .wrapper{
        display: flex;
        justify-content: space-evenly;
        width: 100%;
        min-height: 40rem;
        margin-top: 2rem;
    }

    .card{
        width: 45%;
        border-radius: 2rem;
        background-color: white;
        padding: 1.5rem;
        transition: transform .3s ease, color .5s ease;
    }

    .card img{
        width: 100%;
        height: 70%;
        border-top-left-radius: 2rem;
        border-top-right-radius: 2rem;
    }

    .card:hover{
        transform: scale(1.05);
        cursor: pointer;
    }

    @media (width <= 50rem) {
        .wrapper{
            flex-direction: column;
            padding: 0 2rem;
        }

        .card{
            width: 100%;
            margin-bottom: 2rem;
        }
    }
</style>