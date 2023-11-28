import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useStatusStore = defineStore("status", {
    state: () => ({
        status: [],
    }),
    getters: {
        list: (state) => state.status,
        conditions: (state) =>
            state.status.filter((s) => s.type == "condition-type"),
        assets: (state) => state.status.filter((s) => s.type == "asset"),
        urgencies: (state) => state.status.filter((s) => s.type == "urgency"),
        urgencies_active: (state) =>
            state.status.filter(
                (s) => s.type == "urgency" && s.status == "active"
            ),
    },
    actions: {
        async getStatuses(token) {
            await clientKey(token)
                .get("/api/status/state/status-list")
                .then((res) => {
                    this.status = Object.assign([], res.data);
                    console.log("this.stats 11", this.status);
                })
                .catch((err) => {
                    console.log("getstatus error: ", err);
                });
        },
        async updateStatus(statusData, token) {
            return await clientKey(token)
                .post("/api/status/state/status-update", statusData)
                .then((res) => {
                    this.getStatuses(token).then(() => {
                        return res;
                    });
                })
                .catch((err) => {
                    console.log("updateStatus error: ", err);
                });
        },
    },
});
