import Layout from "../components/kelurahan/layout";
import Index from "../components/kelurahan/index";
import Create from "../components/kelurahan/create";
import View from "../components/kelurahan/view";

export default [
    {
        path: '/kelurahan',
        name: 'kelurahan',
        component: Layout,
        title: 'Kecamatan',
        redirect: { name: 'kelurahan-index' },
        children: [
            {
                path: 'index',
                name: 'kelurahan-index',
                component: Index
            },
            {
                path: 'create',
                name: 'kelurahan-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'kelurahan-view',
                component: View
            }
        ]
    }
];