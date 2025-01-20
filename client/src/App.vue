<template>
    <div class="min-h-screen bg-gray-100">
        <meta name="csrf-token" :content="csrfToken">
        <router-view></router-view>
    </div>
</template>

<script setup>
import {ref, onMounted, provide} from 'vue'
import axios from 'axios'

const csrfToken = ref('')

const fetchCsrfToken = async () => {
    try {
        await axios.get('sanctum/csrf-cookie')
        const token = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
        if (token) {
            csrfToken.value = decodeURIComponent(token)
        }
    } catch (error) {
        console.error('Failed to fetch CSRF token:', error)
    }
}

provide('csrfToken', {
    token: csrfToken,
    refresh: fetchCsrfToken
})

onMounted(async () => {
    await fetchCsrfToken()
})
</script>
