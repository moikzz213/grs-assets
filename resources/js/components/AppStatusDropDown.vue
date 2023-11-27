<template>
  <v-menu>
    <template v-slot:activator="{ props }">
      <v-btn :loading="loadingBtn" block :color="selectedObject.color" v-bind="props">
        {{ selectedObject.title }}
      </v-btn>
    </template>
    <v-list>
      <v-list-item
        v-for="(item, index) in dropDownList"
        :key="index"
        :value="index"
        @click="() => updateSelected(item)"
      >
        <v-list-item-title class="text-capitalize">
          <v-icon :icon="mdiCircleMedium" :color="item.color"></v-icon>
          {{ item.title }}</v-list-item-title
        >
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { mdiCircleMedium } from "@mdi/js";
const emit = defineEmits(["update"]);
const props = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  list: {
    type: Array,
    default: [],
  },
  currentState: {
    type: Object,
    default: null,
  },
});
const dropDownList = ref([]);
const currentState = ref("");

dropDownList.value = props.list;
watch(
  () => props.list,
  (newVal) => {
    dropDownList.value = [...dropDownList.value, ...newVal.list];
  }
);
currentState.value = props.currentState.status;
watch(
  () => props.currentState,
  (newVal) => {
    currentState.value = newVal;
    console.log("props.currentState", currentState.value);
  }
);
const loadingBtn = ref(false);
watch(
  () => props.loading,
  (newVal) => {
    loadingBtn.value = newVal;
  }
);
const selectedObject = computed(() => {
  return dropDownList.value.filter((d) => d.title == currentState.value)[0];
});
const updateSelected = (item) => {
  emit("update", {
    state: selectedObject.value.title,
    model_id: props.currentState.id,
  });
};
</script>
