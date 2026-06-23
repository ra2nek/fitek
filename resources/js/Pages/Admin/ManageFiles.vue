<script setup>

    import ManagmentLayout from '@/Layouts/ManagmentLayout.vue';

    import Searchbar from './Components/Searchbar.vue';
    import ConfirmDialog from '@/Components/ConfirmDialog.vue';

    import NetworkButton from '@/Components/NetworkButton.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    
    import TextInput from '@/Components/TextInput.vue';
    import Checkbox from '@/Components/Checkbox.vue'; 
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';


    import axios from 'axios';

    import { useForm, Head } from '@inertiajs/vue3';

    import { Toaster } from '@/tools.js'
    import { reactive } from 'vue';


    const form = useForm({
        name: '',
        file: null,
        // compress: true,
    })

    const handleFileChange = (event) => {
        form.file = event.target.files[0];
    };

    async function uploadForm() {
        // form.file.name = changeName(form.file.name, form.file);

        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('file', form.file);

        console.log(formData);

        try {
            let status = await axios.post(route('storage.upload_image'), formData,  {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
            console.log("File uploaded successfully", status);
            Toaster.success("Plik został przesłany poprawnie");
        } catch ( e ) {
            console.error( "Upload failed", e );
            Toaster.error( "Wystąpił błąd z przesyłaniem pliku. Błąd " + e.status + ": "+ e.response.data.message )
        }
    }

    const storage = reactive({
        searchquery: '',
        results: [],
        selectedPhoto: '',
        showDialog: false,
        filename: null,
        fileID: null,
    })

    async function fetchPhotos() {
        storage.results = [];
        if(storage.searchquery.trim() == '') return;

        try{
            let status = await axios.get(route('storage.search', storage.searchquery))
            storage.results = status.data;
            console.log(storage.results);
        } catch ( e ){
            console.log(e);
        }
    }

    async function destroyPhoto() {
        try{

            let status = await axios.delete(route('storage.destroy', [storage.fileID, storage.filename]))
            storage.selectedPhoto = '';
            storage.filename = null;
            storage.fileID = null;

        } catch(e){
            console.log(e);
        }

        await fetchPhotos();

    }

</script>

<template>
    <Head title="Pliki" /> 

    <ManagmentLayout>

        <div id="page">

            <section>

                <h2> Dodaj plik </h2>

                <form method="post" @submit.prevent id="uploadForm" class="form">
                    <InputLabel> Nazwa pliku: <TextInput max="100" v-model="form.name" /> </InputLabel>
                    <InputError v-if="form.errors.name"> {{ form.errors.name }}</InputError>
                    <InputLabel> Plik: <div class="fileUpload"> Wybierz plik </div> <span>Wybrany plik: {{ form.file == null ? '*brak*' : form.file.name }}</span> <input type="file" @change="handleFileChange" ></InputLabel>
                    <!-- <InputLabel> Kompresuj obraz: <Checkbox v-model="form.compress" :checked="form.compress"/> </InputLabel> -->
                    <NetworkButton :onSend="uploadForm" text="Prześlij plik" />
                </form>
        
            </section>

            <section>

                <h2> Wyszukaj plik </h2>

                <Searchbar
                    v-model="storage.searchquery"
                    autocomplete="off"
                    @input="fetchPhotos()"
                />

                <div id="results">
                    <h2 v-if="storage.results.length == 0" class="result"> Brak wyników wyszukiwania </h2>

                    <div class="result" 
                        v-for="(file, index) in storage.results"
                        :key="index"
                        tabindex="0"
                        @click="storage.selectedPhoto = file.url"
                        > {{index + 1}}. {{file.title}}
                    
                        <button 
                        type="button" 
                        @click.stop="storage.showDialog = true; storage.filename = file.title; storage.fileID = file.id" 
                        class="delete-btn">
                        <span class="text-faded-hover"> usuń </span> 
                    </button>
                    </div>

                </div>

                <div id="photoPreview">
                    <h3>Podgląd zdjęcia</h3>
                    <p v-if="storage.selectedPhoto.trim() == '' "> *Brak wybranego zdjęcia*</p>
                    <img :src="storage.selectedPhoto" v-else>
                </div>

        
            </section>

        </div>

        <ConfirmDialog
            v-if="storage.showDialog"
            @confirm=" destroyPhoto(); storage.showDialog = false "
            @cancel="storage.showDialog = false">

            <p> Właśnie zamierzasz usunąć plik o nazwie: {{ storage.filename != null ? storage.filename : 'No data' }}</p>
            <p> Ta akcja jest nieodwracalna i jakiekolwiek dane tego kursu zostaną usunięte </p>

        </ConfirmDialog>


    </ManagmentLayout>
</template>

<style scoped>

    #page{
        display: flex;
        align-items: center;
        justify-content: space-around;
        position: relative;
    }

    section{
        background-color: white;
        padding: 1rem;
        margin: 2rem;
        border-radius: 1rem;
        width: 50%;
        height: 45rem;
        position: relative;
    }

    input[type="file"] {
        display: none;
    }
      
    .fileUpload {
        border: 1px solid var(--primary);
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
    }

    .form{
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    #results{
        height: 50%;
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
        background-color: var(--light);
    }

    .result .delete-btn{
        position: absolute;
        right: 1rem;
    }

    #photoPreview{
        height: 25%;
    }

    #photoPreview img{
        height: 100%;
        width: auto;
        object-fit: contain;
    }

</style>