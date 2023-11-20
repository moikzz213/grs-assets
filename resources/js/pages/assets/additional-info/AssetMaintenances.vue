<template>
  <v-row>
    <div class="v-col-12 pt-0 pb-2">
      
      <v-btn @click="openNewMaintenance" class="mb-3" size="small" v-if="assetDetail.status_id != 2">
        New Maintenance <v-icon class="ml-2" :icon="mdiPlus" ></v-icon>
      </v-btn>
      <v-card>
        <v-table density="compact">
          <thead>
            <tr>
              <th class="text-left text-primary">ISR ID</th>
              <th class="text-left text-primary">Urgency</th>  
              <th class="text-left text-primary">Priority</th>  
              <th class="text-left text-primary">Reported by</th>
              <th class="text-left text-primary">Handled by</th>
              <th class="text-left text-primary">Date Reported</th>
              <th class="text-left text-primary">Date Completed</th>  
              <th class="text-left text-primary">Status</th>
              <th class="text-left text-primary"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.asset.maintenance" :key="item.id">
              <td>ISR-2{{ pad(item.id) }}</td> 
              <td>{{ statusTitle(item.urgency) }}</td>
              <td>{{ statusTitle(item.priority) }}</td> 
              <td>{{ item.profile?.display_name }}</td>
              <td>{{ item.handle_by?.display_name }}</td>
              <td>{{ useFormatDate(item.created_at) }}</td>
              <td>{{ item.date_closed ? useFormatDate(item.date_closed) : '' }}</td> 
              <td>{{ item.status?.title }}</td>
              <td><v-btn color="info" size="x-small" @click="openDetails(item.id)">Details</v-btn></td>
            </tr>
          </tbody>
        </v-table>
        <v-sheet
          v-if="props.asset.maintenance && props.asset.maintenance.length == 0"
          class="pa-3 text-center"
          >No record at the moment</v-sheet
        >
      </v-card>
    </div>
  </v-row>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { mdiPlus, mdiClose } from "@mdi/js";
import { useRouter } from "vue-router";
import { useFormatDate } from "@/composables/formatDate.js";

const router = useRouter();
const props = defineProps({
  asset: {
    type: Object,
    default: null,
  },
}); 
 

// maintenance
const openNewMaintenance = () => {
    router.push({
      name: "NewMaintenance",
      query: {
        type: "details",
        asset_code: assetDetail.value.asset_code,
        asset_id: assetDetail.value.id,
        comp: assetDetail.value.company_id,
        loc: assetDetail.value.location_id,
        title: assetDetail.value.asset_name,
      },
    });
};

const openDetails = (id) => {
  router
        .push({
            name: "EditMaintenance",
            params: {
                id: id,
            },
            query: { type: "details", pd:'ox2rKaIYFhBOQW31j6kkiMrJ4KfnYpLmwJhMzBFwPBlVjgGR4g', v:1, vv: 'xK2sX1sDL1BBAvmuoPpWoNRgc4wzbdZ9F2OXAKrS3N2g65xNgy' },
        })
        .catch((err) => {
            console.log(err);
        });
};

const statusTitle = (v) =>{
  if(v){
    if(v == 1){
      return 'Normal';
    }else if(v == 2){
      return 'Medium';
    }else{
      return 'High';
    }
  }
  return '';
}
const pad = (v, size = 5) =>{
      let s = "0000" + v;
      return s.substring(s.length - size);
}; 
const assetDetail = ref({});

watch(
    () => props.asset,
    (newValue, oldValue) => {
      assetDetail.value = newValue;
      console.log("assetDetail.value",assetDetail.value);
    },
    { deep: true }
);
</script>
