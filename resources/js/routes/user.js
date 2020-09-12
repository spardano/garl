import Layout from "../components/user/layout";
import Index from "../components/user/index";
import Create from "../components/user/create";
import View from "../components/user/view";

export default [
    {
        path: '/user',
        name: 'user',
        component: Layout,
        title: 'User',
        redirect: { name: 'user-index' },
        children: [
            {
                path: 'index',
                name: 'user-index',
                component: Index
            },
            {
                path: 'create',
                name: 'user-create',
                component: Create
            },
            {
                path: 'view/:id',
                name: 'user-view',
                component: View
            }
        ]
    }
];