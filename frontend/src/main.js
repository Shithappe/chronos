import { createApp } from "vue";
import App from "./App.vue";
import router from "./router.js";
import { plugin as dialogPlugin } from 'gitart-vue-dialog'
import 'gitart-vue-dialog/dist/style.css'

createApp(App)
    .use(router)
    .use(dialogPlugin)
    .mount('#app')