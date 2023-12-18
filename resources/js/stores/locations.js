import { defineStore } from "pinia";
import { clientKey } from "../services/axiosToken.js";
export const useLocationStore = defineStore("locations", {
    state: () => ({
        locations: [],
    }),
    getters: {
        list: (state) => state.locations,
    },
    actions: {
        async getLocations(token) {
            await clientKey(token)
                .get("/api/location/state/location-list")
                .then((res) => {
                    this.locations = Object.assign([], res.data);
                })
                .catch((err) => {
                    console.log("getlocations error: ", err);
                });
        },
    },
});
