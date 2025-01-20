import api from '../plugins/axios'
import {reactive} from 'vue'

export function useAuth() {
    const state = reactive({
        user: null,
        isAuthenticated: false,
        token: null
    })

    // Add request interceptor
    api.interceptors.request.use(
        (config) => {
            const token = state?.token || localStorage.getItem('token')
            if (token) {
                config.headers.Authorization = `Bearer ${token}`
            }
            return config
        },
        (error) => {
            return Promise.reject(error)
        }
    )

    const getCsrfToken = () => {
        const cookies = document.cookie.split(';')
        const xsrfCookie = cookies.find(cookie =>
            cookie.trim().startsWith('XSRF-TOKEN=') ||
            cookie.trim().startsWith('_csrf=')
        )
        if (!xsrfCookie) return null
        return xsrfCookie.split('=')[1].trim()
    }

    api.interceptors.request.use(config => {
      const csrfToken = getCsrfToken();
      if (csrfToken) config.headers['X-XSRF-Token'] = csrfToken;
      return config;
    });

    const setAxiosToken = (token) => {
        if (token) {
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        } else {
            delete api.defaults.headers.common['Authorization']
        }
    }

    const initializeToken = () => {
        const token = localStorage.getItem('token')
        if (token) {
            state.token = token
            setAxiosToken(token)
            return true
        }
        return false
    }

    const login = async (credentials) => {
        try {
            const { data } = await api.post('patient/login', credentials)
            if (data?.data) {
                state.user = data.data.user
                state.isAuthenticated = true
                state.token = data.data.token
                setAxiosToken(data.data.token)
                localStorage.setItem('token', data.data.token)
            }
        } catch (error) {
            console.error('Login failed', error)
            throw error
        }
    }

    const logout = async () => {
        try {
            await api.get('/logout')
            state.user = null
            state.isAuthenticated = false
            state.token = null
            setAxiosToken(null)
            localStorage.removeItem('token')
        } catch (error) {
            console.error('Logout failed', error)
        }
    }

    const checkAuth = async () => {
        try {
            const response = await api.get('/me')
            if (response.data.data) {
                state.user = response.data.data
                state.isAuthenticated = true
            }
        } catch (error) {
            state.isAuthenticated = false
        }
    }

    return {
        state,
        login,
        logout,
        checkAuth,
        initializeToken,
        setAxiosToken
    }
}
