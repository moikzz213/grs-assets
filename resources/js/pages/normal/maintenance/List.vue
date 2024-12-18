<template>
  <v-container>
    <AppPageHeader title="Maintenance list page" />
    <v-row class="mb-3">
      <div class="v-col-12">
        <v-card :loading="users.loading">
          <v-card-title class="d-flex align-center mb-3 pa-3">
            <div class="mr-2">Maintenance</div>
            <v-btn color="primary" size="small" @click="goTo" class="mr-auto">NEW</v-btn>
            <v-text-field
              v-model="search"
              variant="outlined"
              density="compact"
              clearable
              label="Search (ex. ISR-100035 )"
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
                <th class="text-left text-capitalize">ISR No.</th>
                <th class="text-left text-capitalize">Urgency</th>
                <th class="text-left text-capitalize">Priority</th>

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
                  @click="OrderByField('asset_id')"
                >
                  Asset
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('asset_id')"
                >
                  AssetCode
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('profile_id')"
                >
                  ReportedBy
                </th>
                <th
                  class="text-left text-capitalize cursor-pointer"
                  @click="OrderByField('handled_by')"
                >
                  HandledBy
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
              <tr v-for="item in users.data" :key="item.id">
                <td>ISR-2{{ pad(item.id) }}</td>
                <td>{{ item.urgency?.title }}</td>
                <td>{{ staticStatus(item.priority) }}</td>
                <td>{{ item.company?.title }}</td>
                <td>{{ item.location?.title }}</td>
                <td>{{ item.asset ? item.asset.asset_name : item.title }}</td>
                <td>{{ item.asset?.asset_code }}</td>

                <td>{{ item.profile?.display_name }}</td>
                <td>{{ item.handled_by?.display_name }}</td>
                <td>
                  <v-chip
                    class="text-uppercase"
                    size="small"
                    :color="`${
                      item.status?.title.toLowerCase() == 'completed'
                        ? 'success'
                        : 'error'
                    }`"
                    >{{ item.status?.title }}</v-chip
                  >
                </td>
                <td>{{ useFormatDate(item.created_at) }}</td>
                <td>
                  <div class="d-flex align-center justify-end">
                    <v-icon
                      size="small"
                      v-if="
                        authStore.user.role == 'superadmin' ||
                        authStore.capabilities?.includes('edit')
                      "
                      @click="() => editUser(item.id, 'edit')"
                      :icon="mdiPencil"
                      class="mx-1"
                    />
                    <v-icon
                      size="small"
                      v-else
                      @click="() => editUser(item.id, 'view')"
                      :icon="mdiEyeOutline"
                      class="mx-1"
                    />
                    <!-- <v-icon
                      size="small"
                      v-if="
                        authStore.user.role == 'superadmin' ||
                        authStore.capabilities?.includes('delete')
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
          <v-sheet v-if="users.data.length == 0" class="pa-3 text-center w-100"
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

    <AppSnackBar :options="sbOptions" />
  </v-container>
</template>

<script setup>
import AppPageHeader from "@/components/ApppageHeader.vue";
import { onMounted, ref, watch } from "vue";
import { mdiPencil, mdiTrashCan, mdiEyeOutline } from "@mdi/js";
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
const users = ref({
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
  
  localStorage.setItem("maintenance-filter-row", encryptData(showPerPage.value)); 
  getAllData();
};

const filterSearch = (v) => { 
  sortBy.value = "";
  if (currentPage.value == 1) {
      if(v == 'company'){ 
        if(objFIlter.value.company_id == ''){  
            localStorage.setItem("maintenance-filter-company", '');
        }else{
          localStorage.setItem("maintenance-filter-company", encryptData(objFIlter.value.company_id));
        }
      }else if(v == 'location'){ 
        if(objFIlter.value.location_id == ''){
          localStorage.setItem("maintenance-filter-location", '');
        }else{
          localStorage.setItem("maintenance-filter-location", encryptData(objFIlter.value.location_id));
        }
      } else if(v == 'status'){ 
        if(objFIlter.value.location_id == ''){
          localStorage.setItem("maintenance-filter-status", '');
        }else{
          localStorage.setItem("maintenance-filter-status", encryptData(objFIlter.value.status_id));
        }
      }
      getAllData();
  } else {
    currentPage.value = 1;
  }
};

const goTo = () => {  
    router
    .push({
        name: 'NewMaintenance'       
    })
    .catch((err) => {});
} 

const searchData = () => {
 
  localStorage.setItem("maintenance-search", encryptData(search.value));
  getAllData();
};

const clearSearch = () => {  
    search.value = "";
    localStorage.setItem("maintenance-search", ""); 
    getAllData();
};

const currentPage = ref(route.params && route.params.page ? route.params.page : 1);

const staticStatus = (v) => {
  if (v) {
    if (v == 1) {
      return "Normal";
    } else if (v == 2) {
      return "Medium";
    } else {
      return "High";
    }
  }
  return "";
};

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

const statusList = ref([]);
const fetchStatus = async () => {
  await clientKey(authStore.token)
    .get("/api/fetch-global/incident-status/active")
    .then((res) => {
      statusList.value = res.data;
    })
    .catch((err) => {});
};

const getAllData = async () => { 
 
  users.value.loading = true;
  await clientKey(authStore.token)
    .get(
      "/api/fetch/maintenance-assets/data?userid=" +
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
      users.value.data = res.data.data ? res.data.data : res.data;
      users.value.loading = false;
    })
    .catch((err) => {
      users.value.loading = false;
      localStorage.setItem("maintenance-filter-company", null); 
      localStorage.setItem("maintenance-filter-location", null);  
      localStorage.setItem("maintenance-filter-status", null);
      localStorage.setItem("maintenance-filter-row", 10);
    });
};
watch(currentPage, (newValue, oldValue) => {
 
  if (currentPage.value && newValue != oldValue) {
    router
      .push({
        name: "PaginatedMaintenance",
        params: {
          page: currentPage.value,
        },
      })
      .catch((err) => {});
    getAllData(currentPage.value);
  }
});

const editUser = (id, type) => {
  let queryParam = { type: "details" };
  if (type == "view") {
    queryParam = {
      type: "details",
      pd: "ox2rKaIYFhBOQW31j6kkiMrJ4KfnYpLmwJhMzBFwPBlVjgGR4g",
      v: 1,
      vv: "xK2sX1sDL1BBAvmuoPpWoNRgc4wzbdZ9F2OXAKrS3N2g65xNgy",
    };
  }
  router
    .push({
      name: "EditMaintenance",
      params: {
        id: id,
      },
      query: queryParam,
    })
    .catch((err) => {
 
    });
};

const deleteUser = (item) => {
  console.log("delete", item);
};

const pad = (v, size = 5) => {
  let s = "0000" + v;
  return s.substring(s.length - size);
};

onMounted(() => {
  let vsearch = localStorage.getItem("maintenance-search");

  let vcomp = localStorage.getItem("maintenance-filter-company"); 
  let vlocation = localStorage.getItem("maintenance-filter-location");  
  let vstatus = localStorage.getItem("maintenance-filter-status");
  let vrows = localStorage.getItem("maintenance-filter-row");
     
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
    objFIlter.value.status_id = decryptData(vstatus);
  } 
  if(vrows){
    showPerPage.value = decryptData(vrows);
  }
  getAllData().then(() => {
    fetchCompanies();
    fetchLocations();
    fetchStatus();
  });
});
</script>
