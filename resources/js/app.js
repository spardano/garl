/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

Vue.config.devtools = true

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue'));

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import ExampleComponent from "./components/ExampleComponent";

Vue.mixin({
    data: function(){
        return {
            userData: window.authUser
        }
    },
    methods: {
        goTo(name, optParam){
            if(!!this.$route.name && this.$route.name !== name){
                var opt = {name: name}
                if(optParam !== null) opt['params'] = optParam
                this.$router.push(opt)
            }
        }
    },
    delimiters: ['{%', '%}']
})
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


var baseRoute = [
    {
        path: '/',
        component: ExampleComponent,
        name: 'default',
        redirect: {name: "web"}
    }
];
var routes = baseRoute.concat(require("./routes/web").default)
routes = routes.concat(require("./routes/auth").default)
routes = routes.concat(require("./routes/kabupaten").default)
routes = routes.concat(require("./routes/kecamatan").default)
routes = routes.concat(require("./routes/kelurahan").default)
routes = routes.concat(require("./routes/kategori").default)
routes = routes.concat(require("./routes/kriteria").default)
routes = routes.concat(require("./routes/aturan").default)
routes = routes.concat(require("./routes/class").default)
routes = routes.concat(require("./routes/klasifikasi").default)
routes = routes.concat(require("./routes/maps").default)
routes = routes.concat(require("./routes/config").default)
routes = routes.concat(require("./routes/help").default)
routes = routes.concat(require("./routes/user").default)

const router = new VueRouter({routes});
router.beforeResolve((to, from, next) => {
    // If this isn't an initial page load.
    if (to.name) {
        // Start the route progress bar.
        NProgress.start()
    }
    next()
  })
  
  router.afterEach((to, from) => {
    // Complete the animation of the route progress bar.
    NProgress.done()
  })
  
const app = new Vue({
    router
}).$mount("#app");
