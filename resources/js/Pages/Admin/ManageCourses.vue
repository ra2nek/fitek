<script setup>
    import { Head } from '@inertiajs/vue3';
    import ManagmentLayout from '@/Layouts/ManagmentLayout.vue';
    import TextInput from '@/Components/TextInput.vue';
    import Searchbar from './Components/Searchbar.vue';
    import { reactive } from 'vue';
    import axios from 'axios';
    import OutlinedButton from '@/Components/OutlinedButton.vue';
    import ConfirmDialog from '@/Components/ConfirmDialog.vue';

    import { Toaster, removeEmptyElements, convertYoutubeUrlToEmbedUrl, convertEmbedUrlToYoutubeUrl } from '../../tools.js'

    import NetworkButton from '@/Components/NetworkButton.vue';
    import SearchFiles from './Components/SearchFiles.vue';

    const storage = reactive({
        searchquery: '',
        courses: [],
        detailedCourse: null,
        addNew: true,
        showDialog: false,
        search_id: null,
        canLoadMore: false,
        files: []
    })

    const form = reactive({
        course: {
            title: null,
            description: null,
            video_url: null,
            thumbnail_url: null,
            type: null,
            category: null,
            tags: null,
        },
        tags: '',
        id: ''
    })

    async function fetchCourses() {
        storage.courses = [];

        try {

            let courses = await axios({
                method: 'get',
                url: route('courses.searchTitles', storage.searchquery),
            })
            
            if(courses.data.length > 6) {
                storage.canLoadMore = true;
                courses.data.pop();
            } else {
                storage.canLoadMore = false;
            };
            storage.courses = courses.data
        } catch (e) {
            storage.courses = [e];
        }
    }

    async function loadMoreCourses() {

        try {

            let courses = await axios({
                method: 'get',
                url: route('courses.searchTitles', storage.searchquery),
                params: {
                    offset: storage.courses.length
                }
            })
                        
            if(courses.data.length > 6) {
                storage.canLoadMore = true;
                courses.data.pop();
            } else{
                storage.canLoadMore = false;
            };

            storage.courses = storage.courses.concat(courses.data);

        } catch (e) {
            storage.courses = [e];
        }
    }

    async function fetchDetails(id){
        storage.detailedCourse = null;

        try {
            let course = await axios({
                method: 'get',
                url: route('courses.one', id)
            })

            storage.detailedCourse = course.data,
            form.course = course.data
            // Loading tags

            form.tags = ''
            let tags = JSON.parse(course.data.tags);

            tags.forEach(element => {
                form.tags += element + ', ';
            });

            // Setting header

            storage.addNew = false;
            form.course.video_url = convertEmbedUrlToYoutubeUrl(form.course.video_url);
           
            
        } catch (e){
            console.log(e);
        }   
    }

    async function Edit() {
        console.log('wrimn')
    }

    function showAddForm(){
        storage.addNew = true;

        form.tags = null;

        form.course = {
            title: null,
            description: null,
            video_url: null,
            thumbnail_url: null,
            type: null,
            category: null,
            tags: null,
            level: null,
            time: null,
        }
    }

    async function addCourse() {

        form.course.tags = form.tags.split(',');
        form.course.tags = removeEmptyElements(form.course.tags);

        form.course.video_url = convertYoutubeUrlToEmbedUrl(form.course.video_url);

        try{
            let status = await axios({
                method: 'post',
                url: route('courses.store'),
                params: {
                    ...form.course
                }
            })

            console.log(form.course);

            Toaster.success("Kurs dodano poprawnie")

            console.log(status)

        } catch( e ){

            Toaster.error(`Błąd: ${e.response.data.message}`)

            console.log(e);
        }

        form.course.video_url = convertEmbedUrlToYoutubeUrl(form.course.video_url);
        
    }

    async function updateCourse(id) {
        form.course.tags = form.tags.split(',');
        form.course.tags = removeEmptyElements(form.course.tags);

        form.course.video_url = convertYoutubeUrlToEmbedUrl(form.course.video_url);

        try{
            let status = await axios({
                method: 'put',
                url: route('courses.update', id),
                params: {
                    ...form.course
                }
            })

            Toaster.success("Kurs zaktualizowano poprawnie")

            console.log(status)

        } catch( e ){

            Toaster.error(`Błąd: ${e.response.data.message}`)

            console.log(e);
        }

        form.course.video_url = convertEmbedUrlToYoutubeUrl(form.course.video_url);

    }

    async function deleteCourse(id) {

        if(isNaN(parseInt(id))){
            console.log("ERROR: Id is not a valid number")
        }

        try{
            let status = await axios({
                method: 'put',
                url: route('courses.delete', id),
            })

            console.log(status);
        } catch( e ){
            console.log(e);
        }

        fetchCourses();
    }

</script>

<template>
    <Head title="edycja kursu" />

    <ManagmentLayout>
        <div id="pageContent">
            <section id="details">
                <h2  v-if="storage.addNew"> Dodaj nowy </h2>
                <h2 v-else >Szczegóły</h2>

                <div id="details_content">
                    <h3> Tytuł: <TextInput type="text" v-model="form.course.title" :maxlength="255" :showLimit="true" design="borderless"/> 
                        <!-- <span class="rollback text-faded pressable" @click="form.course.title = storage.detailedCourse.title"> przywróć... </span>  -->
                    </h3>
                    <h3> Opis: 
                        <textarea name="description" v-model="form.course.description"></textarea>
                    </h3>
                    <h3>
                        URL Filmu: 
                        <TextInput type="text" v-model="form.course.video_url" design="borderless"/> 
                    </h3>
                    <h3>
                        URL miniaturki:
                        <!-- <TextInput type="text" v-model="form.course.thumbnail_url" design="borderless"/>  -->

                        <!-- <Searchbar 
                            v-model="storage.searchqueryFile"
                            @input="fetchPhotos()"
                        />

                        <p v-for="file in storage.files"> {{ file }} </p> -->
                        <SearchFiles v-model="form.course.thumbnail_url" /> 

                    </h3>
                    <h3>
                        Miejsce wykonania ćwiczeń:
                        <select v-model="form.course.type">
                            <option value="home">W domu</option>
                            <option value="outside">Na zewnątrz</option>
                            <option value="gym">Na siłowni</option>
                            <option value="office">W biurze</option>
                        </select>
                    </h3>
                    <h3>
                        Kategoria:
                        <select v-model="form.course.category">
                            <option value="full body workout"> Full body workout </option>
                            <option value="legs"> Nogi </option>
                            <option value="strength"> Ręce </option>
                            <option value="strength"> Coś tam coś tam </option>
                        </select>
                    </h3>
                    <h3>
                        Tagi ( po przecinku ): 
                        <TextInput type="text" v-model="form.tags" design="borderless"/> 
                    </h3>
                    <h3>
                        Poziom trudności
                        <select v-model="form.course.level">
                            <option value="easy"> łatwy </option>
                            <option value="medium"> średni </option>
                            <option value="advanced"> zaawansowany </option>
                        </select>
                    </h3>
                    <h3>
                        Czas: 
                        <input type="number" name="time" v-model="form.course.time" ></input> <span> ( minut )</span>
                    </h3>
                        
                </div>

                <div id="details_actions">

                    <div class="btns" v-if="storage.addNew">
                        <OutlinedButton class="btn" @click="showAddForm"> Wyczyść </OutlinedButton>
                        <NetworkButton 
                            text="Dodaj"
                            class="btn"
                            :onSend="addCourse"
                        />
                    </div>

                    <div class="btns" v-else>
                        <NetworkButton 
                            text="Zaktualizuj"
                            class="btn"
                            :onSend="() => updateCourse(form.course.id)"
                        />
                    </div>

                </div>

            </section>

            <section id="options">
                <h3>Akcje: 
                    <OutlinedButton @click="showAddForm">Dodaj nowy</OutlinedButton>
                </h3>
                
            </section>

            <section id="search">
                <h2>Wyszukiwarka kursów</h2>
                <Searchbar 
                    v-model="storage.searchquery"
                    autocomplete="off"
                    @input="fetchCourses()"
                />

                <div v-if="storage.courses.length == 0"> Brak wyników wyszukiwania </div>

                <div 
                    tabindex="0" 
                    @click="fetchDetails(course.id)" 
                    v-for="(course, index) in storage.courses" 
                    :key="course.id"
                    class="result"
                    >
                    {{ index + 1 + '. ' + course.title + ' id: ' + course.id }} 

                    <button 
                        type="button" 
                        @click.stop="storage.showDialog = true; storage.search_id = course.id" 
                        class="delete-btn"
                    >
                        <span class="text-faded-hover"> usuń </span> 
                    </button>
                </div>
                <OutlinedButton
                    @click="loadMoreCourses()"
                    v-if="storage.canLoadMore"
                    id="loadMoreBTN"
                    >
                    Załaduj więcej
                </OutlinedButton>
            </section>
        </div>

        <ConfirmDialog
            v-if="storage.showDialog"
            @confirm=" deleteCourse(storage.search_id); storage.showDialog = false "
            @cancel="storage.showDialog = false"
        >
            <p> Właśnie zamierzasz usunąć kurs o id: {{ storage.search_id != null ? storage.search_id : 'No data' }}</p>
            <p> Ta akcja jest nieodwracalna i jakiekolwiek dane tego kursu zostaną usunięte </p>
        </ConfirmDialog>
    </ManagmentLayout>
</template>

<style scoped>

    #pageContent{
        padding: 3rem;
        display: grid;
        width: 100%;
        grid-template-columns: 50% 50%;
        grid-template-rows: 7rem 40rem;
    }

    section{
        border-radius: 2rem;
        background-color: white;
        margin: 1rem;
        padding: 1rem 2rem;
    }

    section:first-of-type{
        grid-area: 1 / 1 / 3 / 1;
    }

    #results{
        max-height: 80%;
        width: 100%;
        overflow-y: auto;
    }

    .result{
        cursor: pointer;
        padding: .5rem 1rem;
        border-radius: 1rem;
        margin-top: .5rem;
        position: relative;
    }

    .result:focus{
        background-color: var(--light)
    }

    .result .delete-btn{
        position: absolute;
        right: 1rem;
    }

    textarea{
        width: 100%;
        height: 10rem;
        resize: none;
    }

    #details input[type='text']{
        width: 100%;
    }

    #details h3{
        margin: 1rem;
    }

    #details_content{
        max-height: 80% ;
        overflow-y: scroll;
    }

    #details_actions{
        position: relative;
        margin: 2rem 1rem;
    }

    #details_actions .btns{
        display: flex;
        align-items: center;
        justify-content: right;
        width: 100%;
    }

    #details .btn{
        transform: scale(1.5);
        margin-left: 3rem;
    }

    #loadMoreBTN{
        width: 100%;
        margin-top: 2rem;
    }

</style>