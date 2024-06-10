<template>
  <v-container>
    <AppPageHeader title="Asset Transfer page" />
    <v-row class="mb-3">
      <div class="v-col-12">
        <v-card :loading="dataobj.loading">
          <v-card-title class="d-flex align-center mb-3 pa-3">
            <div class="mr-auto">Asset Transfers</div>
            <v-text-field
              v-model="search"
              variant="outlined"
              density="compact"
              clearable
              label="Search (ex. SN-3000035 )"
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
                :items="statusList"
                v-model="objFIlter.status"
                @update:modelValue="filterSearch('status')"
                variant="outlined"
                density="compact"
                hide-details
                item-value="id"
                item-title="title"
                clearable
                @click:clear="clearSearch('status')"
                label="Status"
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
                label="Show"
              ></v-autocomplete>
            </div>
          </v-row>
         
          <v-table>
            <thead>
              <tr>
                <th class="text-left text-capitalize">SN No.</th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('company_id')"
                >
                  Company
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('transferred_to')"
                >
                  Location
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('subject')"
                >
                  Subject
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('profile_id')"
                >
                  Requestor
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('reminder_profile_id')"
                >
                  Approval<br/>
                  <small>PENDING</small>
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('status')"
                >
                  Status
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('created_at')"
                >
                  D.Requested<br />
                  <small>(DD/MM/YY)</small>
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('date_closed')"
                >
                  D.Closed<br />
                  <small>(DD/MM/YY)</small>
                </th>
                <th class="text-right text-capitalize last-child-action"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in dataobj.data" :key="item.id" :class="`${authStore.user.profile.id == item.reminder_profile_id ? 'bg-blue-lighten-5' : ''} `">
                <td>SN-3{{ pad(item.id) }}</td>
                <td>{{ item.company?.title }}</td>
                <td>{{ item.transfer_to?.title }}</td>
                <td>{{ item.subject }}</td>
                <td>{{ item.profile?.display_name }}</td>
                <td>{{ item.reminder_profile?.display_name }}</td>
                <td>
                  <v-chip
                    class="text-uppercase"
                    size="small"
                    :color="`${
                      item.status.toLowerCase() == 'complete' ? 'success' :  (item.status.toLowerCase() == 'pending' || item.status.toLowerCase() == 'awaiting-approval') ? 'warning' : 'error'
                    }`"
                    >{{ statusTitle(item.status) }}</v-chip
                  >
                </td>
                <td>{{ useFormatDate(item.created_at) }}</td>
                <td>
                  {{
                    item.date_closed && item.date_closed != "0000-00-00"
                      ? useFormatDate(item.date_closed)
                      : ""
                  }}
                </td>
                <td class="last-child-action">
                  <div class="d-flex align-center justify-end">
                    <v-icon
                      size="small"
                      @click="() => editUser(item.id)"
                      :icon="mdiPencil"
                      class="mx-1"
                    />
                    <v-icon
                      size="small"
                      v-if="
                        authStore.user.role == 'superadmin' ||
                        authStore.capabilities?.includes('delete')
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
          <v-sheet v-if="dataobj.data.length == 0" class="pa-3 text-center w-100"
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
          :disabled="dataobj.loading"
        ></v-pagination>
      </div>
    </v-row>
    <AppSnackBar :options="sbOptions" />
  </v-container>
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

const sbOptions = ref({
  status: false,
  type: "primary",
  text: null,
});

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const dataobj = ref({
  loading: false,
  data: [],
});
const totalPageCount = ref(0);
const totalResult = ref(0);
const search = ref("");
const showRows = ref([10, 20, 50, 100]);
const showPerPage = ref(10);
const objFIlter = ref({});

const filterRows = () => {
  localStorage.setItem("transfer-filter-row", encryptData(showPerPage.value));
  getAllData();
};

const filterSearch = (v) => {
  sortBy.value = "";
  if (currentPage.value == 1) {
      if(v == 'company'){ 
        localStorage.setItem("transfer-filter-company", encryptData(objFIlter.value.company_id));
      }else if(v == 'location'){ 
        localStorage.setItem("transfer-filter-location", encryptData(objFIlter.value.location_id));
      } else if(v == 'status'){ 
        localStorage.setItem("transfer-filter-status", encryptData(objFIlter.value.status));
      }
      getAllData();
  } else {
    currentPage.value = 1;
  }
};

const searchData = () => {
  localStorage.setItem("transfer-asset-search", encryptData(search.value));
  getAllData();
};

const clearSearch = (v) => {
    
  if(v == 'search'){
    search.value = "";
    localStorage.setItem("transfer-asset-search", "");
    getAllData();
  }else if(v == 'company'){  
      localStorage.setItem("transfer-filter-company", '');
  }else if(v == 'location'){  
    localStorage.setItem("transfer-filter-location", '');
  }else if(v == 'status'){  
    localStorage.setItem("transfer-filter-status", '');
  }
 
};

const currentPage = ref(route.params && route.params.page ? route.params.page : 1);

const orderBy = ref([]);
const sortBy = ref("");
const orderByCount = ref(0);
const OrderByField = (v) => {
  dataobj.value.loading = true;

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

const statusList = ref([
  { id: "pending", title: "Pending" },
  { id: "awaiting-approval", title: "Awaiting Approval" },
  { id: "reject", title: "Rejected" },
  { id: "cancelled", title: "Cancelled" },
  { id: "complete", title: "Completed" },
]);

const statusTitle = (v) => {
  if (v == "reject") {
    return "Rejected";
  } else if (v == "complete") {
    return "Completed";
  }
  return v;
};

const getAllData = async () => {
  dataobj.value.loading = true;
  await clientKey(authStore.token)
    .get(
      "/api/fetch/request-assets/by-requestor/transfer?userid=" +
        authStore.user.profile.id +
        "&role=" +
        authStore.user.profile.role +
        "&page=" +
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
      totalPageCount.value = res.data.last_page ? res.data.last_page : res.data.length;
      currentPage.value = res.data.current_page
        ? res.data.current_page
        : currentPage.value;
      totalResult.value = res.data.total ? res.data.total : res.data.length;
      dataobj.value.data = res.data.data ? res.data.data : res.data;
      dataobj.value.loading = false;
    })
    .catch((err) => {
      dataobj.value.loading = false;
      console.log(err);
    });
};
watch(currentPage, (newValue, oldValue) => {
  if (currentPage.value && newValue != oldValue) {
    router
      .push({
        name: "PaginatedTransferAsset",
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
      name: "EditTransferAsset",
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

const pad = (v, size = 6) => {
  let s = "00000" + v;
  return s.substring(s.length - size);
};

onMounted(() => {
  let vsearch = localStorage.getItem("transfer-asset-search");

  let vcomp = localStorage.getItem("transfer-filter-company"); 
  let vlocation = localStorage.getItem("transfer-filter-location");  
  let vstatus = localStorage.getItem("transfer-filter-status");
  let vrows = localStorage.getItem("transfer-filter-row");
     
  if (vsearch) {
    search.value = decryptData(vsearch);
  }
  if(vcomp){
    objFIlter.value.company_id = decryptData(vcomp);
  }
  if(vlocation){
    objFIlter.value.location_id = decryptData(vlocation);
  }
  
  if(vstatus){
    objFIlter.value.status = decryptData(vstatus);
  } 
  if(vrows){
    showPerPage.value = decryptData(vrows);
  }
  getAllData().then(() => {
    fetchCompanies().then(() => {
      fetchLocations();
    });
  });
});
</script>
