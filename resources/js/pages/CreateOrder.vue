<script setup>
import customInput from "../components/Input.vue";
import { reactive, ref, computed } from "vue";

const local_data = reactive({
    order: {
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

        created_at: new Date().toISOString().substr(0, 10),

        executor: null,
    },

    error_list: {
        errorCity: false,
        errorWheelPairs: false,

        errorBudget: false,
        errorRent: false,
        errorDailyCost: false,

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

const emit = defineEmits(["openPage", "modal"]);

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
        local_data.order.locomotive == null &&
        (!/^\d+$/.test(local_data.order.wheel_pairs) ||
            local_data.order.wheel_pairs != null);

    local_data.error_list.errorBudget =
        !/^\d+$/.test(local_data.order.budget) &&
        local_data.order.budget != null;
    local_data.error_list.errorRent =
        !/^\d+$/.test(local_data.order.rent) && local_data.order.rent != null;
    local_data.error_list.errorDailyCost =
        !/^\d+$/.test(local_data.order.daily_cost) &&
        local_data.order.daily_cost != null;

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
        !local_data.error_list.errorWheelPairs &&
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
            fetch("/Create_order", {
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
                    }
                });
        });
    } else {
        emit("modal", [
            "confirm",
            "???? ?????? ???????? ?????????????????? ??????????. ???????????????????? ???????????? ?????? ?????????????? ?????????? ?? ?????? ????????????????????",
        ]);
    }
}

getLocomotives();
getContacts();
getExecutors();
</script>
<template>
    <div class="page">
        <div>????????????????</div>
        <div>
            <customInput
                v-model="local_data.order.city"
                inputname="??????????"
                typeIn="text"
                :ifError="local_data.error_list.errorCity"
            />
        </div>
        <div class="line">???????????? ????????????????????</div>
        <div class="line">
            <div class="locomotiveWheel">
                <select
                    class="locomotiveWheel__item"
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

                <customInput
                    v-model="local_data.order.wheel_pairs"
                    inputname="???????????????? ????????"
                    typeIn="text"
                    :ifError="local_data.error_list.errorWheelPairs"
                />
            </div>
        </div>

        <div class="line">
            <customInput
                v-model="local_data.order.budget"
                inputname="????????????"
                typeIn="text"
                :ifError="local_data.error_list.errorBudget"
            />
            <customInput
                v-model="local_data.order.daily_cost"
                inputname="????????????????"
                typeIn="text"
                :ifError="local_data.error_list.errorDailyCost"
            />
        </div>
        <div class="line">
            <customInput
                v-model="local_data.order.housing"
                inputname="?????????? ??????????"
                typeIn="text"
                :ifError="false"
            />
            <customInput
                v-model="local_data.order.rent"
                inputname="??????????"
                typeIn="text"
                :ifError="local_data.error_list.errorRent"
            />
        </div>

        <div>
            ??????????
            <div class="line">
                <customInput
                    v-model="local_data.order.tangen"
                    inputname="????????????"
                    typeIn="text"
                    :ifError="local_data.error_list.errorTangen"
                />
                <customInput
                    v-model="local_data.order.cup"
                    inputname="??????????"
                    typeIn="text"
                    :ifError="local_data.error_list.errorCup"
                />
            </div>
        </div>

        <div>
            ???????????????????? ????????
            <div>
                <div class="contact_buttons">
                    <button @click="toggleOptionContact()">
                        ?????????????? /????????????????
                    </button>
                </div>
                <div class="contact_input">
                    <div
                        class="contact_selector"
                        v-show="local_data.optionContact == true"
                    >
                        <select
                            class="contact_selector"
                            v-model="local_data.order.contact.id"
                        >
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
                                inputname="??????????????"
                                typeIn="text"
                                :ifError="local_data.error_list.errorPhone"
                            />
                            <customInput
                                v-model="local_data.order.contact.fio"
                                inputname="??????"
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
                inputname="???????? ????????????"
                typeIn="date"
                :ifError="local_data.error_list.errorDate"
            />
        </div>

        <div>
            ??????????????????????
            <div class="select_executor">
                <select
                    class="select_executor"
                    v-model="local_data.order.executor"
                >
                    <option
                        v-for="option in local_data.executors"
                        :value="option.id"
                    >
                        {{ option.name + " " + option.surname }}
                    </option>
                </select>
            </div>
        </div>
        <div class="buttons">
            <button @click="SendData">??????????????</button>
            <button @click="returnToCabinet(3)">??????????</button>
        </div>
    </div>
</template>
<style scoped>
.locomotiveWheel {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 20px;
}

.locomotiveWheel__item {
    display: flex;
    flex-direction: row;
    align-self: center;
    width: 170px;
}
.page {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.line {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin-top: 10px;
}

.contact_buttons {
    display: flex;
    flex-direction: row;
    margin-top: 10px;
    justify-content: center;
}

.contact_selector {
    display: flex;
    flex-direction: row;
    justify-content: center;
    min-width: 170px;
}

.contact_input {
    margin-top: 30px;
}
.select_executor {
    margin-top: 10px;
    min-width: 170px;
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

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
</style>
