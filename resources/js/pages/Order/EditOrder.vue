<script setup>
import customInput from "../../components/Input.vue";
import { reactive } from "vue";

const props = defineProps({
    orderId: null,
});

const local_data = reactive({
    order: {
        id: props.orderId,

        city: "",
        locomotive: [],

        budget: null,
        daily_cost: null,
        housing: "",
        rent: null,
        payment: null,

        tangen: null,
        cup: null,

        contact: {
            id: null,
            fio: "",
            phone: null,
        },

        created_at: "2023-01-01",

        executor: null,
    },

    locomotives: [],
    newLocomotive: {
        id: null,
        amount: null,
        wheel_pairs: null,
    },
    errorLocomotive: false,
    errorWheelPairs: false,

    error_list: {
        errorCity: false,

        errorBudget: false,
        errorRent: false,
        errorDailyCost: false,
        errorPayment: false,

        errorTangen: false,
        errorCup: false,

        errorFio: false,
        errorPhone: false,

        errorDate: false,
    },

    contacts: [],

    executors: [],

    optionContact: true,
});

const emit = defineEmits(["openPage", "modal"]);

function returnToCabinet(index) {
    emit("openPage", index);
}

function getLocomotives() {
    fetch("/Get_locomotives", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.locomotives.push(element);
            });
        });
}
function getContacts() {
    fetch("/Get_contacts", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.contacts.push(element);
            });
        });
}
function getExecutors(params) {
    fetch("/Get_executors", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.executors.push(element);
            });
        });
}

function addLocomotive() {
    if (
        /^\d+$/.test(local_data.newLocomotive.count) &&
        /^\d+$/.test(local_data.newLocomotive.wheel_pairs) &&
        local_data.newLocomotive.id != null
    ) {
        local_data.order.locomotive.push({
            id: local_data.newLocomotive.id,
            name: local_data.locomotives[local_data.newLocomotive.id - 1].model,
            amount: local_data.newLocomotive.count,
            wheel_pairs: local_data.newLocomotive.wheel_pairs,
        });
        local_data.newLocomotive.id = null;
        local_data.newLocomotive.count = null;
        local_data.newLocomotive.wheel_pairs = null;
        local_data.errorLocomotive = false;
        local_data.errorWheelPairs = false;
    } else {
        local_data.errorLocomotive = true;
        local_data.errorWheelPairs = true;
    }
}
function deleteLocomotive(params) {
    local_data.order.locomotive.splice([params], 1);
}

function toggleOptionContact() {
    local_data.optionContact = !local_data.optionContact;
    local_data.order.contact.id = null;
    local_data.order.contact.fio = null;
    local_data.order.contact.phone = null;
}

function Validate() {
    local_data.error_list.errorCity = local_data.order.city == "";

    local_data.error_list.errorBudget =
        !/^\d+$/.test(local_data.order.budget) &&
        local_data.order.budget != null;
    local_data.error_list.errorRent =
        !/^\d+$/.test(local_data.order.rent) && local_data.order.rent != null;
    local_data.error_list.errorDailyCost =
        !/^\d+$/.test(local_data.order.daily_cost) &&
        local_data.order.daily_cost != null;
    local_data.error_list.errorPayment =
        !/^\d+$/.test(local_data.order.payment) &&
        local_data.order.payment != null;

    local_data.error_list.errorTangen =
        !/^\d+$/.test(local_data.order.tangen) &&
        local_data.order.tangen != null;
    local_data.error_list.errorCup =
        !/^\d+$/.test(local_data.order.cup) && local_data.order.cup != null;

    local_data.error_list.errorFio =
        !local_data.optionContact && local_data.order.contact.fio == "";
    local_data.error_list.errorPhone =
        !local_data.optionContact &&
        !/^(8|\+7)(\d){10}$/.test(local_data.order.contact.phone);

    if (
        !local_data.error_list.errorCity &&
        !local_data.error_list.errorBudget &&
        !local_data.error_list.errorRent &&
        !local_data.error_list.errorDailyCost &&
        !local_data.error_list.errorTangen &&
        !local_data.error_list.errorCup &&
        !local_data.error_list.errorFio &&
        !local_data.error_list.errorPhone &&
        !local_data.error_list.errorDate
    ) {
        return true;
    } else {
        return false;
    }
}
function contact() {
    if (!local_data.optionContact) {
        return fetch("/Create_contact", {
            method: "POST",
            body: JSON.stringify({
                fio: local_data.order.contact.fio,
                phone: local_data.order.contact.phone,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                return response;
            });
    } else {
        return Promise.resolve(local_data.order.contact.id);
    }
}
function SendData() {
    if (Validate()) {
        contact().then((response) => {
            const datuum = {
                id: props.orderId,

                city: local_data.order.city,
                locomotive: [],

                budget: local_data.order.budget,
                daily_cost: local_data.order.daily_cost,
                housing: local_data.order.housing,
                rent: local_data.order.rent,
                payment: local_data.order.payment,

                tangen: local_data.order.tangen,
                cup: local_data.order.cup,

                contact: response,

                created_at: local_data.order.created_at,

                executor: local_data.order.executor,
            };
            local_data.order.locomotive.forEach(element => {
                let obj ={
                    id: element.id,
                    name: element.name,
                    amount: element.amount,
                    wheel_pairs: element.wheel_pairs}
                datuum.locomotive.push(obj)

            });
            fetch("/Edit_order", {
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
                    if (response == true) {
                        returnToCabinet("order-main");
                    } else {
                    }
                });
        });
    } else {
        emit("modal", ["confirm", "Не все поля заполнены верно"]);
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
            local_data.order.city = response[0].city;
            

            local_data.order.budget = response[0].budget;
            local_data.order.daily_cost = response[0].daily_cost;
            local_data.order.housing = response[0].housing;
            local_data.order.rent = response[0].rent;
            local_data.order.payment = response[0].payment;

            local_data.order.tangen = response[0].tangen;
            local_data.order.cup = response[0].cup;

            local_data.order.contact.id = response[0].contact_id;

            local_data.order.created_at = response[0].created_at;

            local_data.order.executor = response[0].executor_id;
            response[3].forEach(element => {
                let obj = {};

                obj.amount = element.amount
                obj.id = element.id
                obj.name = element.model
                obj.wheel_pairs = element.wheel_pairs

                local_data.order.locomotive.push(obj)

            });
        })
}
getDisplayOrder();
getLocomotives();
getContacts();
getExecutors();
</script>
<template>
    <div class="page">
        <div>Редактирование заказа №{{ props.orderId }}</div>
        <div class="block">
            <div class="block_header">Место жительства</div>
            <customInput
                v-model="local_data.order.city"
                inputname="Город"
                typeIn="text"
                :ifError="local_data.error_list.errorCity"
            />
            <customInput
                v-model="local_data.order.housing"
                inputname="Адрес жилья"
                typeIn="text"
                :ifError="false"
            />
        </div>
        <div class="block"> 
            <div class="block_header">Список локомотивов</div>
            <div class="block_list_line">
                <div >Название</div>
                <div >Количество </div>
                <div > КП</div>
                <div ></div>
            </div>
            <div class="block_list_line" v-for="elem,index in local_data.order.locomotive">
                <div >{{ elem.name }}</div>
                <div >{{ elem.amount }} </div>
                <div > {{ elem.wheel_pairs }}</div>
                <div class="smallButtons" @click="deleteLocomotive(index)">&#215</div>
            </div>
            <div class="block_header">Добавить Локомотивы</div>
            <div class="block_add_line">
                <select class="block_add_line_elem"  v-model="local_data.newLocomotive.id">
                    <option
                        v-for="option in local_data.locomotives"
                        :value="option.id"
                    >
                        {{ option.model }}
                    </option>
                </select>

                <customInput class="block_add_line_elem" 
                    v-model="local_data.newLocomotive.count"
                    inputname="Количество локомотивов"
                    typeIn="text"
                    :ifError="local_data.errorLocomotive"
                />
                <customInput 
                    v-model="local_data.newLocomotive.wheel_pairs"
                    inputname="Колесные пары"
                    typeIn="text"
                    :ifError="local_data.errorWheelPairs"
                />
                <button class="block_add_line_elem"  @click="addLocomotive">Добавить</button>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Финансы</div>
            <customInput
                v-model="local_data.order.budget"
                inputname="Бюджет"
                typeIn="text"
                :ifError="local_data.error_list.errorBudget"
            />
            <customInput
                v-model="local_data.order.daily_cost"
                inputname="Суточные"
                typeIn="text"
                :ifError="local_data.error_list.errorDailyCost"
            />
            <customInput
                v-model="local_data.order.rent"
                inputname="Рента"
                typeIn="text"
                :ifError="local_data.error_list.errorRent"
            />
            <customInput
                v-model="local_data.order.payment"
                inputname="Оплата"
                typeIn="text"
                :ifError="local_data.error_list.errorPayment"
            />
        </div>
        <div class="block">
            <div class="block_header">Резцы</div>
            <customInput
                    v-model="local_data.order.tangen"
                    inputname="Тангец"
                    typeIn="text"
                    :ifError="local_data.error_list.errorTangen"
                />
                <customInput
                    v-model="local_data.order.cup"
                    inputname="Чашка"
                    typeIn="text"
                    :ifError="local_data.error_list.errorCup"
                />
        </div>
        <div class="block">
            <div class="block_header">Контактное лицо</div>
            <div>
                <div class="block_line">
                    <button @click="toggleOptionContact()">
                        Выбрать /Добавить
                    </button>
                </div>
                <div class="block_line">
                    <div v-show="local_data.optionContact == true" >
                        <select v-model="local_data.order.contact.id">
                            <option
                                v-for="option in local_data.contacts"
                                :value="option.id"
                            >
                                {{ option.phone + " " + option.fio }}
                            </option>
                        </select>
                    </div>
                    <div v-show="local_data.optionContact == false">
                        <div class="block_line">
                            <customInput
                                v-model="local_data.order.contact.phone"
                                inputname="Телефон"
                                typeIn="text"
                                :ifError="local_data.error_list.errorPhone"
                            />
                            <customInput
                                v-model="local_data.order.contact.fio"
                                inputname="Фио"
                                typeIn="text"
                                :ifError="local_data.error_list.errorFio"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="block_header">Исполнитель</div>
            <div class="block_line">
                <select v-model="local_data.order.executor">
                    <option
                        v-for="option in local_data.executors"
                        :value="option.id"
                    >
                        {{ option.name + " " + option.surname }}
                    </option>
                </select>
            </div>
        </div>
        <customInput
                v-model="local_data.order.created_at"
                inputname="Дата начала"
                typeIn="date"
                :ifError="local_data.error_list.errorDate"
            />
        
            

        <div class="buttons">
            <button @click="SendData">Сохранить изменения</button>
            <button @click="returnToCabinet('order-main')">Назад</button>
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
    grid-template-columns: 50% 20% 20% 10%;
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
}
.block_add_line_elem:nth-of-type(1) {height: 40.19px;}
.block_add_line_elem:nth-of-type(2) { 
    padding-left: 10px;
    padding-right: 10px;
    width:30%;}
.block_add_line_elem:nth-of-type(3){height: 39.19px;}
.block_line{
    display:flex;
    flex-direction:row;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-bottom: 15px;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
select{
    min-width: 170px;
}</style>
