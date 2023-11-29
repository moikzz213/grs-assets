<template>
  <div>
    <v-navigation-drawer
      :rail="temporary == true ? false : rail"
      v-model="drawer"
      permanent
      class="pt-4 no-print"
      color="black"
      :expand-on-hover="false"
    >
      <div class="d-flex flex-column" style="height: 100%">
        <!-- :prepend-avatar="logo" -->
        <v-list-item nav :title="appName" class="mb-3">
          <template v-slot:prepend>
            <v-avatar size="40">
              <v-img :src="logo"></v-img>
            </v-avatar>
            <!-- <v-icon :icon="sub.icon" class="mx-auto"></v-icon> -->
          </template>
        </v-list-item>
        <v-divider></v-divider>
        <div class="overflow-y-auto gag-scroll" style="height: calc(100% - 120px)">
          <v-list nav>
            <nav-item v-for="item in commonNav" :key="item.title" :nav="item"></nav-item>
            <nav-item
              v-for="item in moderatorNav"
              :key="item.title"
              :nav="item"
            ></nav-item>
            <v-divider></v-divider>
          </v-list>
        </div>
        <v-divider></v-divider>
        <v-list nav class="mt-auto">
          <v-list-item
            :prepend-icon="rail == false ? mdiChevronLeft : mdiChevronRight"
            title="Collapse"
            @click="rail = !rail"
          ></v-list-item>
        </v-list>
      </div>
    </v-navigation-drawer>
    <v-app-bar class="no-print" density="compact" color="white" elevation="0">
      <template v-slot:prepend>
        <div class="d-flex align-center">
          <v-app-bar-nav-icon
            v-if="mobile == true"
            @click="drawer = !drawer"
            size="small"
          ></v-app-bar-nav-icon>
          <div class="ml-1 text-body-1 text-pirmary">
            {{ appName }}
          </div>
        </div>
      </template>
      <v-spacer></v-spacer>
      <v-btn
        size="36"
        class="mr-2"
        color="grey-darken-1"
        @click="toggleTheme"
        :icon="`${
          theme.global.name.value == 'light' ? mdiWeatherNight : mdiWhiteBalanceSunny
        }`"
      >
      </v-btn>
      <v-btn size="36" class="mr-2" color="grey-darken-1" :icon="mdiBellOutline"> </v-btn>
      <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
        <template v-slot:activator="{ props }">
          <v-avatar
            :color="`${
              theme.global.name.value == 'light' ? 'grey-lighten-4' : 'grey-darken-4'
            }`"
            :size="36"
            class="mr-3"
            v-bind="props"
            style="cursor: pointer"
          >
            {{ printInitials(authStore.user.username) }}
          </v-avatar>
        </template>
        <v-card min-width="300" class="rounded-lg mt-1">
          <div class="d-flex align-center pa-3">
            <v-avatar
              color="grey-lighten-3"
              :size="36"
              class="d-flex align-center justify-center mr-3"
              style="cursor: pointer"
            >
              <div>
                {{ printInitials(authStore.user.username) }}
              </div>
            </v-avatar>
            <div>
              <div class="text-body-1">
                {{ authStore.user.username }}
              </div>
              <div class="text-caption">
                {{ authStore.user.email }}
              </div>
            </div>
          </div>
          <v-divider></v-divider>
          <v-list nav density="compact" class="d-flex flex-column">
            <v-list-item
              :prepend-icon="mdiAccount"
              title="Account Settings"
              @click="() => openPage('/account')"
            ></v-list-item>
          </v-list>
          <v-divider></v-divider>
          <div class="pa-3">
            <v-btn :loading="loadingLogout" @click="logout" width="100%" color="primary">
              Logout
            </v-btn>
          </div>
        </v-card>
      </v-menu>
    </v-app-bar>
    <v-main>
      <slot />
    </v-main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useDisplay } from "vuetify";
import NavItem from "./NavItem.vue";
import {
  mdiTools,
  mdiChevronLeft,
  mdiChevronRight,
  mdiHomeOutline,
  mdiBellOutline,
  mdiAccount,
  mdiCog,
  mdiAccountGroup,
  mdiWhiteBalanceSunny,
  mdiOfficeBuildingCogOutline,
  mdiMapMarkerRadius,
  mdiShapePlus,
  mdiBookshelf,
  mdiStoreSettings, 
  mdiBellRing,
  mdiAnimation,
  mdiWeatherNight,
  mdiBarcodeScan,
  mdiTruckDelivery,
  mdiTimetable,
  mdiApplicationExport, 
  mdiWrenchClock,
  mdiArchiveArrowUpOutline,
  mdiArchiveStarOutline,
  mdiArchiveOutline
} from "@mdi/js";
import { useAuthStore } from "@/stores/auth";
import { printInitials } from "@/composables/printInitials";
import { useRouter } from "vue-router";
import { useTheme } from "vuetify";
import { authApi } from "@/services/axiosToken";
const theme = useTheme();
const appName = ref(import.meta.env.VITE_APP_NAME);
const logo = ref(import.meta.env.VITE_APP_URL + "/assets/images/fav.png");

// toggle dark mode
const toggleTheme = () => {
  theme.global.name.value = theme.global.current.value.dark ? "light" : "dark"; // toggle
  localStorage.setItem("gag_dark_theme", theme.global.name.value);
};
// navigatio
const authStore = useAuthStore();

const router = useRouter();
const commonNav = ref([
  {
    title: "Dashboard",
    icon: mdiHomeOutline,
    path: "/dashboard",
    slug: "dashboard",
  },
  {
    title: "Scan Barcode",
    icon: mdiBarcodeScan,
    path: "/scan",
    slug: "scan",
  },
  {
    title: "Report Incident",
    icon: mdiTools,
    path: "/report-incident",
    slug: "report-incident",
  },
  {
    title: "Request Asset",
    icon: mdiApplicationExport,
    path: "/request-asset",
    slug: "request-asset",
  },
  {
    title: "Transfer Asset",
    icon: mdiTruckDelivery,
    path: "/transfer-asset",
    slug: "transfer-asset",
  },
  {
    title: "Warranties",
    icon: mdiTimetable,
    path: "/warranties",
    slug: "warranties",
  },
  {
    title: "Maintenance",
    icon: mdiWrenchClock,
    path: "/maintenance",
    slug: "maintenance",
  },
  {
    title: "Assets",
    icon: mdiArchiveOutline,
    subs: [
      {
        title: "Asset List",
        icon: mdiArchiveStarOutline,
        path: "/asset-list",
        slug: "asset-list",
      }, 
      {
        title: "Import Asset",
        icon: mdiArchiveArrowUpOutline,
        path: "/asset-list/import",
        slug: "asset-import",
      },
    ],
  },
]);
const moderatorNav = ref([
  {
    title: "Administrator",
    icon: mdiCog,
    subs: [
      {
        title: "Companies",
        icon: mdiOfficeBuildingCogOutline,
        path: "/companies",
        slug: "companies",
      },
      {
        title: "Locations",
        icon: mdiMapMarkerRadius,
        path: "/locations",
        slug: "locations",
      },
      {
        title: "Categories",
        icon: mdiShapePlus,
        path: "/categories",
        slug: "categories",
      },
      // {
      //   title: "Brands",
      //   icon: mdiWatermark,
      //   path: "/brands",
      //   slug: "brands",
      // },
      // {
      //   title: "Models",
      //   icon: mdiGlobeModel,
      //   path: "/models",
      //   slug: "models",
      // },
      {
        title: "Vendors",
        icon: mdiStoreSettings,
        path: "/vendors",
        slug: "vendors",
      },
      {
        title: "Approval Setup",
        icon: mdiCog,
        path: "/approval-setup/request-asset",
        slug: "approval-setup",
      },
      {
        title: "Pages",
        icon: mdiBookshelf,
        path: "/pages",
        slug: "pages",
      },
      {
        title: "Notifications",
        icon: mdiBellRing,
        path: "/notifications",
        slug: "notifications",
      },
      {
        title: "Others",
        icon: mdiAnimation,
        path: "/others",
        slug: "others",
      },
      {
        title: "Users",
        icon: mdiAccountGroup,
        path: "/users",
        slug: "users",
      },
    ],
  },
]);
const openPage = (openPath) => {
  menu.value = false;
  router
    .push({
      path: openPath,
    })
    .catch((err) => {});
};

// app orientation
const { mobile } = useDisplay();
const menu = ref(false);
const rail = ref(true);
const drawer = ref(true);
const temporary = ref(false);
watch(mobile, async (newMobileValue, oldMobileValue) => {
  if (newMobileValue == true) {
    drawer.value = false;
    temporary.value = true;
  } else {
    drawer.value = true;
    temporary.value = false;
  }
});
onMounted(() => {
  if (mobile.value == true) {
    drawer.value = false;
    temporary.value = true;
  } else {
    drawer.value = true;
    temporary.value = false;
  }
});

// logout
const loadingLogout = ref(false);

// auth logout to sanctum
const authlogout = async () => {
  let data = {
    username: authStore.user.username,
  };

  const response = await authApi.post("/api/sanctumlogout", data);
  return response;
};
const logout = () => {
  loadingLogout.value = true;
  authlogout()
    .then(() => {
      authStore.logout().then(() => {
        loadingLogout.value = false;
        localStorage.removeItem("authUser");
        window.location = "/login";
      });
    })
    .catch((err) => {
      console.log("errrrrr", err);
      loadingLogout.value = false;
    });
};
</script>
