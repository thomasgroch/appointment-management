import {createRouter, createWebHistory} from 'vue-router'
import {useAuth} from './composibles/useAuth'

const auth = useAuth()

function getCookie(name) {
    const cookies = document.cookie.split('; ')
    const cookie = cookies.find(row => row.startsWith(`${name}=`))
    return cookie ? cookie.split('=')[1] : null
}

const routes = [
    {
        path: '/',
        component: () => import('./components/Login.vue'),
        alias: '/login',
        meta: {requiresGuest: true}
    },
    {
        path: '/dashboard',
        component: () => import('./components/Dashboard.vue'),
        meta: {requiresAuth: true}
    },
    {
        path: '/logout',
        name: 'logout',
        meta: {requiresAuth: true},
        beforeEnter: async (to, from, next) => {
            const auth = useAuth()
            try {
                await auth.logout()
                next('/')
            } catch (error) {
                console.error('Logout failed:', error)
                next(false)  // Stay on current page if logout fails
            }
        }
    },
    {
        path: '/:pathMatch(.*)*',
        component: () => import('./components/NotFound.vue'),
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach(async (to, from, next) => {
    const auth = useAuth()

    // Initialize token if exists
    if (!auth.state.isAuthenticated) {
        auth.initializeToken()
    }

    try {
        // Check authentication status if token exists
        if (to.meta.requiresAuth && auth.state.token) {
            await auth.checkAuth()
        }

        // Handle requiresAuth routes
        if (to.meta.requiresAuth && !auth.state.isAuthenticated) {
            return next({path: '/'})
        }

        // Handle requiresGuest routes
        if (to.meta.requiresGuest && auth.state.isAuthenticated) {
            return next({path: '/dashboard'})
        }

        // Allow navigation
        return next()
    } catch (error) {
        console.error('Navigation guard error:', error)
        return next({path: '/'})
    }
})

export default router
