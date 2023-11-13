<template>
  <v-card :loading="loadingAsset" max-width="1600px" class="mx-auto elevation-0">
    <Form as="v-form" :validation-schema="validation">
      <v-card-title class="pb-6 pt-3 d-flex align-center justify-space-between">
        <div class="text-primary text-capitalize">
          {{ formTitle }}
        </div>
        <div class="d-flex align-center">
          <v-btn color="primary" class="mr-3" :loading="loadingAsset" @click="fillAsset"
            >fill form</v-btn
          >
          <v-btn color="primary" :loading="loadingAsset" @click="saveAsset">Save</v-btn>
        </div>
      </v-card-title>
      <v-card-text>
        <v-row>
          <div class="v-col-12 font-weight-bold">Asset Info</div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field name="Name" v-slot="{ field, errors }" v-model="assetObj.asset_name">
              <v-text-field
                v-model="assetObj.asset_name"
                v-bind="field"
                label="Name"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field
              name="Serial Number"
              v-slot="{ field, errors }"
              v-model="assetObj.serial_number"
            >
              <v-text-field
                v-model="assetObj.serial_number"
                v-bind="field"
                label="Serial Number"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field
              name="Section code"
              v-slot="{ field, errors }"
              v-model="assetObj.section_code"
            >
              <v-text-field
                v-model="assetObj.section_code"
                v-bind="field"
                label="Section code (optional)"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <!-- item-title="title"
            item-value="id" -->
            <!-- <Field
              name="Category"
              v-slot="{ field, errors }"
              v-model="assetObj.category_id"
            >
              <v-autocomplete
                v-model="assetObj.category_id"
                v-bind="field"
                :items="categoryStore.list.map((cs) => cs.title)"
                label="Category"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field> -->
            <v-autocomplete
              v-model="assetObj.category_id"
              :items="categoryStore.list"
              item-title="title"
              item-value="id"
              label="Category"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Category is required']"
            />
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <v-autocomplete
              v-model="assetObj.company_id"
              :items="companyStore.list"
              item-title="title"
              item-value="id"
              label="Company"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Company is required']"
            />
            <!-- :error-messages="errors" -->
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <v-autocomplete
              v-model="assetObj.location_id"
              :items="locationStore.list"
              item-title="title"
              item-value="id"
              label="Location"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Location is required']"
            />
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field
              name="Asset Code"
              v-slot="{ field, errors }"
              v-model="assetObj.asset_code"
            >
              <v-text-field
                readonly
                v-model="assetObj.asset_code"
                v-bind="field"
                label="Asset Code (Auto Generated)"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <v-autocomplete
              v-model="assetObj.status_id"
              :items="statusStore.assets"
              item-title="title"
              item-value="id"
              label="Asset Status"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Asset Status is required']"
            />
          </div>
        </v-row>
        <v-row>
          <div v-if="props.page == 'edit'" class="v-col-12 pt-0 font-weight-bold">
            Additional Info
          </div>
          <div v-if="props.page == 'edit'" class="v-col-12 pt-0">
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('specification')"
              :color="selectedTab == 'specification' ? 'primary' : 'white'"
              >Specification</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('purchase')"
              :color="selectedTab == 'purchase' ? 'primary' : 'white'"
              >Purchase</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('financial')"
              :color="selectedTab == 'financial' ? 'primary' : 'white'"
              >Financial</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('warranty')"
              :color="selectedTab == 'warranty' ? 'primary' : 'white'"
              >Warranty</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('allotted')"
              :color="selectedTab == 'allotted' ? 'primary' : 'white'"
              >Allotted</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('maintenance')"
              :color="selectedTab == 'maintenance' ? 'primary' : 'white'"
              >Maintenance</v-btn
            >
          </div>
          <div class="v-col-12 pt-0 text-capitalize">
            {{ selectedTab + " Information" }}
          </div>
          <!-- Additional Information -->
          <div class="v-col-12" v-show="selectedTab == 'specification'">
            <v-row>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Specification"
                  v-slot="{ field, errors }"
                  v-model="assetObj.specification"
                >
                  <v-text-field
                    v-model="assetObj.specification"
                    v-bind="field"
                    label="Specification"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-autocomplete
                  v-model="assetObj.model_id"
                  :items="modelStore.list"
                  item-title="title"
                  item-value="id"
                  label="Model"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Model is required']"
                />
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-autocomplete
                  v-model="assetObj.brand_id"
                  :items="brandStore.list"
                  item-title="title"
                  item-value="id"
                  label="Brand"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Brand is required']"
                />
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-autocomplete
                  v-model="assetObj.condition_id"
                  :items="statusStore.conditions"
                  item-title="title"
                  item-value="id"
                  label="Condition"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Condition is required']"
                />
              </div>
            </v-row>
          </div>
          <!-- Purchase Information -->
          <div class="v-col-12" v-show="selectedTab == 'purchase'">
            <v-row>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field name="Price" v-slot="{ field, errors }" v-model="assetObj.price">
                  <v-text-field
                    v-model="assetObj.price"
                    v-bind="field"
                    label="Price"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-autocomplete
                  v-model="assetObj.vendor_id"
                  :items="vendorStore.list"
                  item-title="title"
                  item-value="id"
                  label="Vendor"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Vendor is required']"
                />
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="PO Number"
                  v-slot="{ field, errors }"
                  v-model="assetObj.po_number"
                >
                  <v-text-field
                    v-model="assetObj.po_number"
                    v-bind="field"
                    label="PO Number"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Purchase Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.purchased_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.purchased_date"
                    v-bind="field"
                    label="Purchase Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
            </v-row>
          </div>
          <!-- Financial Information -->
          <div class="v-col-12" v-show="selectedTab == 'financial'">
            <v-row>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Capitalization Price"
                  v-slot="{ field, errors }"
                  v-model="assetObj.capitalization_price"
                >
                  <v-text-field
                    v-model="assetObj.capitalization_price"
                    v-bind="field"
                    label="Capitalization Price"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Depreciation Percentage"
                  v-slot="{ field, errors }"
                  v-model="assetObj.depreciation_percentage"
                >
                  <v-text-field
                    v-model="assetObj.depreciation_percentage"
                    v-bind="field"
                    label="Depreciation Percentage"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Scrap Value"
                  v-slot="{ field, errors }"
                  v-model="assetObj.scrap_value"
                >
                  <v-text-field
                    v-model="assetObj.scrap_value"
                    v-bind="field"
                    label="Scrap Value"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Scrap Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.scrap_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.scrap_date"
                    v-bind="field"
                    label="Scrap Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Capitalization Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.capitalization_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.capitalization_date"
                    v-bind="field"
                    label="Capitalization Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="End of Life"
                  v-slot="{ field, errors }"
                  v-model="assetObj.end_of_life"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.end_of_life"
                    v-bind="field"
                    label="End of Life"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
              </div>
            </v-row>
          </div>
          <!-- Warranty Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'warranty'">
            <v-row>
              <div class="v-col-12 pt-0 pb-2">
                <v-btn @click="() => openWarrantyDialog()" class="mb-3" size="small">
                  New Warranty <v-icon class="ml-2" :icon="mdiPlus"></v-icon>
                </v-btn>
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
                      <v-row class="mb-0">
                        <div class="v-col-12 pt-0 pb-2">Warranty</div>
                        <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                          <Field
                            name="Warranty"
                            v-slot="{ field, errors }"
                            v-model="dialogWarranty.data.warranty_title"
                          >
                            <v-text-field
                              v-model="dialogWarranty.data.warranty_title"
                              v-bind="field"
                              label="Warranty"
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
                            v-model="dialogWarranty.data.warranty_vendor_id"
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
                          <v-btn color="primary" @click="saveWarranty">Save</v-btn>
                        </div>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-dialog>
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
                      <tr v-for="(item, index) in warranties" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.warranty_start_date }}</td>
                        <td>{{ item.warranty_end_date }}</td>
                        <td>{{ item.vendor_id }}</td>
                        <td>{{ item.vendor_start_date }}</td>
                        <td>{{ item.vendor_end_date }}</td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-card>
              </div>
            </v-row>
          </div>
          <!-- Allotted Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'allotted'">
            <v-row>
              <div class="v-col-12 pt-0 pb-2">
                <v-card>
                  <v-table density="compact">
                    <thead>
                      <tr>
                        <th class="text-left text-primary">#</th>
                        <th class="text-left text-primary">Alloted to</th>
                        <th class="text-left text-primary">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in [
                          {
                            id: 1,
                            title: 'Grandiose Barsha',
                            remarks: 'Static Remarks',
                          },
                          {
                            id: 2,
                            title: 'GAG HO',
                            remarks: 'Static Remarks',
                          },
                        ]"
                        :key="item.id"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.title }}</td>
                        <td>{{ item.remarks }}</td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-card>
              </div>
            </v-row>
          </div>
          <!-- Maintenance Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'maintenance'">
            <v-row>
              <div class="v-col-12 pt-0 pb-2">
                <v-btn @click="openNewMaintenance" class="mb-3" size="small">
                  New Maintenance <v-icon class="ml-2" :icon="mdiPlus"></v-icon>
                </v-btn>
                <v-card>
                  <v-table density="compact">
                    <thead>
                      <tr>
                        <th class="text-left text-primary">INC ID</th>
                        <th class="text-left text-primary">Location</th>
                        <th class="text-left text-primary">Handled by</th>
                        <th class="text-left text-primary">Service Type</th>
                        <th class="text-left text-primary">Date Start</th>
                        <th class="text-left text-primary">Date End</th>
                        <th class="text-left text-primary">Cost</th>
                        <th class="text-left text-primary">Status</th>
                        <th class="text-left text-primary">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in [
                          {
                            id: 1,
                            location: 'Grandiose Barsha',
                            handle_by: 'Romel Indemne',
                            service_type: 'complaint',
                            date_start: '2023-11-11',
                            date_closed: '2023-11-11',
                            cost: '550',
                            status: 'halo-halo',
                            remarks: 'Static remarks',
                          },
                          {
                            id: 2,
                            location: 'GAG HO',
                            handle_by: 'Romel Indemne',
                            service_type: 'complaint',
                            date_start: '2023-11-11',
                            date_closed: '2023-11-11',
                            cost: '550',
                            status: 'halo-halo',
                            remarks: 'Static remarks',
                          },
                        ]"
                        :key="item.id"
                      >
                        <td>{{ item.id }}</td>
                        <td>{{ item.location }}</td>
                        <td>{{ item.handle_by }}</td>
                        <td>{{ item.service_type }}</td>
                        <td>{{ item.date_start }}</td>
                        <td>{{ item.date_closed }}</td>
                        <td>{{ item.cost }}</td>
                        <td>{{ item.status }}</td>
                        <td>{{ item.remarks }}</td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-card>
              </div>
            </v-row>
          </div>

          <div class="v-col-12 pt-0 font-weight-bold">Attachment</div>
          <div class="v-col-12 pt-0">
            <v-row>
              <div class="v-col-12 pb-2">
                <Studio :options="{ multiSelect: true }" @select="studioSelectResponse" />
              </div>
            </v-row>
            <v-row v-if="selectedFiles.length > 0" class="px-1">
              <div
                v-for="(file, index) in selectedFiles"
                :key="file.id"
                class="v-col-12 v-col-md-2 pa-2"
                style="position: relative"
              >
                <v-btn
                  style="position: absolute; top: 0; right: 0; z-index: 1"
                  :icon="mdiClose"
                  size="26px"
                  color="error"
                  @click="() => removeAttachment(file.id)"
                >
                </v-btn>
                <v-card @click="() => openAttachment(index)">
                  <v-img :src="baseURL + '/file/' + file.path" cover aspect-ratio="1">
                  </v-img>
                </v-card>
              </div>
              <v-dialog v-model="dialogAttachment" width="95%" max-width="900">
                <v-card class="bg-black">
                  <v-carousel
                    hide-delimiter-background
                    show-arrows="hover"
                    height="680px"
                    v-model="currentSlider"
                  >
                    <v-carousel-item
                      v-for="(item, i) in selectedFiles"
                      :key="i"
                      reverse-transition="fade"
                      transition="fade"
                    >
                      <div
                        style="height: 680px; width: 100%"
                        class="d-flex align-center justify-center"
                      >
                        <v-img :src="baseURL + '/file/' + item.path"></v-img>
                      </div>
                    </v-carousel-item>
                  </v-carousel>
                </v-card>
              </v-dialog>
            </v-row>
            <v-row v-else>
              <div class="v-col-12 pt-0 pb-2">
                <v-sheet color="grey-lighten-4" class="pa-6 text-center">
                  Attachment is empty at the moment.
                </v-sheet>
              </div>
            </v-row>
          </div>
        </v-row>
      </v-card-text>
    </Form>
    <AppSnackBar :options="sbOptions" />
  </v-card>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { clientKey } from "@/services/axiosToken";
import AppSnackBar from "@/components/AppSnackBar.vue";
import Studio from "@/studio/Studio.vue";
import { useRouter } from "vue-router";
import { mdiPlus, mdiClose } from "@mdi/js";
const router = useRouter();

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

// snackbar
const sbOptions = ref({});

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// companies
import { useCompanyStore } from "@/stores/companies";
const companyStore = useCompanyStore();
if (companyStore.list.length == 0) {
  companyStore.getCompanies(authStore.token);
}

// categories
import { useCategoryStore } from "@/stores/categories";
const categoryStore = useCategoryStore();
if (categoryStore.list.length == 0) {
  categoryStore.getCategories(authStore.token);
}

// locations
import { useLocationStore } from "@/stores/locations";
const locationStore = useLocationStore();
if (locationStore.list.length == 0) {
  locationStore.getLocations(authStore.token);
}

// brands
import { useBrandStore } from "@/stores/brands";
const brandStore = useBrandStore();
if (brandStore.list.length == 0) {
  brandStore.getBrands(authStore.token);
}

// models
import { useModelStore } from "@/stores/models";
const modelStore = useModelStore();
if (modelStore.list.length == 0) {
  modelStore.getModels(authStore.token);
}

// status
import { useStatusStore } from "@/stores/status";
const statusStore = useStatusStore();
if (statusStore.list.length == 0 || statusStore.conditions.length == 0) {
  statusStore.getStatuses(authStore.token);
}

// vendor
import { useVendorStore } from "@/stores/vendors";
const vendorStore = useVendorStore();
if (vendorStore.list.length == 0) {
  vendorStore.getVendors(authStore.token);
}

// ui
const formTitle = ref("Add Asset");
const baseURL = ref(window.location.origin);

// tabs
const selectedTab = ref("specification"); // additional, purchase, financial, allotted, warranty, maintenance
const changeTab = (tab) => {
  selectedTab.value = tab;
};

// asset
let validation = yup.object({
  // add
  Name: yup.string().required().max(150),
  "Serial Number": yup.string().max(80).nullable(),
  "Asset Code": yup.string().max(50).nullable(),
  "Section code": yup.string().max(30).nullable(),
  Category: yup.string().required(),
  Company: yup.string().required(),
  Location: yup.string().required(),
  "Asset Status": yup.string().required(),
  Specification: yup.string().max(120).nullable(),
  Brand: yup.string().required(),
  Model: yup.string().required(),
  Condition: yup.string().required(),

  // edit
});

// fill asset form
const fillAsset = () => {
  assetObj.value = {
    ...assetObj.value,
    ...{
      company_id: 1,
      condition_id: 1,
      location_id: 1,
      category_id: 1,
      status_id: 4,
      brand_id: 1,
      model_id: 1,
      asset_name: "Test Asset",
      serial_number: "testserialnumber",
      section_code: "testsectioncode",
      specification: "Sample Specs",
      //   price: "550.00",
      //   po_number: "0001",
      //   purchased_date: "2023-11-11",
      //   remarks: "static remarks",
    },
  };
};

// save asset
const loadingAsset = ref(false);
const assetObj = ref({});
const selectedFiles = ref([]);
const selectedFilesIds = computed(() => selectedFiles.value.map((sf) => sf.id));

// set assetObj
assetObj.value = Object.assign({}, props.asset);
selectedFiles.value =
  props.asset && props.asset.attachments
    ? Object.assign([], props.asset.attachments)
    : [];
watch(
  () => props.asset,
  (newVal) => {
    assetObj.value = Object.assign({}, newVal);
    selectedFiles.value = newVal.attachments ? assetObj.value.attachments : [];
  }
);

const saveAsset = async () => {
  loadingAsset.value = true;
  assetObj.value.file_ids = selectedFilesIds.value;
  assetObj.value.company_code = companyStore.list.filter(
    (comp) => comp.id == assetObj.value.company_id
  )[0].code;
  assetObj.value.category_code = categoryStore.list.filter(
    (cat) => cat.id == assetObj.value.category_id
  )[0].code;
  await clientKey(authStore.token)
    .post("/api/asset/save", assetObj.value)
    .then((res) => {
      sbOptions.value = {
        status: true,
        type: "success",
        text: res.data.message,
      };
      loadingAsset.value = false;
      // redirect to edit
      if (props.page == "add") {
        router
          .push({
            name: "edit-asset",
            params: {
              id: res.data.asset.id,
            },
          })
          .catch((err) => {
            console.log("router.push", err);
          });
      } else {
        // emit
      }
    })
    .catch((err) => {
      console.log("saveAsset", err);
      sbOptions.value = {
        status: true,
        type: "error",
        text: "Error while saving asset",
      };
      loadingAsset.value = false;
    });
};

const studioSelectResponse = (v) => {
  let fileExist = null;
  v.map((sudioFile) => {
    fileExist = selectedFiles.value.find((f) => f.id == sudioFile.id);
    if (!fileExist) {
      selectedFiles.value.push(sudioFile);
    }
  });
};

// attachment slider
const dialogAttachment = ref(false);
const currentSlider = ref(1);
const openAttachment = (index) => {
  currentSlider.value = index;
  dialogAttachment.value = true;
};
const removeAttachment = (theId) => {
  selectedFiles.value = selectedFiles.value.filter((f) => f.id !== theId);
};

// maintenance
const openNewMaintenance = () => {
  //   router.push({
  //     name: "NewMaintenance",
  //     query: {
  //       type: "details",
  //       asset_code: "1231231",
  //       asset_id: 1,
  //       comp: 1,
  //       loc: 1,
  //       title: "test",
  //     },
  //   });
};

// warranty
const dialogWarranty = ref({
  status: false,
  action: "add",
  loading: false,
  data: {},
});
const warranties = ref([]);
const openWarrantyDialog = (warranty = null) => {
  dialogWarranty.value.status = true;
  if (warranty) {
    dialogWarranty.value.action = "edit";
    dialogWarranty.value.data = Object.assign({}, warranty);
  } else {
    dialogWarranty.value.action = "add";
  }
};
const saveWarranty = async () => {
  dialogWarranty.value.loading = true;
  let data = {
    id: dialogWarranty.value.data.id,
    asset_id: assetObj.value.id,
    warranty_title: dialogWarranty.value.data.warranty_title,
    warranty_start_date: dialogWarranty.value.data.warranty_start_date,
    warranty_end_date: dialogWarranty.value.data.warranty_end_date,
    warranty_vendor_id: dialogWarranty.value.data.warranty_vendor_id,
    vendor_start_date: dialogWarranty.value.data.vendor_start_date,
    vendor_end_date: dialogWarranty.value.data.vendor_end_date,
  };
  await clientKey(authStore.token)
    .post("/api/asset/save-warranty", data)
    .then((res) => {
      console.log("res", res.data);
      dialogWarranty.value.loading = false;
    })
    .catch((err) => {
      dialogWarranty.value.loading = false;
      console.log("err", err);
    });
};
const getWarranties = async () => {
  await clientKey(authStore.token)
    .get("/api/asset/warranty/" + assetObj.value.id)
    .then((res) => {
      warranties.value = res.data;
      dialogWarranty.value.loading = false;
    })
    .catch((err) => {
      dialogWarranty.value.loading = false;
      console.log("err", err);
    });
};
// watch
if (selectedTab == "edit") {
  getWarranties();
}
</script>
