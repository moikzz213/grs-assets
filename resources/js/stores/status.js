import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useStatusStore = defineStore("status", {
    state: () => ({
        status: [],
        statusListByType: [], // used in Others.vue
    }),
    getters: {
        list: (state) => state.status,
        conditions: (state) =>
            state.status.filter((s) => s.type == "condition-type"),
        assets: (state) => state.status.filter((s) => s.type == "asset"),
        urgencies: (state) => state.status.filter((s) => s.type == "urgency"),
        // urgencies_active: (state) =>
        //     state.status.filter(
        //         (s) => s.type == "urgency" && s.status == "active"
        //     ),
    },
    actions: {
        async filterStatusByType(type) {
            this.statusListByType = this.status
                .filter((s) => s.type == type)
                .sort((a, b) =>
                    a.name > b.name ? 1 : b.name > a.name ? -1 : 0
                );
            return this.statusListByType;
        },
        async getStatuses(token) {
            return await clientKey(token)
                .get("/api/status/state/status-list")
                .then((res) => {
                    this.status = Object.assign([], res.data);
                    return res.data;
                })
                .catch((err) => {
                    console.log("getstatus error: ", err);
                });
        },
        async updateStatus(statusData, token) {
            return await clientKey(token)
                .post("/api/status/state/status-update", statusData)
                .then((res) => {
                    return res;
                })
                .catch((err) => {
                    console.log("updateStatus error: ", err);
                });
        },
        async saveStatusList(statusData, token) {
            return await clientKey(token)
                .post("/api/status/state/save-list", statusData)
                .then((res) => {
                    return res;
                })
                .catch((err) => {
                    console.log("updateStatus error: ", err);
                });
        },
    },
});
