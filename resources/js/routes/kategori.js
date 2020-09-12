import Layout from "../components/kategori/layout";
import Index from "../components/kategori/index";
import Create from "../components/kategori/create";
import View from "../components/kategori/view";

export default [
    {
        path: '/kategori',
        name: 'kategori',
        component: Layout,
        title: 'Kategori',
        redirect: { name: 'kategori-index' },
        children: [
            {
                path: 'index',
                name: 'kategori-index',
                component: Index
            },
            {
                path: 'create',
                name: 'kategori-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'kategori-view',
                component: View
            }
        ]
    }
];