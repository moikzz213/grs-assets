<template>
  <v-row>
    <div class="v-col-12 pt-0 pb-2">
      <v-btn @click="() => openWarrantyDialog()" class="mb-3" size="small">
        New Warranty <v-icon class="ml-2" :icon="mdiPlus"></v-icon>
      </v-btn>
      <v-card>
        <v-table density="compact">
          <thead>
            <tr>
              <th class="text-left text-primary">#</th>
              <th class="text-left text-primary">Title</th>
              <th class="text-left text-primary">Warranty Start</th>
              <th class="text-left text-primary">Warranty End</th>
              <th class="text-left text-primary">Vendor</th>
              <th class="text-left text-primary">Vendor Start</th>
              <th class="text-left text-primary">Vendor End</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in warranties"
              :key="item.id"
              @click="() => openWarrantyDialog(item)"
              class="cursor-pointer table-cell-hover"
            >
              <td>{{ index + 1 }}</td>
              <td>{{ item.title }}</td>
              <td>
                {{
                  item.warranty_start_date &&
                  dayjs(item.warranty_start_date).format("MMM. DD, YYYY")
                }}
              </td>
              <td>
                {{
                  item.warranty_end_date &&
                  dayjs(item.warranty_end_date).format("MMM. DD, YYYY")
                }}
              </td>
              <td>{{ item.vendor && item.vendor.title }}</td>
              <td>
                {{
                  item.vendor_start_date &&
                  dayjs(item.vendor_start_date).format("MMM. DD, YYYY")
                }}
              </td>
              <td>
                {{
                  item.vendor_end_date &&
                  dayjs(item.vendor_end_date).format("MMM. DD, YYYY")
                }}
              </td>
            </tr>
          </tbody>
        </v-table>
        <v-sheet v-if="warranties.length == 0" class="pa-3 text-center"
          >No record at the moment</v-sheet
        >
      </v-card>
    </div>
    <v-dialog v-model="dialogWarranty.status" persistent width="1000">
      <v-card>
        <v-card-title
          class="text-capitalize mb-3 d-flex justify-space-between align-center"
        >
          {{ dialogWarranty.action + " Warranty" }}
          <v-btn
            :icon="mdiClose"
            variant="flat"
            size="small"
            @click="dialogWarranty.status = false"
          ></v-btn>
        </v-card-title>
        <v-card-text>
          <Form as="v-form" :validation-schema="warrantyValidation">
            <v-row class="mb-0">
              <div class="v-col-12 pt-0 pb-2">Warranty</div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Warranty Title"
                  v-slot="{ field, errors }"
                  v-model="dialogWarranty.data.title"
                >
                  <v-text-field
                    v-model="dialogWarranty.data.title"
                    v-bind="field"
                    label="Warranty Title"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Warranty Start Date"
                  v-slot="{ field, errors }"
                  v-model="dialogWarranty.data.warranty_start_date"
                >
                  <v-text-field
                    type="date"
                    v-model="dialogWarranty.data.warranty_start_date"
                    v-bind="field"
                    label="Warranty Start Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Warranty End Date"
                  v-slot="{ field, errors }"
                  v-model="dialogWarranty.data.warranty_end_date"
                >
                  <v-text-field
                    type="date"
                    v-model="dialogWarranty.data.warranty_end_date"
                    v-bind="field"
                    label="Warranty End Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 pt-0 pb-2">Vendor Warranty</div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <v-autocomplete
                  v-model="dialogWarranty.data.vendor_id"
                  :items="vendorStore.list"
                  item-title="title"
                  item-value="id"
                  label="Vendor Warranty"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Vendor Warranty is required']"
                />
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Vendor Start Date"
                  v-slot="{ field, errors }"
                  v-model="dialogWarranty.data.vendor_start_date"
                >
                  <v-text-field
                    type="date"
                    v-model="dialogWarranty.data.vendor_start_date"
                    v-bind="field"
                    label="Vendor Start Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Vendor End Date"
                  v-slot="{ field, errors }"
                  v-model="dialogWarranty.data.vendor_end_date"
                >
                  <v-text-field
                    type="date"
                    v-model="dialogWarranty.data.vendor_end_date"
                    v-bind="field"
                    label="Vendor End Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 pt-0 pb-2 d-flex justify-end">
                <v-btn
                  color="primary"
                  :loading="dialogWarranty.loading"
                  @click="saveWarranty"
                  >Save</v-btn
                >
              </div>
            </v-row>
          </Form>
        </v-card-text>
      </v-card>
    </v-dialog>
    <AppSnackBar :options="sbOptions" />
  </v-row>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Form, Field } from "vee-validate";
import { clientKey } from "@/services/axiosToken";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { mdiPlus, mdiClose } from "@mdi/js";
import dayjs from "dayjs";
import * as yup from "yup";
import { useRoute } from "vue-router";

const route = useRoute();

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// vendor
import { useVendorStore } from "@/stores/vendors";
const vendorStore = useVendorStore();
if (vendorStore.list.length == 0) {
  vendorStore.getVendors(authStore.token);
}

// snackbar
const sbOptions = ref({});

const props = defineProps({
  page: {
    type: String,
    default: "add",
  },
  asset: {
    type: Object,
    default: null,
  },
});

const warrantyValidation = yup.object({
  "Warranty Title": yup.string().required().max(150),
  "Warranty Start Date": yup.string().nullable(),
  "Warranty End Date": yup.string().nullable(),
  "Vendor Start Date": yup.string().nullable(),
  "Vendor End Date": yup.string().nullable(),
});

// warranty
const dialogWarranty = ref({
  status: false,
  action: "add",
  loading: false,
  data: {},
});
const warranties = ref([]);

watch(
  () => props.asset,
  (newVal) => {
    warranties.value = newVal.warranty;
  }
);

const openWarrantyDialog = (warranty = null) => {
  dialogWarranty.value.status = true;
  if (warranty) {
    dialogWarranty.value.action = "edit";
    dialogWarranty.value.data = Object.assign({}, warranty);
  } else {
    dialogWarranty.value.action = "add";
    dialogWarranty.value.data = {};
  }
};
const saveWarranty = async () => {
  dialogWarranty.value.loading = true;
  let data = {
    id: dialogWarranty.value.data.id,
    asset_id: route.params.id,
    warranty_title: dialogWarranty.value.data.title,
    warranty_start_date: dialogWarranty.value.data.warranty_start_date,
    warranty_end_date: dialogWarranty.value.data.warranty_end_date,
    warranty_vendor_id: dialogWarranty.value.data.vendor_id,
    vendor_start_date: dialogWarranty.value.data.vendor_start_date,
    vendor_end_date: dialogWarranty.value.data.vendor_end_date,
  };
  await clientKey(authStore.token)
    .post("/api/asset/save-warranty", data)
    .then((res) => {
      getWarranties().then(() => {
        sbOptions.value = {
          status: true,
          type: "success",
          text: res.data.message,
        };
        dialogWarranty.value.loading = false;
        dialogWarranty.value.status = false;
      });
    })
    .catch((err) => {
      dialogWarranty.value.loading = false;
      sbOptions.value = {
        status: true,
        type: "eroor",
        text: "Error while saving warranty",
      };
      console.log("err", err);
    });
};
const getWarranties = async () => {
  await clientKey(authStore.token)
    .get("/api/asset/warranty/" + route.params.id)
    .then((res) => {
      warranties.value = res.data;
      dialogWarranty.value.loading = false;
    })
    .catch((err) => {
      dialogWarranty.value.loading = false;
      console.log("err", err);
    });
};
</script>
