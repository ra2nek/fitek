import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

export const $toast = useToast();

export const diets = [
    'podstawowa',
    'wege',
    'gluten',
    'hashimoto',
    'keto',
    'insulinoodporność',
    'office',
    'sport'
];

class ToasterActions {

    success = function(message) {
        $toast.open({
            message: message,
            type: 'success',
            position: 'top'
        })
    };

    error = function(message) {
        $toast.open({
            message: message,
            type: 'error',
            position: 'top'
        })
    };

    info = function(message) {
        $toast.open({
            message: message,
            type: 'info',
            position: 'top'
        })
    };

}

export const Toaster = new ToasterActions;

export async function fetchData( endpoint ) {
    try{
        const response = await axios.get(endpoint)

        return response.data
    } catch(error){
        console.error(`Error fetchin ${endpoint}: `, error);
        Toaster.error(`Błąd wczytywania danych`)
        return [];
    }
}

export function removeEmptyElements(array){
    array.forEach((value, index) => {
        if(value.trim() == ''){
            array.splice(index, 1);
        }
    })

    return array;
}

export function reroute(url_name){
    window.location.href = route(url_name);
}

export function convertYoutubeUrlToEmbedUrl(url){

    if(url.includes("youtube.com")){
        url = url.replace("watch?v=", "embed/");
        if(url.includes('&')){
            url = url.slice(0, url.indexOf('&'))
        }
        return url;
    }

    
    let arr = url.split('/');
    let id = arr[arr.length - 1];
    if(id.includes('?')){
        id = id.slice(0, id.indexOf('?'));
    }
    url = `https://www.youtube.com/embed/${id}`;

    return url;

}

export function convertEmbedUrlToYoutubeUrl(url){

    let id = url.split('/');
    id = id[id.length - 1];

    return `https://www.youtube.com/watch?v=${id}`;


}