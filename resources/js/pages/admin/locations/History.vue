<template>
    <v-container>
        <AppPageHeader title="ASSET - LOCATION LOGS" />
        <v-row class="mb-3 mt-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                 
                <v-card>
                    <v-card-text> 
                        <v-row>
                            <v-col class="v-col-12 col-sm-12  pt-0 text-center my-5">
                                <h3>{{assetData && assetData.length > 0 ? assetData[0].location.title : ''}} - ASSET - LOCATION LOGS</h3>
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <v-row class="mb-3">
                                <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                                    <v-card class="px-5">
                                        <v-row>
                                            <v-col
                                                class="v-col-6 v-col-md-2 v-col-sm-6 d-flex my-2"
                                            >
                                                <div class="mt-2 mr-2">Show</div>
                                                <v-autocomplete
                                                    :items="showRows"
                                                    v-model="showPerPage"
                                                    @update:modelValue="filterRows"
                                                    variant="outlined"
                                                    density="compact"
                                                    hide-details
                                                    label="Entry"
                                                ></v-autocomplete>
                                            </v-col>
                                        
                                        </v-row>
                                    </v-card>
                                </v-col>
                                <div class="v-col-12">
                                    <v-card :loading="dataObj.loading">
                                        <v-table>
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="text-left text-capitalize"
                                                        
                                                    >
                                                        ASSET
                                                    </th>
                                                    <th
                                                        class="text-left text-capitalize"
                                                        
                                                    >
                                                        CURRENT LOCATION
                                                    </th> 
                                                    <th
                                                        class="text-left text-capitalize"
                                                    
                                                    >
                                                        REMARKS
                                                    </th> 
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in assetData" :key="item.id">
                                                    <td class="cursor-pointer" @click="viewAsset(item.asset?.id)">{{ item.asset?.asset_name }} ( <b style="color:goldenrod">{{ item.asset?.asset_code }}</b> )</td>
                                                    <td>{{ item.asset?.location?.title }}</td>
                                                    <td>{{ item.remarks }}</td>
                                                    <td>{{ useFormatDateTime(item.created_at) }}</td>
                                                
                                                </tr>
                                            </tbody>
                                        </v-table>
                                        <v-sheet
                                            v-if="assetData.length == 0"
                                            class="pa-3 text-center w-100"
                                            >No records found</v-sheet
                                        >
                                    </v-card>
                                   
                                    <v-pagination
                                        v-if="totalPageCount > 1"
                                        v-model="currentPage"
                                        class="my-4"
                                        :length="totalPageCount"
                                        :total-visible="8"
                                        variant="elevated"
                                        active-color="primary"
                                        density="comfortable"
                                        :disabled="dataObj.loading"
                                    ></v-pagination>
                                </div>
                            </v-row>
                    </v-card-text>  
                </v-card>
            </v-col>
        </v-row> 
    </v-container>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue"; 
import { useAuthStore } from "@/stores/auth"; 
import {  useRouter, useRoute } from "vue-router"; 
import { clientKey } from "@/services/axiosToken";
import { useFormatDateTime } from "@/composables/formatDate.js";

const authStore = useAuthStore();  
const router = useRouter();

const showRows = ref([10, 20, 50, 100]);
const showPerPage = ref(20);
const route = useRoute();  
const assetData = ref([]);
const totalPageCount = ref(0);
const totalResult = ref(0);
const dataObj = ref({
    loading: false
});
const currentPage = ref(
    route.params && route.params.page ? route.params.page : 1
);

const filterRows = () => {
    getData();
};

const viewAsset = (id) => {
    let componentName = "edit-asset";
     
    router
        .push({
            name: componentName,
            params: {
                id: id,
            },
        })
        .catch((err) => {
            console.log(err);
        });
}
const getData = async () => {
    dataObj.value.loading = true;
    await clientKey(authStore.token)
        .get(
            "/api/location/fetch/"+ route.params.id +"/asset-history?page=" +
                currentPage.value + "&show="+ showPerPage.value
        )
        .then((response) => { 
            dataObj.value.loading = false;
           assetData.value = response?.data?.data; 
           totalPageCount.value = response.data.last_page ;
            currentPage.value = response.data.current_page;
            totalResult.value = response.data.total;
               
        })
        .catch((err) => {});
};

onMounted(() => {
    getData();
});

watch(currentPage, (newValue, oldValue) => {
    if (currentPage.value && newValue != oldValue) {
        router
            .push({
                name: "location-asset-log",
                params: {
                    page: currentPage.value,
                },
            })
            .catch((err) => {});
            getData(currentPage.value);
    }
});
</script>

<style lang="scss" scoped>
.tab-active {
    background-color: #c6a92d;
    color: #ffffff;
}
</style>
