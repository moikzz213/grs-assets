<template>
    <v-container class="no-print" v-if="!dialogPrintBarcoce">
        <AppPageHeader title="ASSET list page" />
        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <v-card class="px-5 mb-1">
                    <v-card-text>
                        <v-row>
                            <div class="v-col-12 v-col-md-8 d-flex">
                                <v-btn
                                    color="primary"
                                    class="mx-2"
                                    @click="addNew('add-asset')"
                                    v-if="
                                            authStore.user.role ==
                                                'superadmin' ||
                                            authStore.capabilities?.includes(
                                                'add'
                                            )
                                        "
                                    >Add new asset</v-btn
                                >
                                <v-btn
                                    color="primary"
                                    class="mx-2"
                                    @click="addNew('NewTransferAsset')"
                                    >transfer asset</v-btn
                                >
                                <v-btn
                                    color="primary"
                                    class="mx-2"
                                    @click="addNew('NewRequestAsset')"
                                    >request asset</v-btn
                                >
                            </div>
                            <div class="v-col-12 v-col-md-4 d-flex justify-end">
                                <v-btn
                                    color="primary"
                                    class="mx-2"
                                    :disabled="!enablePrintBarcode"
                                    @click="printBarcodesFn"
                                    >print barcodes({{ draftPrints.length }})</v-btn
                                >
                                <DownloadExcel
                                    v-if="dataObj.data?.length > 0"
                                    :fetch="donwloadLeads"
                                    :fields="json_field"
                                    worksheet="Report"
                                    name="Assets.csv"
                                    type="csv"
                                    disabled
                                    class="mr-3"
                                >
                                    <v-btn 
                                        color="success"
                                        :disabled="
                                            Object.keys(dataObj.data).length == 0
                                                ? true
                                                : false
                                        "
                                        :loading="btnLoading"
                                        dark
                                    >
                                        Download
                                    </v-btn>
                                </DownloadExcel>
                            </div>
                            <v-divider class="mb-2"></v-divider>
                        </v-row>
                        <v-row>
                            <div class="my-auto">Filter</div>
                            <div class="v-col-md-2 py-1">
                                <v-autocomplete
                                    :items="companyList"
                                    v-model="objFIlter.company_id"
                                    @update:modelValue="filterSearch"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    clearable
                                    label="Company"
                                ></v-autocomplete>
                            </div>
                            <div class="v-col-md-2 py-1">
                                <v-autocomplete
                                    :items="locationList"
                                    v-model="objFIlter.location_id"
                                    @update:modelValue="filterSearch"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    label="Location"
                                    item-value="id"
                                    clearable
                                    item-title="title"
                                ></v-autocomplete>
                            </div>
                            <div class="v-col-md-2 py-1">
                                <v-autocomplete
                                    :items="categoryStore.list"
                                    v-model="objFIlter.location_id"
                                    @update:modelValue="filterSearch"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    label="Category"
                                    item-value="id"
                                    clearable
                                    item-title="title"
                                ></v-autocomplete>
                            </div>
                            
                            <div class="v-col-md-2 py-1">
                                <v-autocomplete
                                    :items="statusList"
                                    v-model="objFIlter.status_id"
                                    @update:modelValue="filterSearch"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    clearable
                                    label="Status"
                                ></v-autocomplete>
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
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
                                label="Search (ex. ISR-100035 )"
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
                                <th>
                                    <v-checkbox
                                        v-model="checkAll"
                                        @update:modelValue="checkUncheckBox"
                                        class="pa-0 ma-0 v-col-1"
                                        hide-details
                                        variant="underlined"
                                        density="compact"
                                    ></v-checkbox>
                                </th>
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
                                    @click="OrderByField('category_id')"
                                >
                                    Category
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('brand')"
                                >
                                    Brand
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('asset_id')"
                                >
                                    AssetName
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('asset_id')"
                                >
                                    AssetCode
                                </th>

                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('condition_id')"
                                >
                                    Condition
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('status_id')"
                                >
                                    Status
                                </th>
                                <th
                                    class="text-left text-capitalize cursor-pointer"
                                    @click="OrderByField('created_at')"
                                >
                                    D.Created<br />
                                    <small>(DD/MM/YY)</small>
                                </th>
                                <th class="text-right text-capitalize"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in dataObj.data" :key="item.id">
                                <td>
                                    <v-checkbox
                                        v-model="item.print_barcode"
                                        class="pa-0 ma-0 v-col-1"
                                        hide-details
                                        variant="underlined"
                                        density="compact"
                                        @update:modelValue="
                                            checkUncheckBoxSingle
                                        "
                                    ></v-checkbox>
                                </td>
                                <td>{{ item.company?.title }}</td>
                                <td>{{ item.location?.title }}</td>
                                <td>
                                    {{ item.category?.title }}
                                    <strong>({{ item.category?.code }})</strong>
                                </td>
                                <td>{{ item.brand?.title }}</td>
                                <td>{{ item.asset_name }}</td>
                                <td>{{ item.asset_code }}</td>

                                <td>
                                    <v-chip
                                        class="text-uppercase"
                                        size="small"
                                        :color="`${
                                            item.condition?.title.toLowerCase() ==
                                            'good' || item.condition?.title.toLowerCase() ==
                                            'perfect'
                                                ? 'success'
                                                : 'error'
                                        }`"
                                        >{{ item.condition?.title }}</v-chip
                                    >
                                </td>
                                <td>
                                   
                                    <v-chip
                                        class="text-uppercase"
                                        size="small"
                                        :color="`${
                                            item.status?.title.toLowerCase() ==
                                            'maintenance' || item.status?.title.toLowerCase() ==
                                            'broken' || item.status?.title.toLowerCase() ==
                                            'damage'
                                                ? 'error'
                                                : 'info'
                                        }`"
                                        >{{ item.status?.title }}</v-chip
                                    >
                                </td>
                                <td>
                                    {{
                                        item.created_at
                                            ? useFormatDate(item.created_at)
                                            : ""
                                    }}
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
                                        <v-icon
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
                                        />
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
                 <div style="position:relative;" class="mb-5">
                    <div style="position:absolute; left:10px; font-weight:bold; top:10px;">Total: {{totalResult && totalResult > 0 ? totalResult : 0}}</div>
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
                
            </div>
        </v-row>
        <AppSnackBar :options="sbOptions" />
    </v-container> 
    
        <div v-if="dialogPrintBarcoce">
            <div class="text-center no-print my-2">
                <v-btn @click="dialogPrintBarcoce=false" class="mx-1"  size="small" color="primary" >Back</v-btn>
                <v-btn @click="printNow" class="mx-1" size="small" color="primary" >Print Now</v-btn>
                <v-btn @click="clearBarcodes" class="mx-1" size="small" color="primary" >Clear</v-btn>
            </div>
            <div  id="section-to-print" class="pa-0 ma-0 d-flex flex-column justify-center align-center w-100 ">
                <BarcodeGenerator
                    class="ma-auto"
                    v-for="(item, index) in draftPrints"
                    :key="index"
                    :value="item"
                    :format="'CODE128'"
                    :width="1.5"
                    :height="60"
                />
            </div>
        </div>
</template>

<script setup>
import AppPageHeader from "@/components/ApppageHeader.vue";
import { onMounted, ref, watch } from "vue";
import { mdiPencil, mdiTrashCan } from "@mdi/js";
import { useRouter, useRoute } from "vue-router";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { encryptData, decryptData } from "@/composables/encrypt";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useFormatDate } from "@/composables/formatDate.js";
import BarcodeGenerator from "@/components/BarcodeGenerator.vue";
import DownloadExcel from "vue-json-excel3";

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
const objFIlter = ref({});
const btnLoading = ref(false);
const donwloadLeads = async () => { 
    btnLoading.value = true;

    await clientKey(authStore.token)
        .get("/api/fetch-assets/download")
        .then((res) => {
            return res.data;
        })
        .catch((err) => {});
    
};

const json_field = ref({
    Company: "stateReview",
    Location: "statusReview",
    Category: "ecode",
    AssetName: "display_name",
    AssetCode: "designation",
    CodeNumber: "designation",
    Status: "designation",
    Condition: "designation",
    Brand: "designation",
    Model: "designation",
    Specification: "designation",
    SerialNumber: "designation",
    Vendor: "designation",
    "PO Number": "designation",
    "Purchased Date": "designation",
    "Purchased Price": "designation",
    "Capitalization Price": "designation",
    "End of Life": "designation",
    "Capitalization Date": "designation",
    "Depreciation %": "designation",
    "Scrap Value": "designation",
    "Transferred To": "designation",
    "Alotted To": "designation",
    "Warranty": "designation",
    "Warranty Start Date": "designation",
    "Warranty End Date": "designation",
    "AMC Vendor": "designation",
    "AMC Start Date": "designation",
    "AMC End Date": "designation",
    "Maintenance": "designation",
    "Maintenance": "designation",
    "Maintenance": "designation",
    "Maintenance": "designation",
    
    Manager: {
        field: "managed_by.display_name",
        callback: (value) => {
            return `${value}`;
        },
    }, 
    
});

const filterRows = () => {
    getAllData();
};

const filterSearch = () => {
    if (currentPage.value == 1) {
        getAllData();
    } else {
        currentPage.value = 1;
    }
};

const searchData = () => {
    localStorage.setItem("asset-list-search", encryptData(search.value));
    getAllData();
};

const clearSearch = () => {
    search.value = "";
    localStorage.setItem("asset-list-search", "");
    getAllData();
};
const draftPrints = ref([]);
const checkAll = ref(false);

const checkUncheckBox = () => {
    enablePrintBarcode.value = false;
    draftPrints.value = [];
    dataObj.value.data.map((o, i) => {
        o.print_barcode = checkAll.value;
        if (checkAll.value) {
            draftPrints.value.push(o.asset_code);
        }
    });
    if (checkAll.value) {
        enablePrintBarcode.value = true;
    }
};

const enablePrintBarcode = ref(false);
const checkUncheckBoxSingle = () => {
    enablePrintBarcode.value = false;
    let validateBoxes = true;
    let hasSingleEnable = false;
    draftPrints.value = [];
    dataObj.value.data.map((o, i) => {
        if (!o.print_barcode) {
            validateBoxes = false;
        } else {
            hasSingleEnable = true;
            draftPrints.value.push(o.asset_code);
        }
    });
    checkAll.value = validateBoxes;
    if (hasSingleEnable) {
        enablePrintBarcode.value = true;
    }
};

const dialogPrintBarcoce = ref(false);
const printBarcodesFn = () => {
    dialogPrintBarcoce.value = true;
    console.log("printData", draftPrints.value);
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
    getAllData();
};

const companyList = ref([]);
const fetchCompanies = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/companies")
        .then((res) => {
            companyList.value = res.data;
        })
        .catch((err) => {});
};

const locationList = ref([]);
const fetchLocations = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/locations/non-paginated/data")
        .then((res) => {
            locationList.value = res.data;
        })
        .catch((err) => {});
}; 
 
 // categories
import { useCategoryStore } from "@/stores/categories";
const categoryStore = useCategoryStore();
if (categoryStore.list.length == 0) {
  categoryStore.getCategories(authStore.token);
}


const statusList = ref([]);
const fetchStatus = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch-global/status/active/asset")
        .then((res) => {
            statusList.value = res.data;
        })
        .catch((err) => {});
};

const getAllData = async () => {
    dataObj.value.loading = true;
    await clientKey(authStore.token)
        .get(
            "/api/fetch/all-assets/data?page=" +
                currentPage.value +
                "&show=" +
                showPerPage.value +
                "&search=" +
                search.value +
                "&sort=" +
                sortBy.value +
                "&filter=" +
                JSON.stringify(objFIlter.value)
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
                name: "paginated-asset-list",
                params: {
                    page: currentPage.value,
                },
            })
            .catch((err) => {});
        getAllData(currentPage.value);
    }
});

const editUser = (id) => {
    router
        .push({
            name: "edit-asset",
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

const addNew = (v) => {
    router
        .push({
            name: v,
        })
        .catch((err) => {
            console.log(err);
        });
};

const printNow = () =>{
    window.print();
}

const clearBarcodes = ()=>{
    draftPrints.value = [];
    dialogPrintBarcoce.value = false;

    dataObj.value.data.map((o, i) => {
        o.print_barcode = '';
       
    });
    checkAll.value = false;
    enablePrintBarcode.value = false;
   
}
onMounted(() => {
    let vsearch = localStorage.getItem("asset-list-search");

    if (vsearch) {
        search.value = decryptData(vsearch);
    }
    getAllData().then(() => {
        fetchCompanies();
        fetchLocations();
        fetchStatus();
        fetchCategories();
    });
});
</script>
<style>
@media print {
    .no-print {
        display: none;
        visibility: hidden;
    }
    body { 
        visibility: hidden;
        padding:0;
        margin:0;
    }   
    *{  padding:0 !important;}
   
   #section-to-print {
    visibility: visible;  
    display: block;
    left: 0;
    top: 0;
   }
}</style>