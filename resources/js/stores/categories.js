import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useCategoryStore = defineStore("categories", {
    state: () => ({
        categories: [],
    }),
    getters: {
        list: (state) => state.categories,
    },
    actions: {
        async getCategories(token) {
            await clientKey(token)
                .get("/api/category/state/category-list")
                .then((res) => {
                    this.categories = Object.assign([], res.data);
                })
                .catch((err) => {
                    console.log("getCategories error: ", err);
                });
        },
    },
});
