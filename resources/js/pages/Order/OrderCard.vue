<script setup>
import NewImg from "../../../img/new.png";
import WorkImg from "../../../img/working.png";
import ReadyImg from "../../../img/ready.png";
import CancelledImg from "../../../img/cancelled.png";
import { computed } from "vue";

const props = defineProps({
    roleId: null,
    order: null,
});
const emit = defineEmits(["displayOrder", "displayReports", "deleteOrder"]);
const status = computed(() => {
    switch (props.order.status) {
        case 1:
            return NewImg;
            break;
        case 2:
            return WorkImg;
            break;
        case 3:
            return ReadyImg;
            break;

        case 4:
            return CancelledImg;
            break;
    }
});
</script>
<template>
    <div class="card">
        <div class="card-line">
            <img class="pict" :src="status" />
            <img
                @click="$emit('deleteOrder', props.order.id)"
                v-show="props.roleId == 1"
                class="pict"
                src="../../../img/delete.png"
            />
        </div>
        <div>{{ props.order.city }}</div>
        <div>{{ props.order.fio }}</div>

        <button @click="$emit('displayOrder', props.order.id)">
            Детали заказа
        </button>
        <button @click="$emit('displayReports', props.order.id)">Отчеты</button>
    </div>
</template>
<style scoped>
.card {
    padding: 10px;
    border-style: solid;
    border-width: 1px;
    border-radius: 10px;
    width: 100%-20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    background-color: white;
    border-color: var(--color-accent);
    color: var(--color-accent);
}
.card-line {
    width: 90%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.pict {
    height: 20px;
}
</style>
