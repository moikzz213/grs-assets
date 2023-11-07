import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useModelStore = defineStore("models", {
    state: () => ({
        models: [],
    }),
    getters: {
        list: (state) => state.models,
    },
    actions: {
        async getModels(token) {
            await clientKey(token)
                .get("/api/model/state/model-list")
                .then((res) => {
                    this.models = Object.assign([], res.data);
                })
                .catch((err) => {
                    console.log("getModels error: ", err);
                });
        },
    },
});
