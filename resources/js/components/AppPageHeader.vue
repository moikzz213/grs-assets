<template>
  <v-app-bar density="compact" color="background-darken-1" elevation="0">
    <div class="px-4 d-flex align-center">
      <div class="text-overline text-grey-lighten-1">{{ title }}</div>
    </div>
    <v-spacer></v-spacer>
  </v-app-bar>
</template>
<script setup>
const props = defineProps({
  title: {
    type: String,
    default: "Dashboard",
  },
});
</script>
