<script setup>
import { reactive } from "vue";

const props = defineProps({
    orderId: null,
});
const local_data = reactive({
    reports: [],
});

const emit = defineEmits(["openPage", "displayReport"]);

function returnToCabinet() {
    emit("openPage", "order-main");
}

function showReport(index) {
    emit("displayReport", index);
}

function getReports() {
    fetch("/Get_reportList", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.reports.push(element);
            });
        });
}

getReports();
</script>
<template>
    <div class="page">
        <div v-for="option in local_data.reports">
            <div class="order_buttons">
                <button @click="showReport(option.id)" class="button-order">
                    Отчет от {{ option.date }}
                </button>
            </div>
        </div>
        <div class="buttons">
            <button @click="returnToCabinet">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.page {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
.order_buttons {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 20px;
}
.button-order {
    display: flex;
    flex-direction: row;
    align-items: center;
    background-color: white;
    border-color: var(--color-accent);
    color: var(--color-accent);
}

.button-order:hover {
    background-color: var(--color-accent);
    color: white;
    transition: 1s;
}
</style>
