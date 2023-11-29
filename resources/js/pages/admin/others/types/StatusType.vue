<template>
  <div class="v-col-12 v-col-md-8">
    <v-card>
      <v-card-title>
        <div class="d-flex align-center">
          <v-btn
            :icon="mdiPlus"
            @click="addNewStatus"
            size="x-small"
            color="white"
            class="mr-3"
            v-if="
              authStore.user.role == 'superadmin' ||
              authStore.capabilities?.includes('add')
            "
          ></v-btn>
          <div class="text-capitalize">{{ props.type.replace("-", " ") }} List</div>
          <v-btn
            :disabled="!isValid"
            :loading="loadingSave"
            @click="save"
            color="primary"
            class="ml-auto"
            v-if="
              authStore.user.role == 'superadmin' ||
              authStore.capabilities?.includes('edit')
            "
            >Save</v-btn
          >
        </div>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-row v-for="(item, index) in statusListByType" :key="index">
          <div class="v-col">
            <v-text-field
              hide-details="auto"
              v-model="item.title"
              :label="`${props.type.charAt(0).toUpperCase() + props.type.slice(1)} Title`"
              variant="outlined"
              density="compact"
              :disabled="item.id < 10"
            ></v-text-field>
          </div>
          <div class="v-col" v-if="props.type == 'urgency'">
            <v-text-field
              hide-details="auto"
              v-model="item.notification_interval"
              :label="`${mobile ? 'Notif. (days)' : 'Notification Interval (days)'} `"
              variant="outlined"
              density="compact"
              :disabled="item.id < 10"
            ></v-text-field>
          </div>
          <div
            class="v-col px-3 d-flex align-center justify-center"
            style="width: 100%; max-width: 150px; min-width: 150px"
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
import { ref, computed, watch } from "vue";
import { mdiPlus } from "@mdi/js";
import AppStatusDropDown from "@/components/AppStatusDropDown.vue";
import AppSnackbar from "@/components/AppSnackbar.vue";
import { useDisplay } from "vuetify";

// responsiveness
const { mobile } = useDisplay();

// props
const props = defineProps({
  type: {
    type: String,
    default: "",
  },
});

// auth
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// ui
const sbOptions = ref({});

// status
import { useStatusStore } from "@/stores/status";
const statusStore = useStatusStore();
// urgency list
const statusListByType = ref(statusStore.statusListByType);
if (statusStore.list.length < 1) {
  statusStore.getStatuses(authStore.token).then(() => {
    statusStore.filterStatusByType(props.type).then(() => {
      statusListByType.value = statusStore.statusListByType;
    });
  });
}

// watch props
watch(
  () => props.type,
  (newVal) => {
    console.log("newVal", newVal);
    statusStore.filterStatusByType(props.type).then(() => {
      statusListByType.value = statusStore.statusListByType;
    });
  }
);

// add list
const addNewStatus = () => {
  statusListByType.value.push({
    title: null,
    type: null,
    status: "active",
    type: props.type,
    notification_interval: props.type == "urgency" ? 1 : null,
  });
};

// remove item from list
const removeIndex = (i) => {
  statusListByType.value = statusListByType.value.filter(function (item, index) {
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

  // update from status store
  let data = {
    id: v.model_id,
    type: props.type,
    status: v.state,
  };
  statusStore
    .updateStatus(data, authStore.token)
    .then((res) => {
      statusStore.getStatuses(authStore.token).then(() => {
        statusStore.filterStatusByType(props.type).then(() => {
          statusListByType.value = statusStore.statusListByType;
          // deactivate loading
          loadingAppStatusDropDown.value = [];
          sbOptions.value = {
            status: true,
            type: "success",
            text: "Status has been saved",
          };
        });
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

const isValid = computed(() => {
  // check if all fields have title
  let status = true;
  statusListByType.value.map((ul) => {
    if (!ul.title || ul.title.length == 0) {
      status = false;
    }
  });
  return status;
});

const save = () => {
  loadingSave.value = true;
  let data = {
    list: statusListByType.value,
  };
  statusStore
    .saveStatusList(data, authStore.token)
    .then(() => {
      statusStore.getStatuses(authStore.token).then(() => {
        statusStore.filterStatusByType(props.type).then(() => {
          statusListByType.value = statusStore.statusListByType;
          loadingSave.value = false;
          sbOptions.value = {
            status: true,
            type: "success",
            text: "Status list has been saved",
          };
        });
      });
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
