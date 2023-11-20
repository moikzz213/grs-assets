<template>
  <div>
    <v-list-group v-if="item.subs" :value="item.title">
      <template v-slot:activator="{ props }">
        <v-list-item
          v-if="!groupNull || authStore.authRole == 'superadmin'"
          nav
          v-bind="props"
          :title="`${item.title}`"
        >
          <template v-slot:prepend>
            <v-icon :icon="item.icon" size="24"></v-icon>
          </template>
        </v-list-item>
      </template>
      <div class="bg-grey-darken-4" style="border-radius: 4px">
        <template v-for="(sub, i) in item.subs" :key="i">
          <v-list-item
            :title="sub.title"
            :value="sub.title"
            density="compact"
            style="padding-left: 12px !important"
            link
            :to="sub.path"
            v-if="returnAccess(sub, 'group')"
          >
            <template v-slot:title>
              <div style="font-size: 12px">
                {{ sub.title }}
              </div>
            </template>
            <template v-slot:prepend>
              <v-icon :icon="sub.icon" size="16"></v-icon>
            </template>
          </v-list-item>
        </template>
      </div>
    </v-list-group>
    <div v-else>
      <v-list-item
        v-if="returnAccess(item)"
        link
        :to="item.path"
        :title="item.title"
      >
        <!-- :prepend-icon="item.icon" -->
        <template v-slot:prepend>
          <v-icon :icon="item.icon" size="24"></v-icon>
        </template>
      </v-list-item>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

const props = defineProps({
  nav: {
    type: Object,
    default: {},
  },
});

const item = props.nav;
let groupNull = true;

function validateAccess(data) {
    let hasAccess = false;
    authStore.access.map((o) => { 
        if (data.slug == o.slug) {
            hasAccess = true; 
        }
    });

    return hasAccess;
}
 
const returnAccess = (item, type = null) => {
   
  let hasAccess = false;
  if (authStore.authRole == "superadmin") {
    hasAccess = true;
  } else if (
    item.slug == "dashboard" ||
    item.slug == "scan" ||
    item.slug == "report-incident" || 
    item.slug == "request-asset" ||
    item.slug == "transfer-asset"
  ) {
    hasAccess = true;
    
  } else if(validateAccess(item)){ 
    hasAccess = true;

    if (validateAccess(item) && type == "group") {
      groupNull = false;
    }
  }

  return hasAccess;
};
</script>