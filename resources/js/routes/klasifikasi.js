import Layout from "../components/klasifikasi/layout";
import Index from "../components/klasifikasi/index-new";
import View from "../components/klasifikasi/view";

export default [
    {
        path: '/klasifikasi',
        name: 'klasifikasi',
        component: Layout,
        title: 'Klasifikasi',
        redirect: { name: 'klasifikasi-index' },
        children: [
            {
                path: 'index',
                name: 'klasifikasi-index',
                component: Index
            },
            {
                path: 'view/:id',
                name: 'klasifikasi-view',
                component: View
            }
        ]
    }
];