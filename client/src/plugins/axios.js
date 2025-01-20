import axios from 'axios'

const api = axios.create({
    withCredentials: true,
    baseURL: '/api',
    timeout: 15000,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

// Get CSRF cookie from Laravel
export const csrf = () => axios.get('/sanctum/csrf-cookie')

// Request interceptor to handle CSRF token
api.interceptors.request.use(
    (config) => {
        const token = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]

        if (token) {
            config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
        }
        return config
    },
    (error) => Promise.reject(error)
)

// Response interceptor for error handling
api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response ? error.response.status : null

        // Handle specific HTTP status codes
        switch (status) {
            case 401:
                // Unauthorized - Clear local storage and redirect to login
                localStorage.removeItem('token')
                window.location.href = '/'
                break
            case 403:
                // Forbidden
                console.error('Access forbidden')
                break
            case 404:
                console.error('Resource not found')
                break
            case 500:
                console.error('Server error')
                break
            default:
                console.error('API Error:', error)
        }

        return Promise.reject(error)
    }
)

export default api

