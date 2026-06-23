<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import NetworkButton from '@/Components/NetworkButton.vue';


    import { Head } from '@inertiajs/vue3';
    import { reactive } from 'vue';
    import axios from 'axios';
    

    import { fetchData, Toaster } from '../../tools.js';

    const storage = reactive({
        meal: {
            title: null,
            thumbnail: null,
            meal_time: null,
            calories: 0,
            suitable_diets: [],
            ingredients: [],
            preparation: [],
            likes: 0,
            views: 0,
            time: 0,
            difficulty: null,
        }
    })

    async function getContent() {
        let split = window.location.href.split('/');
        let id = split[split.length - 1];

        const data = await fetchData( route('meals.one', id) );

        storage.meal = data;

        storage.meal.suitable_diets = JSON.parse(data.suitable_diets)
        storage.meal.ingredients = JSON.parse(data.ingredients)
        storage.meal.preparation = JSON.parse(data.preparation)
        
        console.log(meal)

    }

    getContent()

    async function addData(to, value, endpoint) {
        console.log(value)
         try {axios({
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
        } catch (e){
            Toaster.error('Bład: ' + e)
        }   
    }

    function toggleButton(id, to, value){

        let circle = document.querySelectorAll('.circle')[id];

        circle.classList.toggle('btn-active');

        addData(to, value, 'user.updateCourses');

        // addData(to, value);
    }

    async function sendMail(){
        try {
            let response = await axios({
            method: 'get',
            url: route('mail.ingredients-list'),
            params: {
                ingredients: storage.meal.ingredients,
                meal: storage.meal.title
            }
            })
            console.log(response)
            
            Toaster.success('Lista została wysłana');

            return response

        } catch(e){
            
            Toaster.error('Błąd: ' + e)

            return e;
        } 

    }
</script>

<template>
    <Head title="Posiłki" />

    <AuthenticatedLayout>

        <div class="positioner">
            <div id="meal">

                <header>
                    <h1>{{ storage.meal.title }}</h1>
                    <span>Dla diet: </span>
                    <span v-for="diet in storage.meal.suitable_diets"> {{ diet }}</span>
                </header>

                <div id="wrapper">

                    <div id="ingredients_list">

                        <h3>Składniki:</h3>
                        <ul>
                            <li v-for="ingredient in storage.meal.ingredients">
                                {{ ingredient.ingredient }}
                                -  
                                {{ ingredient.count }}
                                {{ ingredient.metric }}
                            </li>
                        </ul>
                        <br>
                        
                        <!-- <PrimaryButton @click="sendMail()" class="btn" disabled="true">Wyślij na maila</PrimaryButton> -->
                         <NetworkButton class="btn" text=" Wyślij na maila " :onSend="sendMail"/> 

                        <br>

                    </div>
                    
                    

                        <img :src="storage.meal.thumbnail" id="thumbnail">

                </div>

                <div id="steplist">
                    <div class="step" v-for="(step, index) in storage.meal.preparation">
                        <h3>Krok #{{ index + 1 }} <span v-if="step.header != null"> - {{step.header}}</span> </h3>
                        {{ step.description }}
                    </div>
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

    .positioner{
        width: 100%;
        display: flex;
        justify-content: center;
    }

    #meal{
        width: 90%;
        min-height: 50rem;
        background-color: white;
        border-radius: 2rem;
        position: relative;
        margin-top: 1rem;
        padding: 1rem 2rem;
    }

    #desc{
        margin-top: 1rem;
        width: 50%;
        padding-right: 2rem;
    }

    #wrapper{
        width: 100%;
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        position: relative;
        gap: 2rem;
    }

    #wrapper #ingredients_list{
        width: 100%;
    }

    #thumbnail {
        height: 100%;
        max-height: 30rem;
        position: relative;

        border-radius: 2rem;

        
    }

    /* HEADER */

    header span::after{
        content: ', ';
    }

    header span:first-of-type:after, header span:last-of-type:after{
        content: '';
    }

    /* LISTA SKŁADNIKÓW */

    #ingredients_list ul li{
        padding: .5rem;
    }

    #ingredients_list ul li::before{
        content: '- ';
    }

    /* PRZYCISKI */

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

    #steplist{
        margin-top: 2rem;
        text-align: center;
        display: flex;
        align-items: center;
        flex-direction: column;
        width: 100%;
    }

    .step{
        margin: 1rem;
    }


    /* Responsivness */

@media(width < 70rem){
    #wrapper{
        flex-direction: column-reverse;
    }

    #ingredients_list{
        width: 100%;
    }

    #thumbnail{
        margin-top: 2rem;
    }

    #ingredients_list h3{
        text-align: center;
    }

    #ingredients_list .btn{
        width: 100%;
        display: flex;
        justify-content: center;
    }

}


</style>