import Layout from "../components/config/layout";
import Help from "../components/config/help-index";

export default [
    {
        path: '/help',
        name: 'help',
        component: Layout,
        title: 'help',
        redirect: { name: 'help-index' },
        children: [
            {
                path: '/',
                name: 'help-index',
                component: Help
            }
        ]
    }
];