import axios from "axios";

// sanctum api calls with bearer token
const apiUrl = import.meta.env.VITE_SANCTUM_USER_URL;
const appURL = import.meta.env.VITE_APP_URL;

const authApi = axios.create({
    baseURL: apiUrl,
    headers: { Accept: "application/json" },
});
const axiosToken = (bearer) => {
    return axios.create({
        baseURL: apiUrl,
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            Authorization: `Bearer ${bearer}`,
        },
    });
};
 
const clientKey = (bearer) => {
    return axios.create({
        baseURL: appURL,
        headers: {
            "Authorization": `Bearer ${bearer}`,
        },
    });
}
export { authApi, axiosToken, clientKey };
