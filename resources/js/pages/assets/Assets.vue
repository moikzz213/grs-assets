<template>
    <v-container class="no-print" v-if="!dialogPrintBarcoce">
        <AppPageHeader title="ASSET list page" />
        <v-row class="mt-0">
            <div class="v-col py-0 d-flex align-center">
                <v-btn
                    color="primary"
                    class="mr-3 mb-3"
                    @click="addNew('add-asset')"
                    v-if="
                        authStore.user.role == 'superadmin' ||
                        authStore.capabilities?.includes('add')
                    "
                    >Add new asset</v-btn
                >
                <v-btn
                    color="primary"
                    class="mr-3 mb-3"
                    @click="addNew('NewTransferAsset')"
                    >transfer asset</v-btn
                >
                <v-btn
                    color="primary"
                    class="mr-3 mb-3"
                    @click="addNew('NewRequestAsset')"
                    >request asset</v-btn
                >
                <v-btn
                    color="primary"
                    class="ml-auto mr-3 mb-3"
                    :disabled="!enablePrintBarcode"
                    @click="printBarcodesFn"
                    >print barcodes({{ draftPrints.length }})</v-btn
                >
                <DownloadExcel
                    v-if="
                        dataObj.data?.length > 0
                    "
                    :fetch="donwloadLeads"
                    :fields="json_field"
                    worksheet="Report"
                    name="Assets.csv"
                    type="csv"
                    disabled
                >
                    <v-btn
                        color="success"
                        :disabled="
                            Object.keys(dataObj.data).length == 0 ? true : false
                        "
                        :loading="loadingDownloadLeads"
                        dark
                        class="mb-3"
                    >
                        Download
                    </v-btn>
                </DownloadExcel>
            </div>
        </v-row>
        <v-row class="mb-3">
            <v-col class="v-col-12">
                <v-card :loading="dataObj.loading">
                    <v-card-title class="d-flex align-center mb-3 pa-3">
                        <div class="mr-auto">Asset List</div>
                        <v-text-field
                            v-model="search"
                            variant="outlined"
                            density="compact"
                            clearable
                            label="Search"
                            type="text"
                            hide-details
                            style="max-width: 300px"
                            @click:append-outer="searchData"
                            @keydown.enter="searchData"
                            @click:clear="clearSearch('search')"
                        ></v-text-field>
                    </v-card-title>
                    <v-row class="px-3">
                        <div class="v-col-12 v-col-md">
                            <v-autocomplete
                                :items="companyList"
                                v-model="objFIlter.company_id"
                                @update:modelValue="filterSearch('company')"
                                variant="outlined"
                                density="compact"
                                hide-details
                                item-value="id"
                                item-title="title"
                                clearable
                                label="Company"
                                @click:clear="clearSearch('company')"
                            ></v-autocomplete>
                        </div>
                        <div class="v-col-12 v-col-md">
                            <v-autocomplete
                                :items="locationList"
                                v-model="objFIlter.location_id"
                                @update:modelValue="filterSearch('location')"
                                variant="outlined"
                                density="compact"
                                hide-details
                                label="Location"
                                item-value="id"
                                clearable
                                item-title="title"
                                @click:clear="clearSearch('location')"
                            ></v-autocomplete>
                        </div>
                        <div class="v-col-12 v-col-md">
                            <v-autocomplete
                                :items="categoryStore.list"
                                v-model="objFIlter.category_id"
                                @update:modelValue="filterSearch('category')"
                                variant="outlined"
                                density="compact"
                                hide-details
                                label="Category"
                                item-value="id"
                                clearable
                                item-title="title"
                                @click:clear="clearSearch('category')"
                            ></v-autocomplete>
                        </div>

                        <div class="v-col-12 v-col-md">
                            <v-autocomplete
                                :items="statusList"
                                v-model="objFIlter.status_id"
                                @update:modelValue="filterSearch('status')"
                                variant="outlined"
                                density="compact"
                                hide-details
                                item-value="id"
                                item-title="title"
                                clearable
                                label="Status"
                                @click:clear="clearSearch('status')"
                            ></v-autocomplete>
                        </div>
                        <div class="v-col-12 v-col-md">
                            <v-autocomplete
                                :items="lpoList"
                                v-model="objFIlter.po_number"
                                @update:modelValue="filterSearch('po_number')"
                                variant="outlined"
                                density="compact"
                                hide-details
                                clearable
                                label="LPO No."
                                @click:clear="clearSearch('po_number')"
                            ></v-autocomplete>
                        </div>
                        <div class="v-col-12 v-col-md">
                            <v-autocomplete
                                :items="showRows"
                                v-model="showPerPage"
                                @update:modelValue="filterRows"
                                variant="outlined"
                                density="compact"
                                hide-details
                                label="Entry"
                            ></v-autocomplete>
                        </div>
                    </v-row>

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
                                    class="text-left text-capitalize"
                                    v-if="objFIlter.po_number"
                                >
                                    Price
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
                            <tr
                                v-for="item in dataObj.data"
                                :key="item.id"
                                :class="`${
                                    item.status?.title.toLowerCase() ==
                                    'trashed'
                                        ? 'bg-deep-orange-lighten-5'
                                        : ''
                                }`"
                            >
                                <td>
                                    <v-checkbox
                                        v-if="
                                            item.status?.title.toLowerCase() !=
                                            'trashed'
                                        "
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
                                <td>{{ item.brand }}</td>
                                <td>{{ item.asset_name }}</td>
                                <td>{{ item.asset_code }}</td>
                                <td>
                                    <v-chip
                                        class="text-uppercase"
                                        size="small"
                                        :color="`${
                                            item.condition?.title.toLowerCase() ==
                                                'good' ||
                                            item.condition?.title.toLowerCase() ==
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
                                                'maintenance' ||
                                            item.status?.title.toLowerCase() ==
                                                'broken' ||
                                            item.status?.title.toLowerCase() ==
                                                'trashed' ||
                                            item.status?.title.toLowerCase() ==
                                                'damage'
                                                ? 'error'
                                                : 'info'
                                        }`"
                                        >{{ item.status?.title }}</v-chip
                                    >
                                </td>
                                <td
                                    v-if="objFIlter.po_number"
                                    class="text-uppercase"
                                >
                                    {{ item.price }} {{ item.currency }}
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
                                            @click="
                                                () => editUser(item.id, 'edit')
                                            "
                                            :icon="mdiPencil"
                                            class="mx-1"
                                        />
                                        <v-icon
                                            size="small"
                                            v-else
                                            @click="
                                                () => editUser(item.id, 'view')
                                            "
                                            :icon="mdiFileEye"
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
                        <tfoot v-if="objFIlter.po_number">
                            <tr>
                                <td colspan="9" class="text-right">
                                    Total Price
                                </td>
                                <td colspan="3">{{ getTotalPrice() }}</td>
                            </tr>
                        </tfoot>
                    </v-table>
                    <v-sheet
                        v-if="dataObj.data.length == 0"
                        class="pa-3 text-center w-100"
                        >No records found</v-sheet
                    >
                </v-card>
                Total: {{ totalResult && totalResult > 0 ? totalResult : 0 }}
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
            </v-col>
        </v-row>
        <AppSnackBar :options="sbOptions" />
    </v-container>
    <div v-if="dialogPrintBarcoce">
        <div class="text-center no-print my-2">
            <v-btn
                @click="dialogPrintBarcoce = false"
                class="mx-1"
                size="small"
                color="primary"
                >Back</v-btn
            >
            <v-btn @click="printNow" class="mx-1" size="small" color="primary"
                >Print Now</v-btn
            >
            <v-btn
                @click="clearBarcodes"
                class="mx-1"
                size="small"
                color="primary"
                >Clear</v-btn
            >
        </div>
        <div
            id="section-to-print"
            class="pa-0 ma-0 d-flex flex-column justify-center align-center w-100"
        >
            <!-- <BarcodeGenerator
        class="ma-auto"
        v-for="(item, index) in draftPrints"
        :key="index"
        :value="item"
        :format="'CODE128'"
        :width="1.5"
        :height="60"
      /> -->
            <div
                style="width: 366.7px; height: 103px"
                class="d-flex mr-1 mt-0 pa-0 justify-space-between align-center bg-white"
                v-for="(item, index) in draftPrints"
            >
                <QRCodeVue3
                    :width="98"
                    :height="98"
                    :value="item"
                    :qrOptions="{
                        typeNumber: 0,
                        mode: 'Byte',
                        errorCorrectionLevel: 'Q',
                    }"
                    :imageOptions="{
                        hideBackgroundDots: true,
                        imageSize: 0.4,
                        margin: 0,
                    }"
                    :dotsOptions="{ type: 'square', color: '#05004d' }"
                    :backgroundOptions="{ color: '#ffffff' }"
                    :cornersSquareOptions="{ type: 'square', color: '#0e013c' }"
                    fileExt="svg"
                    myclass="my-qur font-weight-bold pl-2 mb-0"
                    imgclass="img-qr"
                />
                <strong
                    style="
                        font-size: 20px;
                        width: 100%;
                        text-align: center;
                        margin-bottom: 0px;
                    "
                    >{{ item }}</strong
                >
            </div>
        </div>
    </div>
</template>

<script setup>
import AppPageHeader from "@/components/ApppageHeader.vue";
import { onMounted, ref, watch } from "vue";
import { mdiPencil, mdiTrashCan, mdiFileEye } from "@mdi/js";
import { useRouter, useRoute } from "vue-router";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { encryptData, decryptData } from "@/composables/encrypt";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useFormatDate } from "@/composables/formatDate.js";
import DownloadExcel from "vue-json-excel3";
import QRCodeVue3 from "qrcode-vue3";
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
const loadingDownloadLeads = ref(false);
const donwloadLeads = async () => {
    //
    loadingDownloadLeads.value = true;
    let allAssets = await clientKey(authStore.token)
        .get(
            "/api/fetch-assets/download?filter=" +
                JSON.stringify(objFIlter.value)
        )
        .then((res) => {
            loadingDownloadLeads.value = false;
            return res.data;
        })
        .catch((err) => {
            loadingDownloadLeads.value = false;
        });
    return allAssets;
};

const json_field = ref({
    //     Location: {
    //     field: "location.title",
    //     callback: (value) => {
    //       return `${value}`;
    //     },
    //   },
    "Business Unit": "company.title",
    Location: "location.title",
    Category: "category.title",
    "Asset Name": "asset_name",
    "Asset Code": "asset_code",
    "Asset Section": "section_code",
    Status: "status.title",
    condition: "condition.title",
    Brand: "brand",
    Model: "model",
    Specification: "specification",
    "Serial Number": "serial_number",

    Vendor: "vendor.title",
    "PO Number": "po_number",
    "Purchased Date": "purchased_date",
    Price: "price",

    "Capitalization Price": "financial_information.capitalization_price",
    "End of Life": "financial_information.end_of_life",
    "Capitalization Date": "financial_information.capitalization_date",
    "Depreciation Percentage": "financial_information.depreciation_percentage",
    "Scrap Value": "financial_information.scrap_value",
    "Scrap Date": "financial_information.scrap_date",

    "Transferred To": "allotted_information.location.title",
    "Alotted To": "allotted_information.location.title",

    "Warranty Title": "warranties.warranty_title",
    "Warranty Start Date": "warranties.warranty_start_date",
    "Warranty End Date": "warranties.warranty_end_date",
    "AMC Vendor": "warranties.vendor.amc_start_date",
    "AMC End Date": "warranties.vendor.amc_end_date",
    "AMC End Date": "warranties.vendor.amc_end_date",
});

const getTotalPrice = () => {
    let getPrice = dataObj.value.data.map((sf) => sf.price);
    let sum = 0;

    getPrice.forEach((num) => {
        sum += parseFloat(num);
    });
    return sum.toFixed(2);
};
const filterRows = () => {
    localStorage.setItem("asset-filter-row", encryptData(showPerPage.value));
    getAllData();
};

const filterSearch = (v) => {
    sortBy.value = "";
    if (currentPage.value == 1) {
        if (v == "company") {
            localStorage.setItem(
                "asset-filter-company",
                encryptData(objFIlter.value.company_id)
            );
        } else if (v == "location") {
            localStorage.setItem(
                "asset-filter-location",
                encryptData(objFIlter.value.location_id)
            );
        } else if (v == "category") {
            localStorage.setItem(
                "asset-filter-category",
                encryptData(objFIlter.value.category_id)
            );
        } else if (v == "status") {
            localStorage.setItem(
                "asset-filter-status",
                encryptData(objFIlter.value.status_id)
            );
        } else if (v == "po_number") {
            localStorage.setItem(
                "asset-filter-ponum",
                encryptData(objFIlter.value.po_number)
            );
        }
        getAllData();
    } else {
        currentPage.value = 1;
    }
};

const searchData = () => {
    localStorage.setItem("asset-list-search", encryptData(search.value));
    getAllData();
};

const clearSearch = (v) => {
    if (v == "search") {
        search.value = "";
        localStorage.setItem("asset-list-search", "");
        getAllData();
    } else if (v == "company") {
        localStorage.setItem("asset-filter-company", "");
    } else if (v == "location") {
        localStorage.setItem("asset-filter-location", "");
    } else if (v == "category") {
        localStorage.setItem("asset-filter-category", "");
    } else if (v == "status") {
        localStorage.setItem("asset-filter-status", "");
    } else if (v == "po_number") {
        localStorage.setItem("asset-filter-ponum", "");
    }
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

const lpoList = ref([]);
const fetchLPONos = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch-assets/lpo-no-only")
        .then((res) => {
            console.log("res.data", res.data);
            lpoList.value = res.data.result;
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

const editUser = (id, type) => {
    let componentName = "edit-asset";
    if (type == "view") {
        componentName = "view-asset";
    }
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

const printNow = () => {
    window.print();
};

const clearBarcodes = () => {
    draftPrints.value = [];
    dialogPrintBarcoce.value = false;

    dataObj.value.data.map((o, i) => {
        o.print_barcode = "";
    });
    checkAll.value = false;
    enablePrintBarcode.value = false;
};
onMounted(() => {
    let vsearch = localStorage.getItem("asset-list-search");
    let vcomp = localStorage.getItem("asset-filter-company");
    let vlocation = localStorage.getItem("asset-filter-location");
    let vcategory = localStorage.getItem("asset-filter-category");
    let vstatus = localStorage.getItem("asset-filter-status");
    let vponum = localStorage.getItem("asset-filter-ponum");
    let vrows = localStorage.getItem("asset-filter-row");

    if (vsearch) {
        search.value = decryptData(vsearch);
    }
    if (vcomp) {
        objFIlter.value.company_id = decryptData(vcomp);
    }
    if (vlocation) {
        objFIlter.value.location_id = decryptData(vlocation);
    }
    if (vcategory) {
        objFIlter.value.category_id = decryptData(vcategory);
    }
    if (vstatus) {
        objFIlter.value.status_id = decryptData(vstatus);
    }
    if (vrows) {
        showPerPage.value = decryptData(vrows);
    }
    if (vponum) {
        objFIlter.value.po_number = decryptData(vponum);
    }

    getAllData().then(() => {
        fetchCompanies();
        fetchLocations();
        fetchStatus();
        fetchLPONos();
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
        padding: 0;
        margin: 0;
    }
    * {
        padding: 0 !important;
    }

    #section-to-print {
        visibility: visible;
        display: block;
        left: 0;
        top: 0;
    }
}
</style>
