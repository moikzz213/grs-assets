<template>
    <v-container>
        <AppPageHeader title="Global Approval Setup" />
        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <div class="mb-3">
                    <v-btn
                        :class="`${
                            isActive == 'request-asset' ? 'tab-active' : ''
                        }  v-col-12 v-col-md-3 mx-2`"
                        @click="changeType('request-asset')"
                        >Request Asset Approvals</v-btn
                    >
                    <v-btn
                        :class="`${
                            isActive == 'transfer-asset' ? 'tab-active' : ''
                        }   v-col-12 v-col-md-3 mx-2`"
                        @click="changeType('transfer-asset')"
                        >Transfer Asset Approvals</v-btn
                    >
                </div>
                <v-card>
                    <v-card-text> 
                        <v-row class="mb-3">
                            <v-col class="v-col-6 v-col-md-2 v-col-sm-12 d-flex mt-2 mb-0 pb-0"> 
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
                            <div class="v-col-12">
                                <v-card :loading="dataObj.loading">
                                    <v-table>
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-left text-capitalize cursor-pointer"
                                                    @click="
                                                        OrderByField('title')
                                                    "
                                                >
                                                    Title
                                                </th>

                                                <th
                                                    class="text-left text-capitalize cursor-pointer"
                                                    @click="
                                                        OrderByField(
                                                            'profile_id'
                                                        )
                                                    "
                                                >
                                                    Updated By
                                                </th>

                                                <th
                                                    class="text-left text-capitalize cursor-pointer"
                                                    @click="
                                                        OrderByField('status')
                                                    "
                                                >
                                                    Status
                                                </th>
                                                <th
                                                    class="text-right text-capitalize"
                                                >
                                                    <v-btn
                                                        size="small"
                                                        color="primary"
                                                        v-if="
                                                            authStore.user
                                                                .role ==
                                                                'superadmin' ||
                                                            authStore.capabilities?.includes(
                                                                'add'
                                                            )
                                                        "
                                                        :prepend-icon="mdiPlus"
                                                        @click="addNew"
                                                        >Add New Matrix</v-btn
                                                    >
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="item in dataObj.data"
                                                :key="item.id"
                                            >
                                                <td>{{ item.title }}</td>
                                                <td>
                                                    {{
                                                        item.profile
                                                            ?.display_name
                                                    }}
                                                </td>
                                                <td>
                                                    <v-chip
                                                        class="text-uppercase"
                                                        size="small"
                                                        :color="`${
                                                            item.status ==
                                                            'active'
                                                                ? 'success'
                                                                : 'error'
                                                        }`"
                                                        >{{
                                                            item.status
                                                        }}</v-chip
                                                    >
                                                </td>
                                                <td>
                                                    <div
                                                        class="d-flex align-center justify-end"
                                                    >
                                                        <v-btn
                                                            :loading="
                                                                iconLoading
                                                            "
                                                            v-if="
                                                                authStore.user
                                                                    .role ==
                                                                    'superadmin' ||
                                                                authStore.capabilities?.includes(
                                                                    'edit'
                                                                )
                                                            "
                                                            @click="
                                                                () =>
                                                                    editData(
                                                                        item.id
                                                                    )
                                                            "
                                                            :icon="mdiPencil"
                                                            title="Edit"
                                                            density="compact"
                                                            class="mx-2"
                                                        ></v-btn>
                                                        <v-btn
                                                            density="compact"
                                                            :loading="
                                                                iconLoading
                                                            "
                                                            :icon="mdiEyeOff"
                                                            v-if="
                                                                item.status ==
                                                                    'active' &&
                                                                (authStore.user
                                                                    .role ==
                                                                    'superadmin' ||
                                                                    authStore.capabilities?.includes(
                                                                        'delete'
                                                                    ))
                                                            "
                                                            @click="
                                                                () =>
                                                                    deleteData(
                                                                        item.id,
                                                                        'disabled'
                                                                    )
                                                            "
                                                            title="Disable"
                                                            class="mx-2 text-error"
                                                        >
                                                        </v-btn>
                                                        <v-btn
                                                            :loading="
                                                                iconLoading
                                                            "
                                                            density="compact"
                                                            v-if="
                                                                item.status ==
                                                                    'disabled' &&
                                                                (authStore.user
                                                                    .role ==
                                                                    'superadmin' ||
                                                                    authStore.capabilities?.includes(
                                                                        'delete'
                                                                    ))
                                                            "
                                                            @click="
                                                                () =>
                                                                    deleteData(
                                                                        item.id,
                                                                        'active'
                                                                    )
                                                            "
                                                            :icon="mdiEyeCheck"
                                                            title="Enable"
                                                            class="mx-2 text-success"
                                                        ></v-btn>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                    <v-sheet
                                        v-if="dataObj.data.length == 0"
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
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { mdiPencil, mdiEyeOff, mdiEyeCheck, mdiPlus } from "@mdi/js";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";

const sbOptions = ref({});
const authStore = useAuthStore();
const totalPageCount = ref(0);
const totalResult = ref(0); 
const showRows = ref([10, 20, 50, 100]);
const showPerPage = ref(10);

const router = useRouter();
const route = useRoute();
const filterRows = () => {
    fetchAllData();
};   

const currentPage = ref(
    route.params && route.params.page ? route.params.page : 1
    );
    
const orderBy = ref([]);
const sortBy = ref("");
const orderByCount = ref(0);
const dataObj = ref({
    loading: false,
    data: [],
});
const isActive = ref(route.params.type);
const OrderByField = (v) => {
    dataObj.value.loading = true;

    orderBy.value[0] = v;
    if (orderByCount.value % 2) {
        orderBy.value[1] = "DESC";
    } else {
        orderBy.value[1] = "ASC";
    }
    orderByCount.value++;

    sortBy.value = JSON.stringify(orderBy.value);
    fetchAllData();
};

const changeType = (type) => {
    dataObj.value.loading = true;
    dataObj.value.data = [];
    isActive.value = type;
    
    router.push({ path: "/approval-setup/"+type });
    
    fetchAllData();
};

const fetchAllData = async () => {
    dataObj.value.loading = true;
    let status = '';
    if(isActive.value == 'transfer-asset'){
        status = 'transfer';
    }else{
        status = 'request';
    }

    await clientKey(authStore.token)
        .get(
            "/api/approval-setups/all/"+status+"?page=" +
                currentPage.value +
                "&show=" +
                showPerPage.value + 
                "&sort=" +
                sortBy.value
        )
        .then((res) => {
            totalPageCount.value = res.data.last_page
                ? res.data.last_page
                : res.data.length;
            currentPage.value = res.data.current_page
                ? res.data.current_page
                : currentPage.value;
            totalResult.value = res.data.total
                ? res.data.total
                : res.data.length;
            dataObj.value.data = res.data.data ? res.data.data : res.data;
            dataObj.value.loading = false;
        })
        .catch((err) => {
            dataObj.value.loading = false;
            console.log(err);
        });
};
watch(currentPage, (newValue, oldValue) => {
    if (currentPage.value && newValue != oldValue) {
        router
            .push({
                name: "PaginatedApprovals",
                params: {
                    page: currentPage.value,
                },
            })
            .catch((err) => {});
        fetchAllData(currentPage.value);
    }
});  

const addNew = () => {
    router
        .push({ name: "NewApproval"  })
        .catch((err) => {
            console.log(err);
        });
}
const editData = (id) => {
    router
        .push({
            name: "EditApproval",
            params: {
                id: id,
            },
        })
        .catch((err) => {
            console.log(err);
        });
};

const iconLoading = ref(false);
const deleteData = async (id, status) => {
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };
    iconLoading.value = true;
    let form = {
        id: id,
        status: status,
        profile_id: authStore.user.profile.id,
    };
    await clientKey(authStore.token)
        .post("/api/approval-setups/status-change/data", form)
        .then((res) => {
            setTimeout(() => {
                sbOptions.value = {
                    status: true,
                    type: "success",
                    text: res.data.message,
                };
                fetchAllData();
                iconLoading.value = false;
            }, 600);
        })
        .catch((err) => {
            dataObj.value.loading = false;
            console.log(err);
        });
}; 
 

onMounted(() => { 
    fetchAllData();
});

</script>

<style lang="scss" scoped>
.tab-active {
    background-color: rgb(var(--v-theme-secondary)); 
}
</style>
