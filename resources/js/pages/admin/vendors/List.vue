<template>
    <v-container>
        <AppPageHeader title="Vendor List" />
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
                <v-card :loading="dataObj.loading">
                    <v-table>
                        <thead>
                            <tr>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('category_id')"
                                >
                                    Category
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('title')"
                                >
                                    Vendors
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('brand')"
                                >
                                    Brand
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('origin')"
                                >
                                    Origin
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('contact_name')"
                                >
                                    ContactName
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('contact_email')"
                                >
                                    Email
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('contact_no')"
                                >
                                    Contact
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('address')"
                                >
                                    Address
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
                            <tr v-for="item in dataObj.data" :key="item.id">
                                <td>{{ item.category?.title }}</td>
                                <td>{{ item.title }}</td>
                                <td>{{ item.brand }}</td>
                                <td>{{ item.origin }}</td>
                                <td>{{ item.contact_name }}</td>
                                <td>{{ item.contact_email }}</td>
                                <td>{{ item.contact_no }}</td>
                                <td>{{ item.address }}</td>

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
                                        <v-btn
                                            :loading="iconLoading"
                                            v-if="
                                                authStore.user.role ==
                                                    'superadmin' ||
                                                authStore.capabilities?.includes(
                                                    'edit'
                                                )
                                            "
                                            @click="() => editData(item)"
                                            :icon="mdiPencil"
                                            title="Edit"
                                            density="compact"
                                            class="mx-2"
                                        ></v-btn>
                                        <v-btn
                                            density="compact"
                                            :loading="iconLoading"
                                            :icon="mdiEyeOff"
                                            v-if="
                                                item.status == 'active' &&
                                                (authStore.user.role ==
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
                                            :loading="iconLoading"
                                            density="compact"
                                            v-if="
                                                item.status == 'disabled' &&
                                                (authStore.user.role ==
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
        <DialogForm
            :free-form="freeForm"
            :add-new-dialog="addNewDialog"
            title="Vendor"
            :data-object="dataObject"
            :is-view="false"
            @cancelled="cancelledAction"
            @save="saveData"
        />
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import AppPageHeader from "@/components/ApppageHeader.vue";
import { onMounted, ref, watch, computed } from "vue";
import { mdiPencil, mdiEyeOff, mdiEyeCheck } from "@mdi/js";
import { useRouter, useRoute } from "vue-router";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { encryptData, decryptData } from "@/composables/encrypt";
import AppSnackBar from "@/components/AppSnackBar.vue";
import DialogForm from "@/components/DialogForm.vue";
const sbOptions = ref({
    status: false,
    type: "primary",
    text: null,
});

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const dataObj = ref({
    loading: false,
    data: [],
});
const totalPageCount = ref(0);
const totalResult = ref(0);
const search = ref("");
const showRows = ref([10, 20, 50, 100]);
const showPerPage = ref(10);

const filterRows = () => {
    fetchAllData();
};

const searchData = () => {
    localStorage.setItem("vendor-search", encryptData(search.value));
    fetchAllData();
};

const clearSearch = () => {
    search.value = "";
    localStorage.setItem("vendor-search", "");
    fetchAllData();
};

const currentPage = ref(
    route.params && route.params.page ? route.params.page : 1
);

const orderBy = ref([]);
const sortBy = ref("");
const orderByCount = ref(0);
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

const fetchAllData = async () => {
    dataObj.value.loading = true;
    await clientKey(authStore.token)
        .get(
            "/api/vendors/all?page=" +
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
            dataObj.value.data = res.data.data ? res.data.data : res.data;
            dataObj.value.loading = false;
        })
        .catch((err) => {
            dataObj.value.loading = false;
        });
};
watch(currentPage, (newValue, oldValue) => {
    if (currentPage.value && newValue != oldValue) {
        router
            .push({
                name: "PaginatedVendors",
                params: {
                    page: currentPage.value,
                },
            })
            .catch((err) => {});
        fetchAllData(currentPage.value);
    }
});

const cancelledAction = (v) => {
    dataObject.value = {};
    addNewDialog.value = v;
};

const saveData = async (data) => {
    addNewDialog.value = false;
    data.profile_id = authStore.user.profile.id;
    await clientKey(authStore.token)
        .post("/api/vendors/store-update/data", data)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: res.data.message,
            };
            fetchAllData();
        })
        .catch((err) => {
            dataObj.value.loading = false;
            console.log(err);
        });
};

import { useCategoryStore } from "@/stores/categories";
const categoryStore = useCategoryStore();
if (categoryStore.list.length == 0) {
    categoryStore.getCategories(authStore.token);
}

const dataObject = ref({});

const freeForm = computed(() => {
    return [
        {
            name: "category_id",
            label: "Category",
            required: true,
            type: "select",
            data: categoryStore.list,
        },
        { name: "title", label: "Vendor Name", required: true, type: "text" },
        { name: "brand", label: "Brand", required: false, type: "text" },
        { name: "origin", label: "Origin", required: false, type: "text" },
        { name: "contact_name", label: "Name", required: true, type: "text" },
        {
            name: "designation",
            label: "Designation",
            required: false,
            type: "text",
        }, 
        {
            name: "contact_email",
            label: "Email",
            required: true,
            type: "email",
        },
        {
            name: "contact_no",
            label: "Contact",
            required: false,
            type: "number",
        },
        { name: "address", label: "Address", required: false, type: "text" },
        { name: "remarks", label: "Remarks", required: false, type: "textarea" },
    ];
});

const editData = (data) => {
    dataObject.value = Object.assign({}, data);
    dataObject.value.type = "vendor";
    addNewDialog.value = true;
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
        .post("/api/vendors/status-change/data", form)
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
        });
};

const addNewDialog = ref(false);
const addNew = () => {
    dataObject.value = "";
    addNewDialog.value = true;
};

onMounted(() => {
    let vsearch = localStorage.getItem("vendor-search");
    if (vsearch) {
        search.value = decryptData(vsearch);
    }
    fetchAllData();
});
</script>
