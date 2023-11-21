import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useStatusStore = defineStore("status", {
    state: () => ({
        status: [], 
    }),
    getters: {
        list: (state) => state.status,
        conditions: (state) => state.status.filter(s => s.type == 'condition-type'),
        assets: (state) => state.status.filter(s => s.type == 'asset'),
    },
    actions: {
        async getStatuses(token) {
            await clientKey(token)
                .get("/api/vendor/state/status-list")
                .then((res) => {
                    this.status = Object.assign([], res.data);
                    console.log("this.stats 11", this.status);
                })
                .catch((err) => {
                    console.log("getstatus error: ", err);
                });
        }, 
    },
});