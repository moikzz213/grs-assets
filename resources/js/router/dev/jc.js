export const jc = [
    {
        path: "/pv/employee-signatory/:type/approvals",
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

    {
        path: "/warranties",
        component: () => import("../../pages/admin/warranties/List.vue"),
        name: "Warranties",
        meta: {
            requiresAuth: true,
            title: "warranties",
        },
    },
    {
        path: "/warranties/page/:page",
        component: () => import("../../pages/admin/warranties/List.vue"),
        name: "PaginatedWarranties",
        meta: {
            requiresAuth: true,
            title: "warranties",
        },
    },

    /** Maintenance */
    
    {
        path: "/maintenance",
        component: () => import("../../pages/normal/maintenance/List.vue"),
        name: "Maintenance",
        meta: {
            requiresAuth: true,
            title: "maintenance",
        },
    },
    {
        path: "/maintenance/page/:page",
        component: () => import("../../pages/normal/maintenance/List.vue"),
        name: "PaginatedMaintenance",
        meta: {
            requiresAuth: true,
            title: "maintenance",
        },
    },
    {
        path: "/maintenance/new",
        component: () => import("../../pages/normal/maintenance/NewData.vue"),
        name: "NewMaintenance",
        meta: {
            requiresAuth: true,
            title: "maintenance",
            type: "new"
        },
    },
    {
        path: "/maintenance/update/id/:id",
        component: () => import("../../pages/normal/maintenance/EditData.vue"),
        name: "EditMaintenance",
        meta: {
            requiresAuth: true,
            title: "maintenance",
            type: "edit"
        },
    },
];
