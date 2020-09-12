import Layout from "../components/class/layout";
import Index from "../components/class/index";
import Create from "../components/class/create";
import View from "../components/class/view";

export default [
    {
        path: '/class',
        name: 'class',
        component: Layout,
        title: 'Class',
        redirect: { name: 'class-index' },
        children: [
            {
                path: 'index',
                name: 'class-index',
                component: Index
            },
            {
                path: 'create',
                name: 'class-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'class-view',
                component: View
            }
        ]
    }
];