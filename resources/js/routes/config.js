import Layout from "../components/config/layout";
import Help from "../components/config/help";

export default [
    {
        path: '/config',
        name: 'config',
        component: Layout,
        title: 'config',
        redirect: { name: 'config-help' },
        children: [
            {
                path: 'help',
                name: 'config-help',
                component: Help
            }
        ]
    }
];