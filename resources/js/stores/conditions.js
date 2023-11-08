import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useConditionStore = defineStore("conditions", {
    state: () => ({
        conditions: [],
    }),
    getters: {
        list: (state) => state.conditions,
    },
    actions: {
        async getConditions(token) {
            await clientKey(token)
                .get("/api/condition/state/condition-list")
                .then((res) => {
                    this.conditions = Object.assign([], res.data);
                })
                .catch((err) => {
                    console.log("getConditions error: ", err);
                });
        },
    },
});
