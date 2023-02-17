<script setup>
import { reactive } from "vue";
const local_data = reactive({
    users: [],
});
const props = defineProps({
    roleId: null,
});
const emit = defineEmits(["openPage", "displayUser"]);

function returnToCabinet(index) {
    emit("openPage", index);
}
function displayUser(index) {
    emit("displayUser", index);
}

function getUsers() {
    local_data.users = [];
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
                let send = {};
                send.fio = element.name + " " + element.surname;
                if (element.role_id == 2) {
                    send.role = "Исполнитель";
                } else {
                    send.role = "Менеджер";
                }
                send.banned = element.banned;
                send.id = element.id;
                local_data.users.push(send);
            });
        });
}

function deleteUser(userId) {
    let confirmation = confirm(
        "Вы уверены? Данная команда окончательно удалит этого пользователя."
    );
    if (confirmation) {
        fetch("/Delete_user", {
            method: "POST",
            body: JSON.stringify({ id: userId }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response) {
                    getUsers();
                }
            });
    }
}

function ban(index) {
    fetch("/Ban", {
        method: "POST",
        body: JSON.stringify({ id: index }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            getUsers();
        });
}
function unBan(index) {
    fetch("/UnBan", {
        method: "POST",
        body: JSON.stringify({ id: index }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            getUsers();
        });
}

getUsers();
</script>
<template>
    <div>
        <div class="list">
            <div v-for="user in local_data.users" class="user">
                <button @click="displayUser(user.id)" class="user-btn">
                    <div class="user-fio">{{ user.fio }}</div>
                    <div class="user-role">{{ user.role }}</div>
                </button>
                <img
                    @click="ban(user.id)"
                    v-show="user.banned == 0"
                    alt="Забанить"
                    class="close_pict"
                    src="../../img/cancelled.png"
                />
                <img
                    @click="unBan(user.id)"
                    v-show="user.banned == 1"
                    alt="Разбанить"
                    class="close_pict"
                    src="../../img/new.png"
                />
                <img
                    @click="deleteUser(user.id)"
                    class="close_pict"
                    src="../../img/delete.png"
                />
            </div>
        </div>

        <div class="buttons">
            <button @click="returnToCabinet(6)">Создать пользователя</button>
        </div>
    </div>
</template>
<style scoped>
.list {
    width: 100%;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    gap: 15px;
}

.user {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}
.user-btn {
    display: flex;
    background-color: white;
    justify-content: space-between;
    flex-direction: row;
    gap: 20px;
    width: 75%;
    border-color: var(--color-accent);
    color: var(--color-accent);
}

.user-btn:hover {
    background-color: var(--color-accent);
    color: white;
    transition: 1s;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.close_pict {
    height: 20px;
}

.close_pict:hover {
    cursor: pointer;
}
</style>
