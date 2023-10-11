<template>
    <div>
        <data-form
            :objectdata="objectData" 
            :headertitle="'Update Approval Matrix'"
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

const authStore = useAuthStore();
 
const route = useRoute();
const objectData = ref({});
const getData = async () => {
   
    await clientKey(authStore.token)
        .get(
            "/api/fetch/approval-setups/single-data/" +
            route.params.id
        )
        .then((response) => {
            objectData.value = Object.assign({}, response.data);
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
