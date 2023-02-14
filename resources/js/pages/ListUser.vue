<script setup>
import { reactive } from "vue";
const local_data = reactive({
    users: [],
});
const props = defineProps({
    roleId: null,
});
const emit = defineEmits(["openPage", "displayOrder"]);

function getUsers() {
    fetch("/Get_userList", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.users.push(element);
            });
        });
}
getUsers();
</script>
<template>
    <div>
        <div></div>
        <div class="buttons"></div>
    </div>
</template>
<style scoped></style>
