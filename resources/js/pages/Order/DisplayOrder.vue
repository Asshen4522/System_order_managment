<script setup>
import { reactive } from "vue";

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
        emit("createReport", index);
    } else {
        emit("modal", ["confirm", "Заказ еще не начат"]);
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
            local_data.order.startAt = response[0].created_at;
            local_data.order.id = props.orderId;

            local_data.order.firm = response[0].firm;
            local_data.order.city = response[0].city;
            local_data.order.housing = response[0].housing;

            local_data.order.fio = response[0].fio;
            local_data.order.phone = response[0].phone;

            local_data.order.budget = response[0].budget;
            local_data.order.payment = response[0].payment;

            local_data.order.tangen = response[0].tangen;
            local_data.order.cup = response[0].cup;

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
        <div>Заказ № {{ local_data.order.id }}</div>
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
                @click="writeReport(local_data.order.id)"
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
}
.block_line {
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
