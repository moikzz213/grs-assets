<template>
  <v-container class="pb-10">
    <AppPageHeader :title="`${type} Asset`" />
    <v-row>
      <div class="v-col-12">
        <AssetForm :page="type" :asset="asset" @updated="updateResponse" />
      </div>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import { clientKey } from "@/services/axiosToken";
import { useRoute } from "vue-router";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AssetForm from "@/pages/assets/AssetForm.vue";

// route
const route = useRoute();

const type = ref('edit');
// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// get asset
const asset = ref({});
const getAssetById = async () => {
  await clientKey(authStore.token)
    .get("/api/asset/" + route.params.id)
    .then((res) => {
      asset.value = res.data;
      console.log("asset.value", route.name);
      if(route.name == 'view-asset'){
        type.value = 'view';
      }
    })
    .catch((err) => {
      console.log("getAssetById error: ", err);
    });
};
getAssetById();

// updateResponse
const updateResponse = (v) => {
  console.log("updateResponse", v);
};
</script>
