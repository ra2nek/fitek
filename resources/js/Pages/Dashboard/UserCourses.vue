<script setup>

    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import VideoBox from '@/Components/Card.vue';
    import CoursesHolder from '@/Components/CardTape.vue';
    import { reactive } from 'vue'
    import LoadingIcon from '@/Components/LoadingIcon.vue';

    import { fetchData } from '../../tools.js';

    const storage = reactive({

        recentlyWatched: [],
        liked: [],
        toWatch: [],

        userLiked: []

    })

    const status = reactive({
        recentlyWatched: true,
        liked: true,
        toWatch: true,
    })

    async function getContent() {

        let data = await fetchData( route('user.getCourses') );
        // Getting recently watched courses

        if(data.RecentlyWatched.length == 0){
            storage.recentlyWatched = [];
            status.recentlyWatched = false;
        } 
        else { 
            storage.recentlyWatched = [];

            let arr = data.RecentlyWatched;
            let len = arr.length

            for(let i = 0; i < len; i++){
                try{

                    let course = await fetchData( route('courses.one', parseInt(arr[len - i - 1]) ) );
                    storage.recentlyWatched.push(course);

                    status.recentlyWatched = false;

                } catch {
                    console.log('error while fetching data');
                }
            }
        }

        // Getting liked courses

        storage.userLiked = data.Liked;

        if(data.Liked.length == 0){
            storage.liked = [];
            status.liked = false;
        } 
        else { 
            storage.liked = [];

            let arr = data.Liked;
            let len = arr.length;

            for(let i = 0; i < len; i++){
                try{

                    let course = await fetchData( route('courses.one', parseInt(arr[len - i - 1]) ) );
                    storage.liked.push(course);


                } catch {
                    console.log('error while fetching data');
                }
            }

            status.liked = false;

        }

        // Getting to watch courses

        if(data.ToWatch.length == 0){
            storage.toWatch = [];
            status.toWatch = false;
        }
        else { 
            storage.toWatch = [];

            let arr = data.ToWatch;
            let len = arr.length;

            for(let i = 0; i < len; i++){
                try{

                    let course = await fetchData( route('courses.one', parseInt(arr[len - i - 1]) ) );
                    storage.toWatch.push(course);



                } catch {
                    console.log('error while fetching course data')
                }
            }

            status.toWatch = false;
        }

    }

    getContent()

    function checkIfLiked(id){
        let arr = storage.userLiked
        return arr.includes( id.toString() )
    }

</script>

<template>
    <AuthenticatedLayout>
        <CoursesHolder :title="'Ostatnio przeglądane'">
            <LoadingIcon v-if="status.recentlyWatched"/>

            <h1 v-else-if="storage.recentlyWatched.length === 0"> Brak ostatnio przeglądanych kursów / posiłków </h1>

            <VideoBox v-else v-for="course in storage.recentlyWatched" class="video"
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
            ></VideoBox>
        </CoursesHolder>

        <CoursesHolder :title="'Polubione'">
            <LoadingIcon v-if="status.liked"/>

            <h1 v-else-if="storage.liked.length === 0"> Brak polubionych kursów / posiłków </h1>

            <VideoBox v-else v-for="course in storage.liked" class="video"
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
            ></VideoBox>
        </CoursesHolder>
        
        <CoursesHolder :title="'Do obejrzenia'">
            <LoadingIcon v-if="status.toWatch == true"/>
            <h1 v-else-if="storage.toWatch.length === 0"> Brak zapisanych kursów / posiłków </h1>
            <VideoBox v-else v-for="course in storage.toWatch" class="video"
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
            ></VideoBox>
        </CoursesHolder>
    </AuthenticatedLayout>
</template>

<style scoped>

    h1{
        color: white;
    }
    
</style>