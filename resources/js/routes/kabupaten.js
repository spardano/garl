import Layout from "../components/kabupaten/layout";
import Index from "../components/kabupaten/index";

export default [
    {
        path: '/kabupaten',
        name: 'kabupaten',
        component: Layout,
        title: 'Kabupaten',
        redirect: { name: 'kabupaten-index' },
        children: [
            {
                path: 'index',
                name: 'kabupaten-index',
                component: Index
            }
        ]
    }
];