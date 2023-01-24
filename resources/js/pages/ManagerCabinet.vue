<script setup>
import { reactive } from "vue";

const emit = defineEmits(["openPage"]);

function nextPage(index) {
    emit("openPage", index);
}
</script>
<template>
    <div>
        <button @click="nextPage(2)">Добавить заказ</button>
        <button>Добавить пользователя</button>
    </div>
    <div></div>
</template>
<style scoped></style>
