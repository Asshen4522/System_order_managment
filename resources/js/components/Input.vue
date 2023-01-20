<script setup>
const props = defineProps({
    modelValue: "",
    inputname: "",
    ifError: false,
    typeIn: "",
});
const emit = defineEmits(["update:modelValue"]);
</script>
<template>
    <div class="component">
        <input
            :class="{ errorStyle: ifError }"
            :type="typeIn"
            :value="props.modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            required
        />
        <label :class="{ errorStyle: ifError }">{{ props.inputname }}</label>
    </div>
</template>

<style scoped>
.component {
    width: 100%;
}
.errorStyle {
    border-color: red;
    color: red;
}
input {
    background: none;
    color: var(--color-accent);
    font-size: 18px;
    padding: 10px 0 10px 5px;
    display: block;
    width: 100%;
    border: none;
    border-radius: 0;
    border-bottom: 1px solid var(--color-input);
}

label {
    color: var(--color-input);
    font-size: 16px;
    font-weight: normal;
    position: relative;
    pointer-events: none;
    left: 5px;
    top: -25px;
    transition: 1s ease all;
}

input:focus {
    outline: none;
    color: var(--color-accent);
}
input:focus ~ label,
:valid ~ label {
    top: -60px;
    font-size: 12px;
    color: var(--color-accent);
}
</style>
