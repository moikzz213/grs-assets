<template>
  <v-container>
    <AppPageHeader title="Add Asset" />
    <v-row>
      <div class="v-col-12">
        <AssetForm />
      </div>
    </v-row>
  </v-container>
</template>

<script setup>
import AppPageHeader from "@/components/ApppageHeader.vue";
import AssetForm from "@/pages/assets/AssetForm.vue";
</script>
