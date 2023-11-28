<template>
  <div class="v-col-12 v-col-md-12">
    <v-card>
      <v-card-title>
        <div class="d-flex align-center">
          <v-btn
            :icon="mdiPlus"
            @click="addNewUrgency"
            size="x-small"
            color="white"
            class="mr-3"
            v-if="
              authStore.user.role == 'superadmin' ||
              authStore.capabilities?.includes('add')
            "
          ></v-btn>
          <div>Urgency List</div>
          <v-btn
            :loading="loadingSave"
            @click="save"
            color="primary"
            class="ml-auto"
            v-if="
              (authStore.user.role == 'superadmin' ||
                authStore.capabilities?.includes('edit')) &&
              statusStore.urgencies.length > 0
            "
            >Save</v-btn
          >
        </div>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-row v-for="(item, index) in urgencyList" :key="index">
          <div class="v-col">
            <v-text-field
              hide-details="auto"
              v-model="item.title"
              label="Urgency Title"
              variant="outlined"
              density="compact"
              :disabled="item.id < 10"
            ></v-text-field>
          </div>
          <div class="v-col">
            <v-text-field
              type="number"
              hide-details="auto"
              v-model="item.notification_interval"
              label="Notification Interval (in days)"
              variant="outlined"
              density="compact"
              :disabled="item.id < 10"
            ></v-text-field>
          </div>
          <div
            class="px-3 d-flex align-center justify-center"
            style="width: 100%; max-width: 150px"
          >
            <AppStatusDropDown
              v-if="item.id"
              @update="appStatusDropDownRes"
              :list="statusList"
              :current-state="item"
              :loading="loadingAppStatusDropDown.includes(item.id) == true ? true : false"
            />
            <v-btn v-else block color="error" @click="() => removeIndex(index)">
              remove
            </v-btn>
          </div>
        </v-row>
      </v-card-text>
    </v-card>
    <AppSnackbar :options="sbOptions" />
  </div>
</template>

<script setup>
import { ref } from "vue";
import { mdiPlus } from "@mdi/js";
import AppStatusDropDown from "@/components/AppStatusDropDown.vue";
import AppSnackbar from "@/components/AppSnackbar.vue";

// auth
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// ui
const sbOptions = ref({});

// status
import { useStatusStore } from "@/stores/status";
const statusStore = useStatusStore();
// urgency list
const urgencyList = ref(statusStore.urgencies);
if (statusStore.urgencies.length < 1) {
  statusStore.getStatuses(authStore.token).then(() => {
    urgencyList.value = statusStore.urgencies;
  });
}

// add list
const addNewUrgency = () => {
  urgencyList.value.push({
    title: null,
    type: null,
    status: "active",
    type: "urgency",
    notification_interval: 1,
  });
};

// remove item from list
const removeIndex = (i) => {
  urgencyList.value = urgencyList.value.filter(function (item, index) {
    return index !== i;
  });
};

// dropdown
const loadingSave = ref(false);
const loadingAppStatusDropDown = ref([]);
const statusList = ref([
  {
    title: "active",
    color: "success",
  },
  {
    title: "disabled",
    color: "error",
  },
]);
const appStatusDropDownRes = (v) => {
  // activate loading
  loadingAppStatusDropDown.value.push(v.model_id);

  // call status update from status store
  let data = {
    id: v.model_id,
    type: "urgency",
    status: v.state,
  };
  statusStore
    .updateStatus(data, authStore.token)
    .then((res) => {
      statusStore.getStatuses(authStore.token).then(() => {
        urgencyList.value = Object.assign([], statusStore.urgencies);
        // deactivate loading
        loadingAppStatusDropDown.value = [];
        sbOptions.value = {
          status: true,
          type: "success",
          text: "Status has been saved",
        };
      });
    })
    .catch((err) => {
      // deactivate loading
      loadingAppStatusDropDown.value = [];
      sbOptions.value = {
        status: true,
        type: "error",
        text: "Error while updating status",
      };
    });
};

const save = () => {
  // check if all fields have title
  urgencyList.value.map((ul) => {
    if (ul.title.length > 0) {
      sbOptions.value = {
        status: true,
        type: "error",
        text: "Urgency title is required",
      };
      return;
    }
  });

  loadingSave.value = true;
  let data = {
    list: urgencyList.value,
  };
  statusStore
    .saveStatusList(data, authStore.token)
    .then(() => {
      statusStore.getStatuses(authStore.token).then((getStatusesRes) => {
        urgencyList.value = Object.assign([], statusStore.urgencies);
      });
    })
    .finally(() => {
      loadingSave.value = false;
      sbOptions.value = {
        status: true,
        type: "success",
        text: "Status list has been saved",
      };
    })
    .catch((err) => {
      loadingSave.value = false;
      sbOptions.value = {
        status: true,
        type: "error",
        text: "Error while saving status list",
      };
    });
};
</script>
