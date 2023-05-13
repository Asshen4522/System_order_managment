<script setup>
import { computed, reactive } from "vue";

const props = defineProps({
    nowDate: null,
    orderId: null,
    roleId: null,
});
const local_data = reactive({
    order: {},
    reportDates: [],
    locomotives: [],
    editOrCreate: "create",
});
const emit = defineEmits([
    "openPage",
    "editOrder",
    "createReport",
    "editReport",
    "modal",
]);

const statusName = computed(() => {
    switch (local_data.order.status) {
        case 1:
            return "Новый";
            break;
        case 2:
            return "В работе";
            break;
        case 3:
            return "Выполнен";
            break;
        case 4:
            return "Отменен";
            break;
    }
});

function returnToCabinet() {
    emit("openPage", "order-main");
}
function editOrder() {
    emit("editOrder", props.orderId);
}
function editReport() {
    emit("editReport", props.orderId);
}

function writeReport(index) {
    if (local_data.order.startAt <= props.nowDate) {
        emit("createReport", props.orderId);
    } else {
        emit("modal", ["confirm", "Заказ еще не начат"]);
    }
}

function updateStatus() {
    let oldStatus = local_data.order.status;
    if (local_data.order.startAt > props.nowDate) {
        local_data.order.status = 1;
    } else {
        let statusReady = true;
        local_data.order.wheel_pairs.forEach((element) => {
            if (element.wheel_pairs != element.done) {
                statusReady = false;
            }
        });
        if (statusReady) {
            local_data.order.status = 3;
        } else {
            local_data.order.status = 2;
        }
    }
    if (oldStatus != local_data.order.status) {
        const statusAndId = {
            id: props.orderId,
            status: local_data.order.status,
            date: props.nowDate,
        };
        fetch("/Update_status", {
            method: "POST",
            body: JSON.stringify(statusAndId),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                emit("modal", ["confirm", "Статус заказа исправлен"]);
            });
    } else {
        emit("modal", ["confirm", "Статус заказа верен"]);
    }
}

function getDisplayOrder() {
    fetch("/Get_display_order", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            local_data.order.status = response[0].status_id;

            local_data.order.startAt = response[0].created_at;
            local_data.order.order_created_at = response[0].order_created_at;

            local_data.order.id = response[0].id;
            local_data.order.name = response[0].name;

            local_data.order.firm = response[0].firm;
            local_data.order.city = response[0].city;
            local_data.order.housing = response[0].housing;

            local_data.order.fio = response[0].fio;
            local_data.order.phone = response[0].phone;

            local_data.order.budget = 0 + response[0].budget;
            local_data.order.payment = 0 + response[0].payment;

            local_data.order.tangen = 0 + response[0].tangen;
            local_data.order.cup = 0 + response[0].cup;
            local_data.order.tangenLeft = response[0].tangen;
            local_data.order.cupLeft = response[0].cup;
            response[1].forEach((element) => {
                local_data.order.tangenLeft -= element.tangen;
                local_data.order.cupLeft -= element.cup;
            });

            local_data.order.moneyLeft = response[0].budget;
            local_data.order.moneyLeft -=
                (response[0].daily_cost + response[0].rent) *
                response[1].length;
            response[2].forEach((element) => {
                local_data.order.moneyLeft -= element.price;
            });

            local_data.order.wheel_pairs = response[3];
        });
}
function getReportDates() {
    fetch("/Get_order_report_dates", {
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
                local_data.reportDates.push(element.date);
                if (local_data.reportDates.includes(props.nowDate)) {
                    local_data.editOrCreate = "edit";
                } else {
                    local_data.editOrCreate = "create";
                }
            });
        });
}
getDisplayOrder();
getReportDates();
</script>
<template>
    <div class="page">
        <div class="block_line">
            <div>Заказ № {{ local_data.order.name }}</div>
            <div class="block_line">
                <div>Статус: {{ statusName }}</div>
                <button @click="updateStatus">Проверить статус</button>
            </div>
        </div>

        <div class="block">
            <div class="block_header">Даты</div>
            <div class="block_line">
                <div>Заказ создан</div>
                <div class="block_line_text">
                    {{ local_data.order.order_created_at }}
                </div>
            </div>
            <div class="block_line">
                <div>Заказ начинается</div>
                <div class="block_line_text">
                    {{ local_data.order.startAt }}
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block_line">
                <div>Фирма</div>
                <div class="block_line_text">{{ local_data.order.firm }}</div>
            </div>
            <div class="block_line">
                <div>Город</div>
                <div class="block_line_text">{{ local_data.order.city }}</div>
            </div>
            <div class="block_line">
                <div>Жилье</div>
                <div class="block_line_text">
                    {{ local_data.order.housing }}
                </div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Контактное лицо</div>
            <div class="block_line">
                <div>ФИО</div>
                <div class="block_line_text">{{ local_data.order.fio }}</div>
            </div>
            <div class="block_line">
                <div>Телефон</div>
                <div class="block_line_text">{{ local_data.order.phone }}</div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Финансы</div>
            <div class="block_line">
                <div>Оплата</div>
                <div class="block_line_text">
                    {{ local_data.order.payment }} р.
                </div>
            </div>
            <div class="block_list_line">
                <div></div>
                <div>Выделено</div>
                <div>Осталось</div>
            </div>
            <div class="block_list_line">
                <div>Бюджет</div>
                <div>{{ local_data.order.budget }} р.</div>
                <div>{{ local_data.order.moneyLeft }} р.</div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Резцы</div>
            <div class="block_list_line">
                <div></div>
                <div>Выделено</div>
                <div>Осталось</div>
            </div>
            <div class="block_list_line">
                <div>Тангец</div>
                <div>{{ local_data.order.tangen }}</div>
                <div>{{ local_data.order.tangenLeft }}</div>
            </div>
            <div class="block_list_line">
                <div>Чашка</div>
                <div>{{ local_data.order.cup }}</div>
                <div>{{ local_data.order.cupLeft }}</div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Колесные пары</div>
            <div class="block_wheels_line">
                <div>Поезд</div>
                <div>Кол-во поездов</div>
                <div>Кол-во КП</div>
                <div>Сделано КП</div>
            </div>
            <div
                class="block_wheels_line"
                v-for="elem in local_data.order.wheel_pairs"
            >
                <div>{{ elem.model }}</div>
                <div>{{ elem.amount }}</div>
                <div>{{ elem.wheel_pairs }}</div>
                <div>{{ elem.done }}</div>
            </div>
        </div>
        <div class="buttons">
            <button @click="editOrder" v-show="props.roleId == 1">
                Редактировать
            </button>
            <button
                @click="writeReport"
                v-show="
                    props.roleId == 2 && local_data.editOrCreate == 'create'
                "
            >
                Создать отчет
            </button>
            <button
                @click="editReport"
                v-show="props.roleId == 2 && local_data.editOrCreate == 'edit'"
            >
                Редактировать отчет
            </button>
            <button @click="returnToCabinet">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.page {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.block {
    display: flex;
    flex-direction: column;
    gap: 15px;

    border-style: solid;
    border-radius: 10px;
    border-color: var(--color-input);
    border-width: 1px;

    padding-bottom: 20px;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 30px;
}
.block_header {
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: bold;
}
.block_line {
    align-items: center;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 15px;
}
.block_line_text {
    border-bottom-style: solid;
    border-color: var(--color-input);
    border-width: 1px;
}
.block_list_line {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    align-items: center;
    margin-left: 10px;
}
.block_wheels_line {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    align-items: center;
    margin-left: 10px;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
</style>
