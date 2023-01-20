<script setup>
import Authorization from "./pages/Authorization.vue";

import { reactive, ref } from "vue";

const local_data = reactive({
    currentPage: 1,
});
// This starter template is using Vue 3 <script setup> SFCs
// Check out https://vuejs.org/api/sfc-script-setup.html#script-setup
</script>

<template>
    <div class="zone">
        <div v-if="local_data.currentPage === 1">
            <Authorization />
        </div>
    </div>
</template>

<style scoped></style>
