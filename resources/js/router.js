// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router';
import Login from './Pages/Auth/Login.vue'; // Ajusta la ruta al componente de inicio de sesion

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    // ... otras rutas
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;