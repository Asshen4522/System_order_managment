<script setup>
import { reactive } from "vue";

const props = defineProps({
    userId: null,
});

const local_data = reactive({
    user: {},
});

const emit = defineEmits(["openPage"]);

function returnToCabinet(index) {
    emit("openPage", index);
}

function getUser(params) {
    fetch("/Get_user", {
        method: "POST",
        body: JSON.stringify({ id: props.userId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            local_data.user.fio = response.name + " " + response.surname;
            local_data.user.role = response.roleName;
            local_data.user.phone = response.phone;
            local_data.user.id = response.id;
        });
}

getUser();
</script>
<template>
    <div>
        <div class="block">
            <div class="block_header">{{ local_data.user.fio }}</div>
            <div class="block_line">Телефон: "{{ local_data.user.phone }}"</div>
            <div class="block_line">Роль: "{{ local_data.user.role }}"</div>
        </div>
        <div class="buttons">
            <button @click="returnToCabinet(13)">Редактировать</button>
            <button @click="returnToCabinet(11)">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.block_line {
    margin-left: 10px;
    margin-top: 10px;
    margin-bottom: 10px;
}
.block {
    border-style: solid;
    border-radius: 10px;
    margin-bottom: 20px;
}
.block_header {
    margin-bottom: 15px;
    margin-top: 5px;
    margin-left: 10px;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
</style>
