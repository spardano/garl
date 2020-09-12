import Layout from "../components/kecamatan/layout";
import Index from "../components/kecamatan/index";
import Create from "../components/kecamatan/create";
import View from "../components/kecamatan/view";

export default [
    {
        path: '/kecamatan',
        name: 'kecamatan',
        component: Layout,
        title: 'Kecamatan',
        redirect: { name: 'kecamatan-index' },
        children: [
            {
                path: 'index',
                name: 'kecamatan-index',
                component: Index
            },
            {
                path: 'create',
                name: 'kecamatan-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'kecamatan-view',
                component: View
            }
        ]
    }
];