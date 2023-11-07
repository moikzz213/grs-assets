export const jc = [
    {
        path: "/request-asset",
        component: () => import("../../pages/normal/requestasset/List.vue"),
        name: "RequestAsset",
        meta: {
            requiresAuth: true,
            title: "request-asset",
        },
    },
    {
        path: "/request-asset/page/:page",
        component: () => import("../../pages/normal/requestasset/List.vue"),
        name: "PaginatedRequestAsset",
        meta: {
            requiresAuth: true,
            title: "request-asset",
        },
    },
    {
        path: "/request-asset/new",
        component: () => import("../../pages/normal/requestasset/NewData.vue"),
        name: "NewRequestAsset",
        meta: {
            requiresAuth: true,
            title: "request-asset",
            type: "new"
        },
    },
    {
        path: "/request-asset/update/id/:id",
        component: () => import("../../pages/normal/requestasset/EditData.vue"),
        name: "EditRequestAsset",
        meta: {
            requiresAuth: true,
            title: "request-asset",
            type: "edit"
        },
    },
];
