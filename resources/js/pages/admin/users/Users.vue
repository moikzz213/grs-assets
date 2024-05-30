<template>
    <v-container>
        <AppPageHeader title="Users List" />
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
                        <v-spacer></v-spacer>
                        <v-col class="v-col-6 v-col-md-3 v-col-sm-6 my-2">
                            <v-text-field
                                v-model="search"
                                variant="outlined"
                                density="compact"
                                clearable
                                label="Search"
                                type="text"
                                hide-details
                                @click:append-outer="searchData"
                                @keydown.enter="searchData"
                                @click:clear="clearSearch('search')"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
            <div class="v-col-12">
                <v-card :loading="users.loading">
                    <v-table>
                        <thead>
                            <tr>
                                
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('company_id')"
                                >
                                    Company
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('location_id')"
                                >
                                    Location
                                </th>
                               
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('display_name')"
                                >
                                    Name
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('username')"
                                >
                                    Username
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('email')"
                                >
                                    Email
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('role')"
                                >
                                    Role
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('status')"
                                >
                                    Status
                                </th>
                                <th class="text-right text-capitalize">
                                    <v-btn
                                        size="small"
                                        color="primary"
                                        v-if="
                                            authStore.user.role ==
                                                'superadmin' ||
                                            authStore.capabilities?.includes(
                                                'add'
                                            )
                                        "
                                        @click="addNew"
                                        >Add New</v-btn
                                    >
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in users.data" :key="item.id">
                                
                                <td>{{ item.company?.title }}</td>
                                <td>{{ item.location?.title }}</td>
                                <td>{{ item.display_name }}</td>
                                <td>{{ item.username }}</td>
                                <td>{{ item.email }}</td>
                                <td>{{ item.role }}</td>
                                <td>
                                    <v-chip
                                        class="text-uppercase"
                                        size="small"
                                        :color="`${
                                            item.status == 'active'
                                                ? 'success'
                                                : 'error'
                                        }`"
                                        >{{ item.status }}</v-chip
                                    >
                                </td>
                               
                                <td>
                                    <div
                                        class="d-flex align-center justify-end"
                                    >
                                        <v-icon
                                            size="small"
                                            v-if="
                                                authStore.user.role ==
                                                    'superadmin' ||
                                                authStore.capabilities?.includes(
                                                    'edit'
                                                )
                                            "
                                            @click="() => editUser(item.id)"
                                            :icon="mdiPencil"
                                            class="mx-1"
                                        />
                                        <!-- <v-icon
                                            size="small"
                                            v-if="
                                                authStore.user.role ==
                                                    'superadmin' ||
                                                authStore.capabilities?.includes(
                                                    'delete'
                                                )
                                            "
                                            @click="() => deleteUser(item.id)"
                                            :icon="mdiTrashCan"
                                            class="mx-1"
                                        /> -->
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <v-sheet
                        v-if="users.data.length == 0"
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
                    :disabled="users.loading"
                ></v-pagination>
            </div>
        </v-row>

        <v-dialog
            width="600"
            persistent
            v-model="addNewDialog"
            @keydown.esc="cancelAddNew"
        >
            <v-card class="pt-3">
                <v-card-title class="text-h6 d-flex"
                    >New User <v-spacer></v-spacer
                    ><small class="text-error">{{ errorMessage }}</small>
                    <v-spacer></v-spacer
                ></v-card-title>
                <v-card-text>
                    <v-row>
                        <div class="v-col-12 v-col-md-6">
                            <v-text-field
                                :loading="ecodeLoading"
                                density="compact"
                                variant="outlined"
                                autofocus
                                label="Search Employee ID"
                                :append-inner-icon="mdiMagnify"
                                hide-details
                                v-model="employeeCode"
                                @keydown.enter="fetchUMSEcode"
                                @click:append-inner="fetchUMSEcode"
                            ></v-text-field>
                        </div>
                        <div class="v-col-12 v-col-md-6 text-caption">
                            <v-autocomplete
                                density="compact"
                                variant="outlined" 
                                label="Role"
                                :items="roleList"
                                hide-details
                                v-model="profileRole"
                            ></v-autocomplete>
                        </div> 
                        <div class="v-col-12 v-col-md-6 text-caption py-1">
                            Name: {{ profileData?.profile?.fullname }}
                        </div>
                        <div
                            class="v-col-12 v-col-md-6 text-caption my-auto py-1"
                        >
                            Status: {{ profileData.status }}
                        </div>
                        <div class="v-col-12 v-col-md-6 text-caption py-1">
                            Email:{{ profileData?.email }}
                        </div>
                        <div class="v-col-12 text-caption py-1">
                            Company: {{ profileData?.profile?.company?.title }}
                        </div>
                    </v-row>
                </v-card-text>
                <v-divider class="mt-2"></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        size="small"
                        text="cancel"
                        @click="cancelAddNew"
                    ></v-btn>
                    <v-btn
                        size="small"
                        v-if="confirmOk"
                        color="secondary"
                        @click="saveData"
                        >Confirm</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import AppPageHeader from "@/components/ApppageHeader.vue";
import { onMounted, ref, watch } from "vue";
import { mdiPencil, mdiTrashCan, mdiMagnify } from "@mdi/js";
import { useRouter, useRoute } from "vue-router";
import { clientKey, authApi } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { encryptData, decryptData } from "@/composables/encrypt";
import AppSnackBar from "@/components/AppSnackBar.vue";

const sbOptions = ref({
    status: false,
    type: "primary",
    text: null,
});

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const users = ref({
    loading: false,
    data: [],
});
const totalPageCount = ref(0);
const totalResult = ref(0);
const search = ref("");
const showRows = ref([10, 20, 50, 100]);
const showPerPage = ref(10);
const roleList = ref([
    { title: "Administrator", value: "admin" },
    { title: "Asset Supervisor-Project", value: "asset-supervisor" },
    { title: "Commercial Manager-Project", value: "commercial-manager" }, 
    { title: "Facility Team", value: "facility" },
    { title: "Normal", value: "normal" },
    { title: "Technical Operation", value: "technical-operation" },
]);
 
const filterRows = () => {
    getAllUsers();
};

const searchData = () => {
    localStorage.setItem("user-search", encryptData(search.value));
    getAllUsers();
};

const clearSearch = () => {
    search.value = "";
    localStorage.setItem("user-search", "");
    getAllUsers();
};

const currentPage = ref(
    route.params && route.params.page ? route.params.page : 1
);

const orderBy = ref([]);
const sortBy = ref("");
const orderByCount = ref(0);
const OrderByField = (v) => {
    users.value.loading = true;

    orderBy.value[0] = v;
    if (orderByCount.value % 2) {
        orderBy.value[1] = "DESC";
    } else {
        orderBy.value[1] = "ASC";
    }
    orderByCount.value++;

    sortBy.value = JSON.stringify(orderBy.value);
    getAllUsers();
};

const getAllUsers = async () => {
    users.value.loading = true;
    await clientKey(authStore.token)
        .get(
            "/api/user/all?page=" +
                currentPage.value +
                "&show=" +
                showPerPage.value +
                "&search=" +
                search.value +
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
            users.value.data = res.data.data ? res.data.data : res.data;
            users.value.loading = false;
        })
        .catch((err) => {
            users.value.loading = false;
            console.log(err);
        });
};
watch(currentPage, (newValue, oldValue) => {
    if (currentPage.value && newValue != oldValue) {
        router
            .push({
                name: "PaginatedUsers",
                params: {
                    page: currentPage.value,
                },
            })
            .catch((err) => {});
        getAllUsers(currentPage.value);
    }
});

const editUser = (id) => {
    router
        .push({
            name: "EditUser",
            params: {
                id: id,
            },
        })
        .catch((err) => {
            console.log(err);
        });
};

const deleteUser = (item) => {
    console.log("delete", item);
};

const addNewDialog = ref(false);
const addNew = () => {
    addNewDialog.value = true;
};

const confirmOk = ref(false);
const ecodeLoading = ref(false);
const employeeCode = ref("");
const profileData = ref("");
const profileRole = ref("");
const errorMessage = ref("");
const profileObject = ref({});
const fetchUMSEcode = () => {
    profileObject.value = {};
    confirmOk.value = false;
    if (!employeeCode.value) {
        return;
    }
    errorMessage.value = "";
    clientKey(authStore.token)
        .get("/api/admin/add-new/profile-by/ecode/" + employeeCode.value)
        .then((res) => {
            if (res.data.id) {
                errorMessage.value = "Profile already registered.";
                return;
            }

            authApi
                .post("/api/fetch-user/other-application", {
                    ecode: employeeCode.value,
                    key: "SFFjUDI2S1p0bUpWcit2Y21wNlJhQ1p5WndyQUR2Mnpoc0hERmt0RVBUMD0",
                    user: "TW9pa3p6q",
                    app: 'grs-asset-wvukt'
                })
                .then((res) => {
                    ecodeLoading.value = false;
                    profileData.value = res.data;
                    if (res.data?.id) {
                        profileRole.value = res.data.role;
                        confirmOk.value = true;
                        // form to be save
                        profileObject.value = res.data;
                    } else {
                        errorMessage.value =
                            "Profile not found. Contact IT department.";
                    }
                });
        });
};

const saveData = async () => {
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };
    let names = profileObject.value.profile.fullname.split(" ");
    let formObject = {
        display_name: profileObject.value.profile.fullname,
        email: profileObject.value.email,
        first_name: names[0],
        last_name: names.slice(-1)[0],
        role: profileRole.value,
        company_id: profileObject.value.profile.company_id,
        ecode: profileObject.value.profile.ecode,
        username: profileObject.value.profile.ecode,
        designation: profileObject.value.profile.position,
        profile_id: authStore.user.profile.id,
        company: profileObject.value.profile?.company?.title,
    };

    await clientKey(authStore.token)
        .post("/api/account/create-new/profile", formObject)
        .then((response) => {
            addNewDialog.value = false;
            sbOptions.value = {
                status: true,
                type: "success",
                text: response.data.message,
            };

            cancelAddNew();
            getAllUsers();

        })
        .catch((err) => {
            
        });
};

const cancelAddNew = () => {
    addNewDialog.value = false;
    profileRole.value = "";
    confirmOk.value = false;
    profileData.value = {};
    employeeCode.value = '';
    profileObject.value = {};
};
onMounted(() => {
    let vsearch = localStorage.getItem("user-search");

    if (vsearch) {
        search.value = decryptData(vsearch);
    }
    getAllUsers();
});
</script>
