<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { reactive } from 'vue';

    const storage = reactive({
        course: {
            id: 0,
            title: '',
            description: '',
            url: '',
            thumbnailUrl: '',
            type: '',
            categoty: '',
            tags: [],
            level: '',
            time: '',
            views: 0,
            likes: 0
        },
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
        let split = window.location.href.split('/');
        let id = split[split.length - 1];

        const data = await fetchData( route('courses.one', id) );

        storage.course = data;

        storage.course.tags = JSON.parse(data.tags);

        const userData = await fetchData(route('user.getCourses'));

        let courses = JSON.parse(userData[0].courses);

        if(courses.Liked.includes(id)){
            let btn = document.querySelector('#like-btn');
            btn.classList.toggle('btn-active');
        }
        
        if(courses.ToWatch.includes(id)){
            let btn = document.querySelector('#toWatch-btn');
            console.log(btn)
            btn.classList.toggle('btn-active');
        }

    }

    

    getContent();

    async function addData(to, value, endpoint) {
        console.log(value)
        axios({
            method: 'put',
            url: route(endpoint),
            data: {
                to: to,
                value: value
            }
        }).then(function (response) {
            console.log(response)
        }).catch(function (error) {
            console.log(error)
        })   
    }

    function toggleButton(id, to, value){

        let circle = document.querySelectorAll('.circle')[id];

        circle.classList.toggle('btn-active');

        addData(to, value, 'user.updateCourses');

        // addData(to, value);
    }



</script>

<template>
    <Head title="twój kurs" />
    <AuthenticatedLayout>
    <div class="positioner">
        <div id="course">
            <header>
                <h1>{{ storage.course.title }}</h1>
                <div id="tags">
                    <span class="tag" v-for="tag in storage.course.tags"> {{ tag }} </span>
                </div>
            </header>

        <div id="wrapper">

            <div id="desc">
                {{ storage.course.description }}
            </div>

            <iframe
                id="video"
                :src="storage.course.video_url" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
            </iframe>

            <!-- <video muted controls>
                <source :src="storage.course.video_url" type="video/mp4" id="video">
            </video> -->
            
        </div>

        <div id="options">
            <div class="circle" @click="toggleButton(0, 'Liked', storage.course.id)" id="like-btn">
                <span class="mdi mdi-heart heart"></span>
            </div>
            <div class="circle" @click="toggleButton(1, 'ToWatch', storage.course.id)" id="toWatch-btn">
                <span class="mdi mdi-eye eye"></span>
            </div>

        </div>
        

        </div>
    </div>
    </AuthenticatedLayout>
</template>

<style scoped>
    iframe, video{
        width: 60%;
        height: 80%;
    }

    #course{
        width: 90%;
        min-height: 50rem;
        background-color: white;
        border-radius: 2rem;
        position: relative;
        margin-top: 1rem;
        padding: 1rem 2rem;
    }

    .positioner{
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .tag{
        padding: .5rem;
        border-radius: .5rem;

        width: fit-content;

        display: inline-block;
        margin-right: 1rem;
        background-color: rgba(0,0,0,.2);
    }

    #tags{
        position: relative;
        min-height: 2rem;
        width: 100%;
        gap: 1rem;
    }

    #desc{
        margin-top: 1rem;
        width: 50%;
        padding-right: 2rem;
    }

    #wrapper{
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        position: relative;
    }

    iframe{
        height: 25rem;
        width: 50%;
        border-radius: 1rem;
    }

    /* Opcje */

    #options .circle{
        width: 5rem;
        aspect-ratio: 1/1;
        border-radius: 50%;
        border: var(--primary) solid .1rem;
        display: grid;
        place-items: center;
        transition: background .5s ease;
    }

    #options .circle:hover{
        cursor: pointer;
        background-color: var(--primary);
    }

    

    .eye, .heart{
        color: var(--primary);
        transform: scale(3);
        transition: color .5s ease;
    }

    #options .circle:hover .heart, #options .circle:hover .eye{
        color: white;
    } 
    
    #options{
        margin-top: 2rem;
        width: 100%;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    /* Zmienianie stanu przycisku */

    #options .btn-active{
        background-color: var(--primary);
    }

    #options .btn-active .heart, #options .btn-active .eye{
        color: white;
    }

    #options .btn-active:hover{
        background-color: var(--primary);
        filter: brightness(.8);
    }

    #options .btn-active:hover .heart, #options .btn-active:hover .eye{
        color: var(--secondary);
    }

    @media(width < 45rem){
        #wrapper{
            flex-direction: column;
        }

        #desc, iframe{
            width: 100%;
            margin: 1rem;
        }

        h1{
            font-size: 9vmin;
            text-align: center;
            padding-bottom: 2rem;
        }

        #course{
            padding: 2rem 1rem;
        }


    }


    


</style>