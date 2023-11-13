export const jc = [
    {
        path: "/public/employee-signatory/:type/approvals",
        component: () => import("../../pages/Approvals.vue"),
        name: "PublicApproval",
        meta: {
            requiresAuth: false,
            title: "PublicApproval",
        },
    },
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

    /**
     * Transfer Asset
     */

    {
        path: "/transfer-asset",
        component: () => import("../../pages/normal/transferasset/List.vue"),
        name: "Transferasset",
        meta: {
            requiresAuth: true,
            title: "transfer-asset",
        },
    },
    {
        path: "/transfer-asset/page/:page",
        component: () => import("../../pages/normal/transferasset/List.vue"),
        name: "PaginatedTransferAsset",
        meta: {
            requiresAuth: true,
            title: "transfer-asset",
        },
    },
    {
        path: "/transfer-asset/new",
        component: () => import("../../pages/normal/transferasset/NewData.vue"),
        name: "NewTransferAsset",
        meta: {
            requiresAuth: true,
            title: "transfer-asset",
            type: "new"
        },
    },
    {
        path: "/transfer-asset/update/id/:id",
        component: () => import("../../pages/normal/transferasset/EditData.vue"),
        name: "EditTransferAsset",
        meta: {
            requiresAuth: true,
            title: "transfer-asset",
            type: "edit"
        },
    },
];
