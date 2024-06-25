<!-- ### Import this Component at App.vue Component ### -->
<template>
  <div v-if="notification.show" class="notification text-center">
    <span class="text-light py-1 px-3 rounded" :class="[notification.type === 'success' ? 'bg-success' : 'bg-danger']">
      {{ notification.message }}
    </span>
  </div>
</template>

<script setup>
import { computed } from "vue";
import store from "../store";

const notification = computed(() => store.state.notification);
</script>

<style scoped>
.notification {
  position: fixed;
  left: 30%;
  right: 30%;
  bottom: 15px;
}
</style>
