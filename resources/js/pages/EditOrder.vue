<script setup>
import customInput from "../components/Input.vue";
import { reactive, ref, computed } from "vue";

const props = defineProps({
    orderId: null,
});

const local_data = reactive({
    order: {
        id: props.orderId,

        city: "",
        locomotive: null,
        wheel_pairs: null,

        budget: null,
        daily_cost: null,
        housing: "",
        rent: null,

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

    error_list: {
        errorCity: false,
        errorWheelPairs: false,

        errorBudget: false,
        errorRent: false,
        errorDailyCost: false,

        errorHousing: false,

        errorTangen: false,
        errorCup: false,

        errorFio: false,
        errorPhone: false,

        errorDate: false,
    },
    createError: false,

    locomotives: [],

    contacts: [],

    executors: [],

    optionContact: true,
});

const emit = defineEmits(["openPage"]);

function selectLocomotive(selector) {
    local_data.order.wheel_pairs =
        local_data.locomotives[selector.target.value - 1].wheel_pairs;
}

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

function toggleOptionContact() {
    local_data.optionContact = !local_data.optionContact;
    local_data.order.contact.id = null;
    local_data.order.contact.fio = null;
    local_data.order.contact.phone = null;
}

function Validate() {
    local_data.error_list.errorCity = local_data.order.city == "";
    local_data.error_list.errorWheelPairs =
        local_data.order.locomotive == null ||
        !/^\d+$/.test(local_data.order.wheel_pairs);

    local_data.error_list.errorBudget = !/^\d+$/.test(local_data.order.budget);
    local_data.error_list.errorRent = !/^\d+$/.test(local_data.order.rent);
    local_data.error_list.errorDailyCost = !/^\d+$/.test(
        local_data.order.daily_cost
    );

    local_data.error_list.errorHousing = local_data.order.housing == "";

    local_data.error_list.errorTangen = !/^\d+$/.test(local_data.order.tangen);
    local_data.error_list.errorCup = !/^\d+$/.test(local_data.order.cup);

    local_data.error_list.errorFio =
        local_data.order.contact.id == null &&
        local_data.order.contact.fio == "";
    local_data.error_list.errorPhone =
        local_data.order.contact.id == null &&
        !/^(8|\+7)(\d){10}$/.test(local_data.order.contact.phone);

    if (
        !local_data.error_list.errorCity &&
        !local_data.error_list.errorWheelPairs &&
        !local_data.error_list.errorBudget &&
        !local_data.error_list.errorRent &&
        !local_data.error_list.errorDailyCost &&
        !local_data.error_list.errorHousing &&
        !local_data.error_list.errorTangen &&
        !local_data.error_list.errorCup &&
        !local_data.error_list.errorFio &&
        !local_data.error_list.errorPhone &&
        !local_data.error_list.errorDate &&
        local_data.order.executor != null
    ) {
        return true;
    } else {
        return false;
    }
}
function contact() {
    if (local_data.order.contact.id == null) {
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
                locomotive: local_data.order.locomotive,
                wheel_pairs: local_data.order.wheel_pairs,

                budget: local_data.order.budget,
                daily_cost: local_data.order.daily_cost,
                housing: local_data.order.housing,
                rent: local_data.order.rent,

                tangen: local_data.order.tangen,
                cup: local_data.order.cup,

                contact: response,

                created_at: local_data.order.created_at,

                executor: local_data.order.executor,
            };
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
                        returnToCabinet(3);
                    } else {
                    }
                });
        });
    } else {
        local_data.createError = true;
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
            local_data.order.locomotive = response[0].locomotive_id;
            local_data.order.wheel_pairs = response[0].wheel_pairs;

            local_data.order.budget = response[0].budget;
            local_data.order.daily_cost = response[0].daily_cost;
            local_data.order.housing = response[0].housing;
            local_data.order.rent = response[0].rent;

            local_data.order.tangen = response[0].tangen;
            local_data.order.cup = response[0].cup;

            local_data.order.contact.id = response[0].contact_id;

            local_data.order.created_at = response[0].created_at;

            local_data.order.executor = response[0].executor_id;
        });
}
getDisplayOrder();
getLocomotives();
getContacts();
getExecutors();
</script>
<template>
    <div class="page">
        <div>Редактирование заказа №{{ props.orderId }}</div>
        <div>
            <customInput
                v-model="local_data.order.city"
                inputname="Город"
                typeIn="text"
                :ifError="local_data.error_list.errorCity"
            />
        </div>

        <div class="line">
            <div>
                <div>Модель локомотива</div>
                <select
                    @change="selectLocomotive($event)"
                    v-model="local_data.order.locomotive"
                >
                    <option
                        v-for="option in local_data.locomotives"
                        :value="option.id"
                    >
                        {{ option.model }}
                    </option>
                </select>
            </div>
            <div>
                <customInput
                    v-model="local_data.order.wheel_pairs"
                    inputname="Кол-во колесных пар"
                    typeIn="text"
                    :ifError="local_data.error_list.errorWheelPairs"
                />
            </div>
        </div>

        <div class="line">
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
        </div>
        <div class="line">
            <customInput
                v-model="local_data.order.housing"
                inputname="Адрес жилья"
                typeIn="text"
                :ifError="local_data.error_list.errorHousing"
            />
            <customInput
                v-model="local_data.order.rent"
                inputname="Рента"
                typeIn="text"
                :ifError="local_data.error_list.errorRent"
            />
        </div>

        <div>
            Резцы
            <div class="line">
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
        </div>

        <div>
            Контактное лицо
            <div>
                <div class="contact_buttons">
                    <button @click="toggleOptionContact()">
                        Выбрать /Добавить
                    </button>
                </div>
                <div class="contact_input">
                    <div
                        class="contact_selector"
                        v-show="local_data.optionContact == true"
                    >
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
                        <div class="line">
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
        </div>

        <div>
            <customInput
                v-model="local_data.order.created_at"
                inputname="Дата начала"
                typeIn="date"
                :ifError="local_data.error_list.errorDate"
            />
        </div>

        <div>
            Исполнитель
            <select v-model="local_data.order.executor">
                <option
                    v-for="option in local_data.executors"
                    :value="option.id"
                >
                    {{ option.name + " " + option.surname }}
                </option>
            </select>
        </div>
        <div class="buttons">
            <button @click="SendData">Сохранить изменения</button>
            <button @click="returnToCabinet(3)">Назад</button>
        </div>

        <div class="error" v-show="local_data.createError">
            Не все поля заполнены верно
        </div>
    </div>
</template>
<style scoped>
.buttons {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
.page {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.line {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin-top: 20px;
}

.contact_buttons {
    display: flex;
    flex-direction: row;
    gap: 50px;
    justify-content: space-around;
}

.contact_input {
    margin-top: 30px;
}

.button-selector {
    color: var(--color-object);
    background-color: white;
    border-color: var(--color-object);
    padding: 2px;
    width: 100%;
}
.button-selector:hover {
    color: white;
}
.active {
    color: white;
    background-color: var(--color-object);
}

.error {
    margin-top: 20px;
    text-align: center;
    font-size: 20px;
    color: red;
}
</style>
