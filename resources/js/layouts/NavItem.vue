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
            v-if="returnAccess(sub.slug, 'group')"
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
        v-if="returnAccess(item.slug)"
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
const returnAccess = (slug, type = null) => {
  let hasAccess = false;
  if (authStore.authRole == "superadmin") {
    hasAccess = true;
  } else if (slug == "dashboard") {
    hasAccess = true;
  }
  if (!hasAccess) {
    authStore.access?.map((o, i) => {
      if (slug == o.slug) {
        hasAccess = true;
        if (type == "group") {
          groupNull = false;
        }
      }
    });
  }

  return hasAccess;
};
</script>
