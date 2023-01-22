<script setup>
import Authorization from "./pages/Authorization.vue";
import CreateOrder from "./pages/CreateOrder.vue";

import { reactive, ref } from "vue";

const local_data = reactive({
    currentPage: 2,
});
// This starter template is using Vue 3 <script setup> SFCs
// Check out https://vuejs.org/api/sfc-script-setup.html#script-setup
</script>

<template>
    <div class="zone">
        <div v-if="local_data.currentPage === 1">
            <Authorization />
        </div>
        <div v-if="local_data.currentPage === 2">
            <CreateOrder />
        </div>
    </div>
</template>

<style scoped></style>
