<script setup lang="ts">
import { computed } from "vue";

const props = defineProps({
  text: { type: String, default: "" },
  color: { type: String, default: "#FB8C00" },
  disable: { type: Boolean, default: false },
});

const setColor = computed(() => {
  switch (props.color) {
    case "red":
      return "#FF0000";
    case "blue":
      return "#0000FF";
    default:
      return "#FB8C00";
  }
});
</script>

<template>
  <v-btn :color="setColor" :disabled="props.disable">
    <span>{{ props.text }}</span>
  </v-btn>
</template>
