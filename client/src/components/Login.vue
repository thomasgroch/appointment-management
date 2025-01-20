<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Login</h1>

            <div class="flex items-center h-4">
                <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>
            </div>
            <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
                <input
                    v-model="email"
                    type="email"
                    placeholder="Email"
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <input
                    v-model="password"
                    type="password"
                    placeholder="Password"
                    class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                    type="submit"
                    class="bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composibles/useAuth'

const auth = useAuth()

const errorMessage = ref('')
const email = ref('')
const password = ref('')
const router = useRouter()
const { token: csrfToken, refresh: refreshToken } = inject('csrfToken')

const handleLogin = async () => {
    if (!csrfToken.value) {
        await refreshToken()
    }

    if (!email.value || !password.value) {
        errorMessage.value = 'Please enter valid credentials!'
        return
    }

    try {
        await auth.login({
            email: email.value,
            password: password.value
        })

        router.push('/dashboard')

    } catch (error) {
        console.error(error)
        errorMessage.value = error?.response?.data?.message || 'Invalid credentials!'
    }
}
</script>
