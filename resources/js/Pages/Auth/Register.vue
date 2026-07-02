<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    checkbox: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

function closeModal(){
    document.getElementById("modal").style.display = "none";
}

function openModal(){
    document.getElementById("modal").style.display = "flex";
}

</script>

<template>
    <GuestLayout>
        <Head title="Rejestracja" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nazwa użytkownika" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Hasło" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Potwierdź hasło" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4 flex gap-2">
                <Checkbox id="accept_tos" required/>

                <InputLabel>Akceptuję <span class="focus-text underline cursor-pointer" @click="openModal()">politykę prywatności</span></InputLabel>

                <InputError class="mt-2" :message="form.errors.checkbox" />
            </div> 

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md"
                >
                    Masz już konto?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Zarejestruj się
                </PrimaryButton>
            </div>
        </form>

        <div id="modal" class="w-full h-full" @click.self="closeModal()">
            <div id="TOS">
                <h3 class="absolute top-1 right-4 cursor-pointer" @click="closeModal()">zamknij</h3>
                <h2>Polityka prywatności</h2>
                <p>1. <span class="bold">Administratorem</span> danych osobowych jest <span class="bold">Dawid Domaniecki</span> zamieszkały pod adresem Sulejewo 12, 63-112 Sulejewo. Z administratorem danych można kontaktować się telefonicznie pod numerem: +48 537 316 576, lub adresu email: dawid.domaniecki2@gmail.com.</p>
                <p>2. <span class="bold">Celem przetwarzania danych jest:</span>
                    <ol>
                        <li>a) Umożliwienie logowania oraz identyfikacji użytkownika posiadającego konto w serwisie.</li>
                        <li>b) Przesyłanie informacji dotyczących składników potrzebnych do przygotowania wybranego posiłku drogą elektroniczną. [ art. 6 ust. 1 lit. RODO (wykonanie usługi na żądanie użytkownika) ].</li>
                    </ol>
                </p>
                <p>3. <span class="bold">Odbiorcą danych</span> jest dostawca hostingu <span class="bold">SEOHOST Sp. z o.o</span>, którego polityka prywatności znajduje się pod adresem: <a href="https://seohost.pl/polityka-prywatnosci" target="_blank">https://seohost.pl/polityka-prywatnosci.</a></p>
                <p>4.  <span class="bold">Dane osobowe są przechowywane do momentu</span> w którym przestają być one konieczne do świadczenia usługi, przez co rozumie się moment usunięcia konta poprzez serwis, na prośbę założyciela konta, lub osoby której dane dotyczą po wystosowaniu żądania usunięcia danych do administratora, chyba że przepisy prawa wymagają ich dalszego przechowywania..</p>
                <p>5. <span class="bold">Użytkownik ma prawo:</span> 
                <ol>
                    <li>a) Prawo do dostępu do swoich danych.</li>
                    <li>b) Prawo do sprostowania swoich danych w razie ich niezgodności z stanem rzeczywistym, ich usunięcia lub ograniczenia przetwarzania.</li>
                    <li>c) Prawo do cofnięcia zgody w dowolnym momencie.</li>
                    <li>d) Prawo do wniesienia skargi do Prezesa UODO.</li>
                    <li>e) Prawo do przenoszenia danych.</li>
                </ol>
                </p>
                <p>6. <span class="bold">Pliki cookies:</span>
                Serwis wykorzystuje pliki cookies w celu utrzymania sesji użytkownika oraz zapewnienia prawidłowego działania procesu logowania.</p>
                <p>7.  <span class="bold">Podanie danych osobowych</span> jest niezbędne do założenia konta oraz korzystania z funkcjonalności serwisu. Niepodanie wymaganych danych uniemożliwi korzystanie z usług wymagających rejestracji.</p>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
    #modal{
        background-color: rgba(0, 0, 0, .4);
        backdrop-filter: blur(5px);
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;

        display: none;
        
    }

    #TOS{
        width: 80%;
        height: 80%;
        background-color: white;
        border-radius: 2rem;
        overflow-y: auto;
        padding: 1rem 2rem;
        position: relative;
    }

    #TOS p{
        padding-bottom: 1rem;
    }

    #TOS a{
        color: var(--secondary);
    }

    @media(width < 1100px){
        #TOS{
            width: 90%;
            height: 90%;
        }
    }

    ::-webkit-scrollbar {
        position: absolute;
        width: .5rem;
        right: 1rem;
    }
    
    /* Track */
    ::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 1rem;
    }
    
    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 1rem;
    }
    
    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    ::-webkit-scrollbar-track-piece:end {
        background: transparent;
        margin-bottom: 3rem; 
    }
    
    ::-webkit-scrollbar-track-piece:start {
        background: transparent;
        margin-top: 3rem;
    }


</style>