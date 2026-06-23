<script setup>
    import { computed, isMemoSame, reactive, resolveComponent } from 'vue';

    const props = defineProps({
        id: {
            type: Number
        },
        url: {
            type: String
        },
        thumbnail: {
            type: String
        },
        title: {
            type: String
        },
        description: {
            type: String
        },
        views: {
            type:Number
        },
        likes: {
            type: Number
        },
        tags: {
            type: Array,
            default: []
        },
        time: {
            type: Number
        },
        level: {
            type: String
        },
        isLiked: {
            type: Boolean,
            default: false,
        },
        isMeal: {
            type: Boolean,
            default: false
        }
    });

    const storage = reactive({
        isLiked: false,
        likes: props.likes
    })

    const levels = {
        "easy": "łatwy",
        "medium": "średni",
        "mid-adv": "średnio-zaawansowany", 
        "advanced": "zaawansowany",
    }

    storage.isLiked = props.isLiked;

    const classes = computed(() => 
        props.isLiked
        ? 'mdi mdi-heart heart heart-red'
        : 'mdi mdi-heart heart'
    )

    function shortenNums(num, e){
        return Intl.NumberFormat('pl-PL', {
            notation: 'compact',
            maximumFractionDigits: 0,
            roundingMode: 'floor',
        }).format(Math.floor(num))
    }    

    function goTo(id, isMeal = false){
        if(isMeal){
            addData('RecentlyWatched', id, isMeal)
            window.location.href = route('meal', id);
        } else {
            addData('RecentlyWatched', id)
            window.location.href = route('course', id);
        }
        
    }

    // Add Data to its tab

    async function addData(to, value, isMeal) {
        if(isMeal){
            axios({
            method: 'put',
            url: route('user.updateMeals'),
            params: {
                to: to,
                value: value
            }
            }).then(function (response) {
                console.log(response)
            }).catch(function (error) {
                console.log(error)
            })  
        } else {
            axios({
            method: 'put',
            url: route('user.updateCourses'),
            params: {
                to: to,
                value: value
            }
            }).then(function (response) {
                console.log(response)
            }).catch(function (error) {
                console.log(error)
            })  
        }
        
    }

    function likeCourse(id, e, isMeal = false){
        let btn = e.target

        btn.classList.toggle('heart-red');

        if(btn.classList.contains('heart-red')) storage.likes++;
        else storage.likes--;

        addData('Liked', id, isMeal);
    }

</script>

<template>
    <div class="card" @click="goTo(id, isMeal)">
        <div class="img-holder">
            <img :src="thumbnail" :alt="title" draggable="false">
            
            <div class="tags">
                <div v-for="(tag) in tags.slice(0,3)">{{ tag }}</div>
                <div v-if="isMeal">{{title}}</div>
            </div>

            <div class="difficulty-tag" :class="level"><span class="mdi mdi-chart-line"></span>{{ levels[level] }}</div>

            <div class="outer-circle circle" id="play-circle">
                <div class="circle">
                    <div class="triangle"></div>
                </div>
            </div>

            <div class="outer-circle circle" id="like-circle" @click.stop.prevent="(event) => likeCourse(id, event, isMeal)">
                <div class="circle">
                    <span :class="classes"></span>
                </div>
            </div>
        </div>
        <h3>{{ title }}</h3>
        <!-- <p>{{ description }}</p> -->

        <div class="bottom-row">
                <span class="mdi mdi-eye"> {{ shortenNums(views) }} </span>
                <span class="mdi mdi-heart"> {{ shortenNums(storage.likes) }} </span>
                <span class="mdi mdi-clock"> {{ time + 'min' }} </span>
        </div>

    </div>


</template>

<style scoped>

    .card{
        min-width: 25rem;
        max-width: 25rem;
        height: 85%;
        background-color: white;
        position: relative;
        border-radius: 2rem;
        margin: 2rem;
        transition: all .5s ease;
        word-break: break-word;
        box-shadow: 0rem 0rem 1rem .5rem gray;
    }

    img{
        height: 100%;
        width: 100%;
        border-top-left-radius: 2rem;
        border-top-right-radius: 2rem;
    }

    .img-holder{
        height: 65%;
        width: 100%;
        position: relative;
        margin-bottom: 2rem;
    }

    .bottom-row{
        width: 100%;
        height: 10%;
        position: absolute;
        display: flex;
        justify-content: space-around;
        transform: translateX(-.5rem) translateY(10%);
        bottom: 0;
    }

    /* TAGS */

    .tags{
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, .7);

        border-top-left-radius: 2rem;
        border-top-right-radius: 2rem;

        display: flex;
        align-items: center;
        justify-content: space-evenly;

        font-size: 1.2rem;
        font-weight: 500;
        opacity: 0;

        transition: opacity .3s ease-in-out;
    }

    .card:hover{
        cursor: pointer;
    }

    .card:hover .tags{
        opacity: 1;
    }
    
    h3{
        font-weight: 900;
        font-size: 2.1rem;
        padding-left: 1rem;
        display: -webkit-box;
        line-clamp: 1;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow-y: hidden;
    }
    
    .difficulty-tag{
        position: absolute;
        bottom: 0;
        right: .5rem;

        background-color: var(--lv-gray);
        border-radius: .5rem;
        color: white;
        padding: 0 .5rem 0 .5rem;
        border: white solid .2rem;

        transform: translateY(50%);
    }

    .difficulty-tag span{
        margin-right: .5rem;
    }

    .easy{
        background-color: var(--lv-easy);
    }

    .medium{
        background-color: var(--lv-medium);
    }

    .advanced{
        background-color: var(--lv-hard);
    }

    /* play circle */

    .circle{
        border-radius: 50%;
        aspect-ratio: 1/1;
    }

    .outer-circle{
        background-color: white;
        width: 3.5rem;
        position: absolute;
        display: grid;
        place-items: center;
        transition: transform .5s ease;
    }

    #play-circle{
        bottom: 0;
        left: 5%;
        transform: translateY(50%);
    }

    #like-circle{
        bottom: 0;
        left: 25%;
        transform: translateY(50%);
    }

    .outer-circle .circle{
        background-color: var(--primary);
        width: 3rem;
        display: grid;
        place-items: center;
        transition: background-color .5s ease;
    }

    .triangle{
        height: 0;
        width: 0;
        border-top: 1rem solid transparent;
        border-bottom: 1rem solid transparent;
  
        border-left: 1.5rem solid white;
        transform: translateX(10%) scale(.9);
    }

    .outer-circle:hover{ 
        transform: scale(1.1) translateY(40%);
    }

    .outer-circle:hover .circle{
        background-color: var(--hover);
    }
    
    .heart{
        transform: scale(2);
        color: white;
    }

    .heart-red{
        color: red;
    }

    
    




</style>