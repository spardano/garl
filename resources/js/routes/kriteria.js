import Layout from "../components/kriteria/layout";
import Index from "../components/kriteria/index";
import Create from "../components/kriteria/create";
import View from "../components/kriteria/view";

export default [
    {
        path: '/kriteria',
        name: 'kriteria',
        component: Layout,
        title: 'Kriteria',
        redirect: { name: 'kriteria-index' },
        children: [
            {
                path: 'index',
                name: 'kriteria-index',
                component: Index
            },
            {
                path: 'create',
                name: 'kriteria-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'kriteria-view',
                component: View
            }
        ]
    }
];