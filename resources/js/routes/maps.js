import Layout from "../components/maps/layout";
import Index from "../components/maps/index";
import Maps from "../components/maps/maps";
import Shp from "../components/maps/shp";
import Cluster from "../components/maps/cluster";

export default [
    {
        path: '/maps',
        name: 'maps',
        component: Layout,
        title: 'Maps',
        redirect: { name: 'maps-index' },
        children: [
            {
                path: 'index',
                name: 'maps-index',
                component: Maps
            },
            {
                path: 'shp',
                name: 'maps-shp',
                component: Shp
            },
            {
                path: 'cluster',
                name: 'maps-cluster',
                component: Cluster
            }
        ]
    }
];