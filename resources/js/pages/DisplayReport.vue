<script setup>
import { reactive } from "vue";

const props = defineProps({
    reportId: null,
});
const local_data = reactive({
    report: {
        date: null,
        wheel_pairs: 0,
        tangen: 0,
        cup: 0,
        costs: [],
        activities: [],
        comment: null,
        id: props.orderId,
    },

    activities: [],
    costs: [],
    inner_costs: [],
});

const emit = defineEmits(["openPage"]);

function returnToCabinet() {
    emit("openPage", 9);
}

function getActivities() {
    local_data.activities = [];
    return fetch("/Get_activities", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.activities.push(element);
            });
        });
}
function getCosts() {
    local_data.costs = [];
    return fetch("/Get_costs", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.costs.push(element);
            });
        });
}
function getOrderCosts() {
    fetch("/Get_inner_costs_report", {
        method: "POST",
        body: JSON.stringify({ id: props.reportId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.inner_costs.push(element);
            });
        });
}
function getReport() {
    fetch("/Get_display_report", {
        method: "POST",
        body: JSON.stringify({ id: props.reportId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            (local_data.report.date = response[0].date),
                (local_data.report.id = response[0].id),
                (local_data.report.wheel_pairs = response[0].wheel_pair),
                (local_data.report.tangen = response[0].tangen),
                (local_data.report.cup = response[0].cup),
                (local_data.report.comment = response[0].comment),
                response[1].forEach((element) => {
                    local_data.report.activities.push({
                        id: element.activity_id,
                        name: local_data.activities[element.activity_id - 1]
                            .name,
                    });
                });

            response[2].forEach((element) => {
                local_data.report.costs.push({
                    id: element.cost_id,
                    price: element.price,
                    name: local_data.costs[element.cost_id - 1].name,
                });
            });
        });
}

getOrderCosts();
Promise.all([getCosts(), getActivities()]).then(getReport);
</script>
<template>
    <div>
        <div>Дневной отчет от {{ local_data.report.date }}</div>
        <div class="block">
            <div class="block_header">Финансовые затраты</div>
            <div class="display_line">
                <div class="display_line_first">Суточные</div>
                <div class="display_line_second">
                    {{ local_data.inner_costs[0]?.daily_cost }} р.
                </div>
            </div>
            <div class="display_line">
                <div class="display_line_first">Квартира</div>
                <div class="display_line_second">
                    {{ local_data.inner_costs[0]?.rent }} р.
                </div>
            </div>

            <div
                class="display_line"
                v-for="(elem, index) in local_data.report.costs"
            >
                <div class="display_line_first">{{ elem.name }}</div>
                <div class="display_line_second">{{ elem.price }} р.</div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Занятость</div>
            <div class="display_line">
                <div class="display_line_first">Обточка КП</div>
                <div class="wheelPairs">
                    <div>{{ local_data.report.wheel_pairs }}</div>
                </div>
            </div>
            <div class="display_line">
                <div class="display_line_first">
                    Израсходовано чашечных резцов
                </div>
                <div class="wheelPairs">
                    <div>{{ local_data.report.cup }}</div>
                </div>
            </div>
            <div class="display_line">
                <div class="display_line_first">
                    Израсходовано тангецных резцов
                </div>
                <div class="wheelPairs">
                    <div>{{ local_data.report.tangen }}</div>
                </div>
            </div>

            <div
                class="display_line"
                v-for="(elem, index) in local_data.report.activities"
            >
                <div class="display_line_first">{{ elem.name }}</div>
            </div>
        </div>

        <div class="block">
            <div class="block_header">Комментарий</div>
            <div class="comment">{{ local_data.report.comment }}</div>
        </div>
        <div class="buttons">
            <button @click="returnToCabinet">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.smallButtons {
    font-size: 30px;
    align-self: right;
}

.smallButtons:hover {
    cursor: pointer;
}

.block {
    border-style: solid;
    border-radius: 10px;
    margin-bottom: 20px;
    margin-top: 20px;
    border-width: 1px;
    border-color: var(--color-input);
}
.block_header {
    margin-bottom: 15px;
    margin-top: 5px;
    margin-left: 10px;
}

.comment {
    margin-left: 10px;
    margin-bottom: 10px;
    width: 75%;
    height: 80px;
    resize: none;
}

.display_line {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-left: 10px;
    margin-bottom: 10px;
}
.display_line_act {
    margin-top: 30px;
    margin-left: 10px;
    margin-bottom: 15px;
}
.display_line_first {
    width: 50%;
}
.display_line_second {
    width: 30%;
}

.addCost {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.addCostId {
    width: 50%;
    margin: 5px;
    height: 75%;
    align-self: center;
}
.addCostButton {
    margin: 10px;
}

.wheelPairs {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    width: 25%;
    align-items: center;
}

.buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
</style>
