<template>
    <div>
        <AppSnackBar :options="sbOptions" />
        <data-form v-if="isLoaded"
            :objectdata="objectData" 
            :headertitle="'Update Request'"
            @saved="savedResponse"
        ></data-form>
    </div>
</template>

<script setup>
 
import { onMounted, ref } from "vue";
import DataForm from "./DataForm.vue";
import { useRoute } from "vue-router";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import AppSnackBar from "@/components/AppSnackBar.vue";
const authStore = useAuthStore();
 
const sbOptions = ref({});
const isLoaded = ref(false);
const route = useRoute();
const objectData = ref({});
const getData = async () => {
    sbOptions.value = {
        status: true,
        type: "info",
        text: "loading...",
    };
    await clientKey(authStore.token)
        .get(
            "/api/fetch/request-assets/by-requestor/data/" +
            route.params.id
        )
        .then((response) => {
            objectData.value = Object.assign({}, response.data); 
            isLoaded.value = true;
            sbOptions.value = {
                status: false,                
            };
        })
        .catch((err) => {});
};
const savedResponse = () => {
    getData();
};
onMounted(() => { 
getData();
});
</script>
