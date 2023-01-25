<script setup>
import { reactive } from "vue";

const local_data = reactive({
    orders: [],
});

const emit = defineEmits(["openPage"]);

function getOrders() {
    fetch("/Get_orders", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                const elem = {
                    status: element.status_id,
                    id: element.id,
                };
                local_data.orders.push(elem);
            });
        });
}

function nextPage(index) {
    emit("openPage", index);
}

getOrders();
</script>
<template>
    <div>
        <div class="buttons">
            <button @click="nextPage(2)">Добавить заказ</button>
            <button>Добавить пользователя</button>
        </div>
        <div class="field">
            <div v-for="option in local_data.orders">
                <div v-show="option.status === 1">
                    <button class="button-order">
                        Новый заказ #{{ option.id }}
                    </button>
                </div>
                <div v-show="option.status === 2">
                    <button class="button-order">
                        Работающий заказ #{{ option.id }}
                    </button>
                </div>
                <div v-show="option.status === 3">
                    <button class="button-order">
                        Выполненный заказ #{{ option.id }}
                    </button>
                </div>
                <div v-show="option.status === 4">
                    <button class="button-order">
                        Отмененный заказ #{{ option.id }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.field {
    margin-top: 30px;
}

.button-order {
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
