<template>
  <v-container>
    <AppPageHeader title="Others - Status Setup" />

    <v-row class="mb-3">
      <v-col class="v-col-12 v-col-md-6 mt-1 col-sm-12">
        <v-card :loading="page.loading">
          <v-card-title>
            <div class="d-flex align-center">
              <v-btn
                :icon="mdiPlus"
                @click="AddNewStatus()"
                size="x-small"
                color="white"
                class="mr-3"
                v-if="
                  authStore.user.role == 'superadmin' ||
                  authStore.capabilities?.includes('add')
                "
              ></v-btn>
              <div>Status List</div>
              <v-btn
                :loading="btnLoading"
                @click="savePage('status')"
                color="primary"
                class="ml-auto"
                v-if="
                  (authStore.user.role == 'superadmin' ||
                    authStore.capabilities?.includes('edit')) &&
                  statusData.length > 0
                "
                >Save</v-btn
              >
            </div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-row class="mb-2">
              <div class="v-col-12 py-0">
                <small>Default value cannot be edited</small>
              </div>
            </v-row>
            <v-row
              v-for="(item, index) in statusData"
              :key="index"
              class="parent-row-show"
            >
              <div class="v-col-5 d-flex">
                <v-autocomplete
                  hide-details="auto"
                  label="Type"
                  variant="outlined"
                  v-model="item.type"
                  :items="statusTypes"
                  item-title="value"
                  item-value="id"
                  density="compact"
                  :disabled="item.id < 10"
                ></v-autocomplete>
              </div>
              <div class="v-col-5">
                <v-text-field
                  hide-details="auto"
                  v-model="item.title"
                  label="Status Title"
                  variant="outlined"
                  density="compact"
                  :disabled="item.id < 10"
                ></v-text-field>
              </div>
              <v-icon
                  :title="`${item.status == 'active' ? 'Active' : 'Disabled'}`"
                  v-if="item.id"
                  :icon="`${
                    item.status == 'active' ? mdiEyeCheckOutline : mdiEyeRemoveOutline
                  }`"
                  :color="`${item.status == 'active' ? 'success' : 'error'}`"
                  class="my-auto mr-2"
                ></v-icon>
              <div class="v-col-2 my-auto" v-if="item.id > 9 || !item.id">
                <v-btn
                  :title="`${
                    item.id
                      ? item.status == 'active'
                        ? 'Click to disable'
                        : 'Click to enable'
                      : 'Remove row'
                  }`"
                  :icon="`${
                    item.id
                      ? item.status == 'active'
                        ? mdiEyeOff
                        : mdiEyeRefresh
                      : mdiTrashCan
                  }`"
                  @click="deleteFn(index, item, 'status')"
                  size="x-small"
                  :color="`${item && item.status == 'disabled' ? 'success' : 'error'}`"
                  class="mx-1 show-hover"
                  v-if="
                    authStore.user.role == 'superadmin' ||
                    authStore.capabilities?.includes('delete')
                  "
                ></v-btn>
              </div>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col class="v-col-12 v-col-md-6 mt-1 col-sm-12">
        <v-card :loading="page.loading">
          <v-card-title>
            <div class="d-flex align-center">
              <v-btn
                :icon="mdiPlus"
                @click="AddNewCondition()"
                size="x-small"
                color="white"
                class="mr-3"
                v-if="
                  authStore.user.role == 'superadmin' ||
                  authStore.capabilities?.includes('add')
                "
              ></v-btn>
              <div>Condition List</div>
              <v-btn
                :loading="btnLoading"
                @click="savePage('condition')"
                color="primary"
                class="ml-auto"
                v-if="
                  (authStore.user.role == 'superadmin' ||
                    authStore.capabilities?.includes('edit')) &&
                  conditionData.length > 0
                "
                >Save</v-btn
              >
            </div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-row
              v-for="(item, index) in conditionData"
              :key="index"
              class="parent-row-show"
            >
              <div class="v-col-5 d-flex">
                <v-icon
                  :title="`${item.status == 'active' ? 'Active' : 'Disabled'}`"
                  v-if="item.id"
                  :icon="`${
                    item.status == 'active' ? mdiEyeCheckOutline : mdiEyeRemoveOutline
                  }`"
                  :color="`${item.status == 'active' ? 'success' : 'error'}`"
                  class="my-auto mr-2"
                ></v-icon>
                <v-autocomplete
                  hide-details="auto"
                  label="Type"
                  variant="outlined"
                  v-model="item.type"
                  :items="conditionTypes"
                  item-title="value"
                  item-value="id"
                  density="compact"
                ></v-autocomplete>
              </div>
              <div class="v-col-5">
                <v-text-field
                  hide-details="auto"
                  v-model="item.title"
                  label="Condition Title"
                  variant="outlined"
                  density="compact"
                ></v-text-field>
              </div>
              <div class="v-col-2 my-auto">
                <v-btn
                  :title="`${
                    item.id
                      ? item.status == 'active'
                        ? 'Click to disable'
                        : 'Click to enable'
                      : 'Remove row'
                  }`"
                  :icon="`${
                    item.id
                      ? item.status == 'active'
                        ? mdiEyeOff
                        : mdiEyeRefresh
                      : mdiTrashCan
                  }`"
                  @click="deleteFn(index, item, 'condition')"
                  size="x-small"
                  :color="`${item && item.status == 'disabled' ? 'success' : 'error'}`"
                  class="mx-1 show-hover"
                  v-if="
                    authStore.user.role == 'superadmin' ||
                    authStore.capabilities?.includes('delete')
                  "
                ></v-btn>
              </div>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col class="v-col-12 v-col-md-6 mt-1 col-sm-12">
        <v-card :loading="page.loading">
          <v-card-title>
            <div class="d-flex align-center">
              <v-btn
                :icon="mdiPlus"
                @click="AddNewIncident()"
                size="x-small"
                color="white"
                class="mr-3"
                v-if="
                  authStore.user.role == 'superadmin' ||
                  authStore.capabilities?.includes('add')
                "
              ></v-btn>
              <div>Incident Type List</div>
              <v-btn
                :loading="btnLoading"
                @click="savePage('incident')"
                color="primary"
                class="ml-auto"
                v-if="
                  (authStore.user.role == 'superadmin' ||
                    authStore.capabilities?.includes('edit')) &&
                  incidentData.length > 0
                "
                >Save</v-btn
              >
            </div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-row class="mb-2">
              <div class="v-col-12 py-0">
                <small>Default value cannot be edited</small>
              </div>
            </v-row>
            <v-row
              v-for="(item, index) in incidentData"
              :key="index"
              class="parent-row-show"
            >
              <div class="v-col-5 d-flex">
                <v-icon
                  :title="`${item.status == 'active' ? 'Active' : 'Disabled'}`"
                  v-if="item.id"
                  :icon="`${
                    item.status == 'active' ? mdiEyeCheckOutline : mdiEyeRemoveOutline
                  }`"
                  :color="`${item.status == 'active' ? 'success' : 'error'}`"
                  class="my-auto mr-2"
                ></v-icon>
                <v-autocomplete
                  hide-details="auto"
                  label="Type"
                  variant="outlined"
                  v-model="item.type"
                  :items="incidentTypes"
                  item-title="value"
                  item-value="id"
                  density="compact"
                  :disabled="item.id < 10"
                ></v-autocomplete>
              </div>
              <div class="v-col-5">
                <v-text-field
                  hide-details="auto"
                  v-model="item.title"
                  label="Incident Title"
                  variant="outlined"
                  density="compact"
                  :disabled="item.id < 10"
                ></v-text-field>
              </div>
              <div class="v-col-2 my-auto" v-if="item.id > 9 || !item.id">
                <v-btn
                  :title="`${
                    item.id
                      ? item.status == 'active'
                        ? 'Click to disable'
                        : 'Click to enable'
                      : 'Remove row'
                  }`"
                  :icon="`${
                    item.id
                      ? item.status == 'active'
                        ? mdiEyeOff
                        : mdiEyeRefresh
                      : mdiTrashCan
                  }`"
                  @click="deleteFn(index, item, 'incident')"
                  size="x-small"
                  :color="`${item && item.status == 'disabled' ? 'success' : 'error'}`"
                  class="mx-1 show-hover"
                  v-if="
                    authStore.user.role == 'superadmin' ||
                    authStore.capabilities?.includes('delete')
                  "
                ></v-btn>
              </div>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <AppSnackbar :options="sbOptions" />
  </v-container>
</template>
<script setup>
import { ref } from "vue";

import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackbar from "@/components/AppSnackBar.vue";
import {
  mdiTrashCan,
  mdiPlus,
  mdiEyeOff,
  mdiEyeCheckOutline,
  mdiEyeRemoveOutline,
  mdiEyeRefresh,
} from "@mdi/js";

const statusData = ref([]);
const conditionData = ref([]);
const incidentData = ref([]);
const authStore = useAuthStore();
const btnLoading = ref(false);

const statusTypes = ref([
  { id: "asset", value: "Asset" },
  { id: "maintenance", value: "Maintenance" },
  { id: "incident", value: "Incident" },
]);

const conditionTypes = ref([{ id: "condition-type", value: "Asset" }]);
const incidentTypes = ref([{ id: "incident-type", value: "Incident" }]);

const AddNewStatus = () => {
  statusData.value.push({ title: null, type: null });
};

const AddNewCondition = () => {
  conditionData.value.push({ title: null, type: "condition-type" });
};

const AddNewIncident = () => {
  incidentData.value.push({ title: null, type: "incident-type" });
};

const page = ref({ loading: true });
const sbOptions = ref({
  status: false,
  type: "info",
  text: null,
});
const fetchPage = async () => {
  statusData.value = [];
  conditionData.value = [];
  incidentData.value = [];
  await clientKey(authStore.token)
    .get("/api/fetch-global/setup-status")
    .then((res) => {
      if (res.data.length > 0) {
        res.data.map((o) => {
          if (o.type == "condition-type") {
            conditionData.value.push(o);
          } else if (o.type == "incident-type") {
            incidentData.value.push(o);
          } else {
            statusData.value.push(o);
          }
        });
      }

      page.value.loading = false;
    })
    .catch((err) => {
      page.value.loading = false;
    });
};
fetchPage();

const deleteFn = async (index, item, type) => {
  if (item.id) {
    sbOptions.value = {
      status: true,
      type: "info",
      text: "Please wait...",
    };

    let storeData = {
      type: "status",
      item: item,
      profile_id: authStore.user.profile.id,
    };
    storeUpdate(storeData);
  } else {
    if (type == "status") {
      let rows = statusData.value;
      rows.splice(index, 1);
      statusData.value = [...rows];
    } else if (type == "condition") {
      let rows = conditionData.value;
      rows.splice(index, 1);
      conditionData.value = [...rows];
    } else {
      let rows = incidentData.value;
      rows.splice(index, 1);
      incidentData.value = [...rows];
    }
  }
};

const savePage = (type) => {
  sbOptions.value = {
    status: true,
    type: "info",
    text: "Please wait...",
  };

  if (type == "status") {
    if (statusData.value.length == 0) {
      return;
    }
    statusFn(statusData.value);
  } else if (type == "condition") {
    if (conditionData.value.length == 0) {
      return;
    }
    statusFn(conditionData.value);
  } else if (type == "incident") {
    if (incidentData.value.length == 0) {
      return;
    }
    statusFn(incidentData.value);
  }
};

const statusFn = (objData) => {
  let updateData = [];
  let newData = [];
  objData.map((o, i) => {
    if (o.title && o.type) {
      delete o["profile_id"];
      o.profile_id = authStore.user.profile.id;
      if (o.id) {
        delete o["created_at"];
        delete o["updated_at"];

        updateData.push(o);
      } else {
        newData.push(o);
      }
    }
  });
  if (newData.length == 0 && updateData.length == 0) {
    sbOptions.value = {
      status: true,
      type: "error",
      text: "Kindly fill-up the fields.",
    };
    return;
  }

  let storeData = {
    type: "store-update",
    new: newData,
    update: updateData,
    profile_id: authStore.user.profile.id,
  };
  storeUpdate(storeData);
};

const storeUpdate = (storeData) => {
  clientKey(authStore.token)
    .post("/api/store-update/setup-status", storeData)
    .then((res) => {
      fetchPage().then(() => {
        sbOptions.value = {
          status: true,
          type: "success",
          text: res.data.message,
        };
      });
    })
    .catch((err) => {
      page.value.loading = false;
    });
};
</script>
<style>
.parent-row-show:hover .show-hover {
  display: block;
}
.show-hover {
  display: none;
}
</style>
