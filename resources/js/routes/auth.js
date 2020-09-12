import Index from "../components/auth/Index";
import Login from "../components/auth/Login";
import Register from "../components/auth/Register";
import PasswordReset from "../components/auth/PasswordReset";

export default [
    {
        path: '/auth',
        name: 'auth',
        component: Index,
        title: 'Login',
        redirect: { name: 'auth-login' },
        children: [
            {
                path: 'login',
                name: 'auth-login',
                component: Login
            },
            {
                path: 'register',
                name: 'auth-register',
                component: Register
            },
            {
                path: 'password-reset',
                name: 'auth-password-reset',
                component: PasswordReset
            }
        ]
    }
];