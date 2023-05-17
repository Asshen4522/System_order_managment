<script setup>
import { reactive, computed } from "vue";

const props = defineProps({
    nowDate: null,
});
const local_data = reactive({
    menuActive: 1,
    reqYear: props.nowDate.slice(0, 4),
    years: [],
    month: [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь",
    ],
    orders: [],
    reports: [],
    orderLocomotives: [],
    reportWheels: [],
});
const emit = defineEmits(["openPage"]);

const tableTwo = computed(() => {
    let table = [[""]];
    table[0].push(`Итого`);
    for (let index = 0; index < local_data.month.length; index++) {
        table[0].push(local_data.month[index]);
    }
    let i = 1;
    local_data.years.forEach((element) => {
        table.push([]);
        table[i].push(element);
        for (let index = 0; index < local_data.month.length; index++) {
            table[i].push([0, 0]);
        }
        table[i].push([0, 0]);

        local_data.orderLocomotives.forEach((order) => {
            if (element === order.created_at.slice(0, 4)) {
                if (order.done == order.wheel_pairs) {
                    table[i][Number(order.created_at.slice(5, 7)) + 1][0] +=
                        Number(order.amount);
                    table[i][1][0] += Number(order.amount);
                }
            }
        });
        local_data.reportWheels.forEach((report) => {
            if (element === report.date.slice(0, 4)) {
                table[i][Number(report.date.slice(5, 7)) + 1][1] += Number(
                    report.amount
                );
                table[i][1][1] += Number(report.amount);
            }
        });

        i += 1;
    });
    for (let i = 1; i < table.length; i++) {
        for (let k = 1; k < table[i].length; k++) {
            table[i][k] = String(table[i][k][0]);
            //+ "/" + String(table[i][k][1]);
            //На случай, если потребуется вернуть количество обточенных пар
        }
    }
    table = table[0].map((col, i) => table.map((row) => row[i]));
    return table;
});

const tableOne = computed(() => {
    let table = [["Итого", [0, 0], [0, 0], [0, 0]]];
    for (let index = 0; index < local_data.month.length; index++) {
        table.push([local_data.month[index], [0, 0], [0, 0], [0, 0]]);
    }

    local_data.orderLocomotives.forEach((element) => {
        if (local_data.reqYear === element.created_at.slice(0, 4)) {
            table[Number(element.created_at.slice(5, 7))][2][0] += Number(
                element.wheel_pairs
            );
            table[0][2][0] += Number(element.wheel_pairs);

            table[Number(element.created_at.slice(5, 7))][1][0] += Number(
                element.amount
            );
            table[0][1][0] += Number(element.amount);

            if (element.done == element.wheel_pairs) {
                table[Number(element.created_at.slice(5, 7))][1][1] += Number(
                    element.amount
                );
                table[0][1][1] += Number(element.amount);
            }
        }
    });
    local_data.reportWheels.forEach((element) => {
        if (local_data.reqYear === element.date.slice(0, 4)) {
            table[Number(element.date.slice(5, 7))][2][1] += Number(
                element.amount
            );
            table[0][2][1] += Number(element.amount);
            table[0][3][0] += Number(element.amount);
            table[Number(element.date.slice(5, 7))][3][0] += Number(
                element.amount
            );
        }
    });
    local_data.reports.forEach((element) => {
        if (local_data.reqYear === element.date.slice(0, 4)) {
            table[Number(element.date.slice(5, 7))][3][1] += 1;
            table[0][3][1] += 1;
        }
    });

    table.forEach((element) => {
        element[3] = element[3][0] / element[3][1];
        if (isNaN(element[3])) {
            element[3] = 0;
        }
    });

    return table;
});

function getData() {
    fetch("/Get_stats", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response[0].forEach((element) => {
                local_data.orders.push(element);
            });
            response[1].forEach((element) => {
                local_data.reports.push(element);
            });
            response[2].forEach((element) => {
                local_data.orderLocomotives.push(element);
            });
            response[3].forEach((element) => {
                local_data.reportWheels.push(element);
            });

            let a = new Set();
            a.add(props.nowDate.slice(0, 4));
            local_data.orders.forEach((element) => {
                a.add(element.created_at.slice(0, 4));
            });
            local_data.years = Array.from(a);
        });
}

getData();
</script>
<template>
    <div>
        <div class="top-menu">
            <div
                :class="{ menu_active: local_data.menuActive == 1 }"
                class="menu-elem"
                @click="local_data.menuActive = 1"
            >
                Локомотивы
            </div>
            <div
                :class="{ menu_active: local_data.menuActive == 2 }"
                class="menu-elem"
                @click="local_data.menuActive = 2"
            >
                Детализация
            </div>
        </div>
        <table v-if="local_data.menuActive == 1" class="table">
            <tbody class="table">
                <tr class="table_line_one" v-for="(line, i) in tableTwo">
                    <th v-for="(line_elem, j) in line">
                        <b v-if="i === 1 && j === 0">{{ line_elem }}</b>
                        <template v-else>
                            {{ line_elem }}
                        </template>
                    </th>
                </tr>
            </tbody>
        </table>
        <table v-if="local_data.menuActive == 2" class="table">
            <select class="year_picker" v-model="local_data.reqYear">
                <option v-for="option in local_data.years" :value="option">
                    {{ option }}
                </option>
            </select>
            <tbody class="table">
                <tr class="table_line_two">
                    <th class="text_block">Период</th>
                    <th class="text_block">
                        <div>Локомотивы</div>
                        <div>(План/Факт)</div>
                    </th>
                    <th class="text_block">
                        <div>Обточенные КП</div>
                        <div>(План/Факт)</div>
                    </th>
                    <th class="text_block">
                        <div>Ср. время</div>
                        <div>обточки КП</div>
                    </th>
                </tr>
                <tr class="table_line_two" v-for="line in tableOne">
                    <th>{{ line[0] }}</th>
                    <th>{{ line[1][0] }}/{{ line[1][1] }}</th>
                    <th>{{ line[2][0] }}/{{ line[2][1] }}</th>
                    <th>{{ line[3] }}</th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style scoped>
th {
    border-style: solid;
    border-width: 1px;
    border-color: var(--color-input);
    padding-top: 5px;
    padding-bottom: 5px;
    font-weight: normal;
}
.table {
    width: 100%;
}
.table_line_two {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
}
.table_line_one {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
}
.text_block {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.top-menu {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}
.menu-elem {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    transition: 1s;
}

.menu-elem:hover {
    cursor: pointer;
    text-decoration: underline;
    transition: 1s;
    font-size: 20px;
}
.menu_active {
    color: var(--color-accent);
}
.year_picker {
    margin-bottom: 10px;
}
</style>
