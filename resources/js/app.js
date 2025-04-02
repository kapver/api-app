import './bootstrap';
import router from "./router";
import { createApp } from "vue";
import axios from 'axios';
import App from "./App.vue";

axios.defaults.baseURL = import.meta.env.VITE_API_URL;

const app = createApp(App);
app.config.globalProperties.$axios = axios;
app.use(router).mount("#app");