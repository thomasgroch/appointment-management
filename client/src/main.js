import './style.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import api from './plugins/axios'

const app = createApp(App)

window.axios = api
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

axios.interceptors.request.use(config => {
    const token = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1]
    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
    }
    return config
})

app.use(router)
app.mount('#app')
