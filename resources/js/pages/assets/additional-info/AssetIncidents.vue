<template>
  <v-row>
    <div class="v-col-12 pt-0 pb-2">
      <v-card>
        <v-table density="compact">
          <thead>
            <tr>
              <th class="text-left text-primary">ISR ID</th>
              <th class="text-left text-primary">Type</th>
              <th class="text-left text-primary">Urgency</th>
              <th class="text-left text-primary">Priority</th>
              <th class="text-left text-primary">Reported by</th>
              <th class="text-left text-primary">Handled by</th>
              <th class="text-left text-primary">Date Reported</th>
              <th class="text-left text-primary">Date Completed</th>
              <th class="text-left text-primary">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.asset.incidents" :key="item.id">
              <td
                class="text-decoration-underline d-flex align-center cursor-pointer"
                @click="openDetails(item.id)"
              >
                ISR-2{{ pad(item.id) }}
                <v-icon class="ml-1" size="small" :icon="mdiEye"></v-icon>
              </td>
              <td>{{ item.type?.title }}</td>
              <td>{{ item.urgency?.title }}</td>
              <td>{{ item.priority ? statusTitle(item.priority) : "-" }}</td>
              <td>{{ item.profile?.display_name }}</td>
              <td>
                {{ item.handled_by?.display_name ? item.handled_by?.display_name : "-" }}
              </td>
              <td>
                {{
                  item.created_at ? dayjs(item.created_at).format("MMM. DD, YYYY") : "-"
                }}
              </td>
              <td>
                {{
                  item.date_closed ? dayjs(item.date_closed).format("MMM. DD, YYYY") : "-"
                }}
              </td>
              <td>{{ item.status?.title }}</td>
            </tr>
          </tbody>
        </v-table>
        <v-sheet
          v-if="props.asset.incidents && props.asset.incidents.length == 0"
          class="pa-3 text-center"
          >No record at the moment</v-sheet
        >
      </v-card>
    </div>
  </v-row>
</template>

<script setup>
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import dayjs from "dayjs";
import { mdiEye } from "@mdi/js";
const router = useRouter();
const props = defineProps({
  asset: {
    type: Object,
    default: null,
  },
});

const openDetails = (id) => {
  router
    .push({
      name: "EditIncident",
      params: {
        id: id,
      },
      query: {
        type: "details",
      },
    })
    .catch((err) => {
      console.log(err);
    });
};

const statusTitle = (v) => {
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
const pad = (v, size = 5) => {
  let s = "0000" + v;
  return s.substring(s.length - size);
};
const assetDetail = ref({});

watch(
  () => props.asset,
  (newValue, oldValue) => {
    assetDetail.value = newValue;
     
  },
  { deep: true }
);
</script>
