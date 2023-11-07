import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useCompanyStore = defineStore("companies", {
    state: () => ({
        companies: [],
    }),
    getters: {
        list: (state) => state.companies,
    },
    actions: {
        async getCompanies(token) {
            await clientKey(token)
                .get("/api/company/state/list")
                .then((res) => {
                    this.companies = Object.assign([], res.data);
                })
                .catch((err) => {
                    console.log("getCompanies error: ", err);
                });
        },
    },
});
