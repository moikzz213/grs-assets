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
            let response = null;
            await clientKey(token)
                .get("/api/status/state/status-list")
                .then((res) => {
                    this.status = Object.assign([], res.data);
                    response = res;
                })
                .catch((err) => {
                    console.log("getstatus error: ", err);
                });
            return response;
        },
        async updateStatus(statusData, token) {
            let response = null;
            await clientKey(token)
                .post("/api/status/state/status-update", statusData)
                .then((res) => {
                    this.getStatuses(token).then(() => {
                        response = res;
                    });
                })
                .catch((err) => {
                    // console.log("updateStatus error: ", err);
                });

            return response;
        },
        async saveStatusList(statusData, token) {
            let response = null;
            await clientKey(token)
                .post("/api/status/state/save-list", statusData)
                .then((res) => {
                    this.getStatuses(token).then(() => {
                        response = res;
                    });
                })
                .catch((err) => {
                    console.log("updateStatus error: ", err);
                });

            return response;
        },
    },
});
