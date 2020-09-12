import Layout from "../components/aturan/layout";
import Index from "../components/aturan/index";
import Create from "../components/aturan/create";
import View from "../components/aturan/view";

export default [
    {
        path: '/aturan',
        name: 'aturan',
        component: Layout,
        title: 'Aturan',
        redirect: { name: 'aturan-index' },
        children: [
            {
                path: 'index',
                name: 'aturan-index',
                component: Index
            },
            {
                path: 'create',
                name: 'aturan-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'aturan-view',
                component: View
            }
        ]
    }
];