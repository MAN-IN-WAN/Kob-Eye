const NotFound = { template: '<p>'+window.location.pathname+' -> Page not found</p>' }
const router = new VueRouter({
    mode: 'history',
    routes:[
        {path: '/', component: window.httpVueLoader('/Reservation/Accueil/VueAccueil.vue')},
        {path: '/sorties', component: window.httpVueLoader('/Reservation/Spectacle/VueList.vue')},
        {path: '/sorties/:url', component: window.httpVueLoader('/Reservation/Spectacle/VueFiche.vue'),
            props: {
                header: true,
                content: true
            },}
    ]
});
import Slider from '@jeremyhamm/vue-slider'
window.spec = new Vue({
    el: '#app',
    data: {
        genre: '',
        spectacles: [],
        spectacle: false,
        date: '',
        search: '',
        dirty: 0,
        render: true,
        first: true,
        currentRoute: window.location.pathname
    },
    router: router,
    components: {
        headerMenu: window.httpVueLoader('/Systeme/Header/VueMenu.vue'),
        'slider': Slider
    }
});

