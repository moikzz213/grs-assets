import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useVendorStore = defineStore("vendors", {
    state: () => ({
        vendors: [],
    }),
    getters: {
        list: (state) => state.vendors,
    },
    actions: {
        async getVendors(token) {
            await clientKey(token)
                .get("/api/vendor/state/vendor-list")
                .then((res) => {
                    this.vendors = Object.assign([], res.data);
                    console.log("getVendors", this.vendors);
                })
                .catch((err) => {
                    console.log("getvendors error: ", err);
                });
        },
    },
});
