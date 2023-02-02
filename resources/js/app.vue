<script setup>
import Authorization from "./pages/Authorization.vue";
import CreateOrder from "./pages/CreateOrder.vue";
import Cabinet from "./pages/Cabinet.vue";
import DisplayOrder from "./pages/DisplayOrder.vue";
import CreateReport from "./pages/CreateReport.vue";

import { reactive } from "vue";

const local_data = reactive({
    currentPage: 1,
    pickOrder: null,
    roleId: null,
});

function changePage(index) {
    local_data.currentPage = index;
}

function writeReport(index) {
    local_data.pickOrder = index;
    local_data.currentPage = 5;
}

function showOrder(index) {
    local_data.currentPage = 4;
    local_data.pickOrder = index;
}

function authorizate(roleId) {
    local_data.currentPage = 3;
    local_data.roleId = roleId;
}
</script>

<template>
    <div class="zone">
        <div v-if="local_data.currentPage === 1">
            <Authorization @auth="authorizate" />
        </div>
        <div v-if="local_data.currentPage === 2">
            <CreateOrder @openPage="changePage" />
        </div>
        <div v-if="local_data.currentPage === 3">
            <Cabinet
                @openPage="changePage"
                @displayOrder="showOrder"
                :roleId="local_data.roleId"
            />
        </div>
        <div v-if="local_data.currentPage === 4">
            <DisplayOrder
                @createReport="writeReport"
                @openPage="changePage"
                :orderId="local_data.pickOrder"
                :roleId="local_data.roleId"
            />
        </div>
        <div v-if="local_data.currentPage === 5">
            <CreateReport
                :orderId="local_data.pickOrder"
                @openPage="changePage"
            />
        </div>
    </div>
</template>

<style scoped></style>
