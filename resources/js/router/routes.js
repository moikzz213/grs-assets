// import Dashboard from "../pages/admin/Dashboard.vue";
// import Users from "../admin//Users.vue";
// import Large from "../admin//Large.vue";
export const routes = [
    /**
     * Auth
     * https://medium.com/@ripoche.b/create-a-spa-with-role-based-authentication-with-laravel-and-vue-js-ac4b260b882f
     *
     * Auth with sanctum
     * https://techvblogs.com/blog/spa-authentication-laravel-9-sanctum-vue3-vite
     */

    /**
     * Auth Login
     */
    {
        path: "/login",
        component: () => import("../auth/SanctumLogin.vue"),
        name: "Login",
        meta: {
            requiresAuth: false,
            title: "Login",
        },
    },

    /**
     * Admin routes
     */

    {
        path: "/dashboard",
        component: () => import("../pages/admin/Dashboard.vue"),
        name: "Dashboard",
        meta: {
            requiresAuth: true,
            title: "dashboard",
        },
    },
    {
        path: "/pages",
        component: () => import("../pages/admin/slugs/Slugs.vue"),
        name: "Pages",
        meta: {
            requiresAuth: true,
            title: "pages",
        },
    },
    // {
    //     path: "/asset-list",
    //     component: () => import("../pages/admin/users/Users.vue"),
    //     name: "asset-list",
    //     meta: {
    //         requiresAuth: true,
    //         title: "asset-list",
    //     },
    // },

    // assets
    {
        path: "/asset-list",
        component: () => import("../pages/assets/Assets.vue"),
        name: "asset-list",
        meta: {
            requiresAuth: true,
            title: "assets",
        },
    },
    {
        path: "/asset-list/add",
        component: () => import("../pages/assets/AddAsset.vue"),
        name: "add-asset",
        meta: {
            requiresAuth: true,
            title: "asset",
        },
    },

    // users
    {
        path: "/users",
        component: () => import("../pages/admin/users/Users.vue"),
        name: "Users",
        meta: {
            requiresAuth: true,
            title: "users",
        },
    },
    {
        path: "/users/page/:page",
        component: () => import("../pages/admin/users/Users.vue"),
        name: "PaginatedUsers",
        meta: {
            requiresAuth: true,
            title: "users",
        },
    },

    {
        path: "/users/:id",
        component: () => import("../pages/admin/users/EditUser.vue"),
        name: "EditUser",
        meta: {
            requiresAuth: true,
            title: "users",
            type: "edit",
        },
    },
    // brands
    {
        path: "/brands",
        component: () => import("../pages/admin/brands/List.vue"),
        name: "Brands",
        meta: {
            requiresAuth: true,
            title: "brands",
        },
    },
    {
        path: "/brands/page/:page",
        component: () => import("../pages/admin/brands/List.vue"),
        name: "PaginatedBrands",
        meta: {
            requiresAuth: true,
            title: "brands",
        },
    },
    // companies
    {
        path: "/companies",
        component: () => import("../pages/admin/companies/List.vue"),
        name: "Companies",
        meta: {
            requiresAuth: true,
            title: "companies",
        },
    },
    {
        path: "/companies/page/:page",
        component: () => import("../pages/admin/companies/List.vue"),
        name: "PaginatedCompanies",
        meta: {
            requiresAuth: true,
            title: "companies",
        },
    },
    // locations
    {
        path: "/locations",
        component: () => import("../pages/admin/locations/List.vue"),
        name: "Locations",
        meta: {
            requiresAuth: true,
            title: "locations",
        },
    },
    {
        path: "/locations/page/:page",
        component: () => import("../pages/admin/locations/List.vue"),
        name: "PaginatedLocations",
        meta: {
            requiresAuth: true,
            title: "locations",
        },
    },
    // categories
    {
        path: "/categories",
        component: () => import("../pages/admin/categories/List.vue"),
        name: "Categories",
        meta: {
            requiresAuth: true,
            title: "categories",
        },
    },
    {
        path: "/categories/page/:page",
        component: () => import("../pages/admin/categories/List.vue"),
        name: "PaginatedCategories",
        meta: {
            requiresAuth: true,
            title: "categories",
        },
    },
    // models
    {
        path: "/models",
        component: () => import("../pages/admin/models/List.vue"),
        name: "Models",
        meta: {
            requiresAuth: true,
            title: "models",
        },
    },
    {
        path: "/models/page/:page",
        component: () => import("../pages/admin/models/List.vue"),
        name: "PaginatedModels",
        meta: {
            requiresAuth: true,
            title: "models",
        },
    },
    // vendors
    {
        path: "/vendors",
        component: () => import("../pages/admin/vendors/List.vue"),
        name: "Vendors",
        meta: {
            requiresAuth: true,
            title: "vendors",
        },
    },
    {
        path: "/vendors/page/:page",
        component: () => import("../pages/admin/vendors/List.vue"),
        name: "PaginatedVendors",
        meta: {
            requiresAuth: true,
            title: "vendors",
        },
    },
    // approval setup
    {
        path: "/approval-setup/:type",
        component: () => import("../pages/admin/approvalsetup/List.vue"),
        name: "Approvals",
        meta: {
            requiresAuth: true,
            title: "approval-setup",
        },
    },
    {
        path: "/approval-setup/:type/page/:page",
        component: () => import("../pages/admin/approvalsetup/List.vue"),
        name: "PaginatedApprovals",
        meta: {
            requiresAuth: true,
            title: "approval-setup",
        },
    },
    {
        path: "/approval-setup/:type/new",
        component: () => import("../pages/admin/approvalsetup/NewData.vue"),
        name: "NewApproval",
        meta: {
            requiresAuth: true,
            title: "approval-setup",
            type: "new",
        },
    },
    {
        path: "/approval-setup/:type/update/id/:id",
        component: () => import("../pages/admin/approvalsetup/EditData.vue"),
        name: "EditApproval",
        meta: {
            requiresAuth: true,
            title: "approval-setup",
            type: "edit",
        },
    },

    /**
     * Normal user routes
     */
    {
        path: "/account",
        component: () => import("../pages/account/Account.vue"),
        name: "Account",
        meta: {
            requiresAuth: true,
            title: "Account",
        },
    },

    /**
     * Normal user routes
     */
    {
        path: "/unauthorized",
        component: () => import("../pages/Unauthorized.vue"),
        name: "Unauthorized",
        meta: {
            requiresAuth: true,
            title: "Unauthorized",
        },
    },
];
