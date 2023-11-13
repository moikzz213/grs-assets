import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useBrandStore = defineStore("brands", {
    state: () => ({
        brands: [],
    }),
    getters: {
        list: (state) => state.brands,
    },
    actions: {
        async getBrands(token) {
            await clientKey(token)
                .get("/api/brand/state/brand-list")
                .then((res) => {
                    this.brands = Object.assign([], res.data);
                })
                .catch((err) => {
                    console.log("getbrands error: ", err);
                });
        },
    },
});
