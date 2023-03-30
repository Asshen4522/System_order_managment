<script setup>
import { reactive } from "vue";
import customInput from "../../components/Input.vue";


const emit = defineEmits(["openPage"]);
const props = defineProps({
    nowDate: null,
    orderId: null,
});
const local_data = reactive({
    report: {
        wheel_pairs:[],
        cutter:[{name: "Тангец", count:0, done:0},{name: "Чашка", count:0, done:0}],
        costs: [],
        activities: [],
        comment: null,
        date: props.nowDate,
        id:props.orderId,
    },

    newActivity: null,
    newCost: {
        id: null,
        price: null,
    },


    errorCost: false,


    activities: [],
    costs: [],
    inner_costs: [],

    createNewCost:"",
    errorNewCost : false,

    createNewActivity:"",
    errorNewActivity : false,
});

function updateStatus() {
    let statusId = 2;
    let statusReady = true;
    local_data.report.wheel_pairs.forEach(element => {
        if (element.wheel_pairs > element.done+element.doneNow) {
            statusReady = false
        }
    });
    if (statusReady) {
        statusId=3
    }
    const statusAndId = {id : props.orderId, status : statusId, date : props.nowDate}
    fetch("/Update_status", {
        method: "POST",
        body: JSON.stringify(statusAndId),
        headers: {
            "X-CSRF-TOKEN":
                document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response) {
                returnToOrder();
            }
        });
}

function returnToOrder() {
    emit("openPage", 'order-show');
}

function createCost() {
    local_data.errorNewCost = local_data.createNewCost == "";
    if (!local_data.errorNewCost) {
        fetch("/Create_cost", {
            method: "POST",
            body: JSON.stringify({
                name: local_data.createNewCost,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response == false) {
                    local_data.errorNewCost = true;
                } else {
                    local_data.costs.push({
                        id: response,
                        name: local_data.createNewCost,
                    });
                    local_data.createNewCost = "";
                }
            });
    }
}
function createActivity() {
    local_data.errorNewActivity = local_data.createNewActivity == "";
    if (!local_data.errorNewActivity) {
        fetch("/Create_activity", {
            method: "POST",
            body: JSON.stringify({
                name: local_data.createNewActivity,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response == false) {
                    local_data.errorNewActivity = true;
                } else {
                    local_data.activities.push({
                        id: response,
                        name: local_data.createNewActivity,
                    });
                    local_data.createNewActivity = "";
                }
            });
    }
}

function getActivities() {
    local_data.activities = [];
    fetch("/Get_activities", {
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
    fetch("/Get_costs", {
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
    fetch("/Get_costs_order", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response[0].forEach((element) => {
                local_data.inner_costs.push(element);
            });
            local_data.report.cutter[0].amount = response[1][0].tangen;
            local_data.report.cutter[1].amount = response[1][0].cup;
            if (response[2].length != 0) {
                response[2].forEach(element => {
                    local_data.report.cutter[0].done += element.tangen;
                    local_data.report.cutter[1].done += element.cup;
                });
            }
            
            
        });
}
function getWheelPair() {
    fetch("/Get_wheel_pair", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            local_data.report.wheel_pairs = response;
            local_data.report.wheel_pairs.forEach(element => {
                element.doneNow = 0
            });
        });
}

function valCost() {
    local_data.errorCost = !/^\d+$/.test(local_data.newCost.price);
    return !local_data.errorCost && local_data.newCost.id
}
function addCost() {
    if (valCost()) {
        local_data.report.costs.push({
        id: local_data.newCost.id,
        name: local_data.costs[local_data.newCost.id - 1].name,
        price: local_data.newCost.price,
    });
    local_data.newCost.id = null;
    local_data.newCost.price = null;
    }
}
function deleteCost(params) {
    local_data.report.costs.splice([params],1);
}

function addActivity() {
    if (local_data.newActivity) {
        local_data.report.activities.push({
        id: local_data.newActivity,
        name: local_data.activities[local_data.newActivity - 1].name,
    });
    local_data.newActivity = null;
    }
    
}
function deleteActivity(params) {
    local_data.report.activities.splice([params],1)
}

function lessPairs(index) {
    if (local_data.report.wheel_pairs[index].doneNow != 0) {
        local_data.report.wheel_pairs[index].doneNow-=0.5
    }
}
function morePairs(index) {
    if (local_data.report.wheel_pairs[index].doneNow != local_data.report.wheel_pairs[index].wheel_pairs-local_data.report.wheel_pairs[index].done) {
        local_data.report.wheel_pairs[index].doneNow += 0.5
    }
}

function lessCutter(index) {
    if (local_data.report.cutter[index].count != 0) {
        local_data.report.cutter[index].count-=1
    }
}
function moreCutter(index) {
    local_data.report.cutter[index].count+=1
}

function SendData() {
    let datuum = {
        wheel_pairs: [],
        tangen: local_data.report.cutter[0].count,
        cup: local_data.report.cutter[1].count,
        costs: [],
        activities: [],
        comment: local_data.report.comment,
        date: local_data.report.date,
        id: props.orderId,
    }
    local_data.report.wheel_pairs.forEach(element => {
        datuum.wheel_pairs.push({
            id: element.id,
            locomotive_id:element.locomotive_id,
            done: element.done,
            doneNow: element.doneNow,
            order_id: element.order_id,
        })
    });
    local_data.report.costs.forEach(element => {
        datuum.costs.push({
            id: element.id, 
            name: element.name, 
            price: element.price
        })
    });
    local_data.report.activities.forEach(element => {
        datuum.activities.push({
            id: element.id, 
            name: element.name
        })
    });
    fetch("/Create_report", {
        method: "POST",
        body: JSON.stringify(datuum),
        headers: {
            "X-CSRF-TOKEN":
                document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response) {
                updateStatus();
            }
        });
}


getCosts();
getActivities();
getOrderCosts();
getWheelPair();
</script>
<template>
    <div class="page">
        <customInput
                v-model="local_data.report.date"
                inputname="Дата отчета"
                typeIn="date"
                :ifError="false"
            />
        <!-- <div class="block_header">Дневной отчет от {{props.nowDate}}</div> -->
        <div class="block">
            <div class="block_header">Финансовые затраты</div>
            <div class="block_list_line">
                <div>Суточные</div>
                <div>{{ local_data.inner_costs[0]?.daily_cost }} р.</div>
            </div>
            <div class="block_list_line">
                <div>Квартира</div>
                <div>{{ local_data.inner_costs[0]?.rent }} р.</div>
            </div>

            <div class="block_list_line" v-for="elem,index in local_data.report.costs">
                <div>{{ elem.name }}</div>
                <div>{{ elem.price }} р.</div>
                <div class="smallButtons" @click="deleteCost(index)">&#215</div>
            </div>
            <div class="block_header">Добавить траты</div>
            <div class="block_add_line">
                <select v-model="local_data.newCost.id" class="block_add_line_elem">
                    <option
                        v-for="option in local_data.costs"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
                <customInput class="block_add_line_elem"
                    v-model="local_data.newCost.price"
                    inputname="Сумма"
                    typeIn="text"
                    :ifError="local_data.errorCost"
                />
                <button @click="addCost">Добавить</button>
            </div>
            <div class="block_header">Добавить вид трат</div>
            <div class="block_add_line">
                <customInput
                    v-model="local_data.createNewCost"
                    inputname="Затрата"
                    typeIn="text"
                    :ifError="local_data.errorNewCost"
                />
                <button @click="createCost">Добавить</button>
            </div>
        </div>
       
        <div class="block">
            <div class="block_header">Занятость</div>
            <div class="block_header">Обточка КП</div>
            <div class="block_list_train">
                <div>Модель</div>
                <div>Кол-во поездов</div>
                <div>Кол-во КП</div>
                <div>Обточено всего</div>
                <div>Обточено сейчас</div>
            </div>
            <div class="block_list_train" v-for="train,index in local_data.report.wheel_pairs">
                <div>{{train.model}}</div>
                <div>{{train.amount}}</div>
                <div>{{train.wheel_pairs}}</div>
                <div>{{train.done}}</div>
                <div class="block_list_count">
                    <div class="smallButtons" @click="lessPairs(index)">-</div>
                    <div>{{ train.doneNow }}</div>
                    <div class="smallButtons" @click="morePairs(index)">+</div>
                </div>
            </div>
            <div class="block_header">Расход резцов</div>
            <div class="block_list_cutter">
                <div>Название</div>
                <div>Выделено</div>
                <div>Израсходовано</div>
                <div>Используется</div>
            </div>
            <div class="block_list_cutter" v-for="cutter,index in local_data.report.cutter">
                <div>{{cutter.name}}</div>
                <div>{{cutter.amount}}</div>
                <div>{{cutter.done}}</div>
                <div class="block_list_count">
                    <div class="smallButtons" @click="lessCutter(index)">-</div>
                    <div>{{cutter.count}}</div>
                    <div class="smallButtons" @click="moreCutter(index)">+</div>
                </div>
            </div>
            <div class="block_header">Прочие занятости</div>
            <div class="block_list_line" v-for="elem,index in local_data.report.activities">
                <div>{{ elem.name }}</div>
                <div class="smallButtons" @click="deleteActivity(index)">&#215</div>
            </div>
            <div class="block_header">Добавить занятия</div>
            <div class="block_add_line">
                <select  v-model="local_data.newActivity">
                    <option
                        v-for="option in local_data.activities"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
                <button @click="addActivity" >Добавить</button>
            </div>
            <div class="block_header">Добавить вид занятий</div>
            <div class="block_add_line">
                <customInput
                    v-model="local_data.createNewActivity"
                    inputname="Активность"
                    typeIn="text"
                    :ifError="local_data.errorNewActivity"
                />
                <button @click="createActivity">Добавить</button>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Комментарий</div>
            <textarea v-model="local_data.report.comment"> </textarea>
        </div>
        <div class="buttons">
            <button @click="SendData">Создать отчет</button>
            <button @click="returnToOrder">Назад</button>
            
        
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
    display:flex;
    flex-direction:column;
    gap: 15px;

    border-style: solid;
    border-radius: 10px;
    border-color: var(--color-input);
    border-width:1px;

    padding-bottom: 20px;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 30px;
}
.block_header{
    margin-bottom: 15px;
}
.block_list_line{ 
    display:grid;
    grid-template-columns: 50% 40% 10%;
    align-items:center;
    margin-left: 10px;
}
.smallButtons{
    font-size:30px;
}
.smallButtons:hover{
    cursor: pointer;
}
.block_add_line{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap:10px;
}
.block_list_train{
    display:grid;
    grid-template-columns: 20% 20% 20% 10% 30% ;
    align-items:center;
    margin-left: 10px;
    place-items:center;
}
.block_list_cutter{
    display:grid;
    grid-template-columns: 20% 25% 25% 30% ;
    align-items:center;
    margin-left: 10px;
    justify-content:center;
    place-items:center;
}
.block_list_count{
    display:flex;
    flex-direction:row;
    justify-content:space-between;
    align-items:center;
    width:70%
}
.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
</style>
