import Index from "../components/web/Index";
import Home from "../components/web/Home";
import Tags from "../components/web/Tags";

export default [
    {
        path: '/index',
        name: 'web',
        component: Index,
        redirect: { name: 'web-home' },
        children: [
            {
                path: '/home',
                name: 'web-home',
                component: Home
            },
            {
                path: '/tags/:tag',
                name: 'web-tags',
                component: Tags
            }
        ]
    }
];