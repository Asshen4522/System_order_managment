<script setup>
import { reactive } from "vue";

const local_data = reactive({
    showPhone: false,
});
const props = defineProps({
    roleId: null,
});
const emit = defineEmits(["openPage"]);

function nextPage(index) {
    emit("openPage", index);
}

function logout() {
    fetch("/Logout", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            emit("openPage", 1);
        });
}
</script>
<template>
    <div>
        <div class="header">
            <div v-if="props.roleId == 1" class="phone">
                <img
                    @click="local_data.showPhone = !local_data.showPhone"
                    class="menu-pic"
                    src="../../img/menu.png"
                />
                <div
                    :class="{ notShow: !local_data.showPhone }"
                    class="phone-menu"
                >
                    <button @click="nextPage(3)">Заказы</button>
                    <button @click="nextPage(11)">Пользователи</button>
                    <button @click="logout">Выйти</button>
                </div>
            </div>

            <div v-if="props.roleId == 1" class="top-menu">
                <div class="menu-buttons">
                    <button @click="nextPage(3)">Заказы</button>
                    <button @click="nextPage(11)">Пользователи</button>
                </div>

                <button @click="logout">Выйти</button>
            </div>
            <div v-if="props.roleId == 2" class="logout">
                <button @click="logout">Выйти</button>
            </div>
        </div>
    </div>
</template>
<style scoped>
.header {
    width: 100%;
    margin-bottom: 20px;
    border-bottom-style: solid;
    padding-bottom: 20px;
}

.phone {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}
.phone-menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
    transition: 2s;
}
.menu-pic {
    width: 20px;
    border-style: solid;
    border-width: 1px;
    border-radius: 5px;
    padding: 5px;
    border-color: var(--color-accent);
}
.menu-pic:hover {
    cursor: pointer;
}
.notShow {
    display: none;
    transition: 2s;
}

.top-menu {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
.menu-buttons {
    display: flex;
    width: 50%;
    justify-content: space-between;
    gap: 10px;
}

.logout {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
}

@media only screen and (max-width: 425px) {
    .top-menu {
        display: none;
    }
}
@media only screen and (min-width: 425px) {
    .phone {
        display: none;
    }
}
</style>
