<template>
    <div>

        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ label }}</label>
        <select v-model="value" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 
                    block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                    dark:focus:ring-primary-500 dark:focus:border-primary-500">

            <option v-if="!hidePleaseSelect && placeholder" value="">{{ placeholder }}</option>
            <option v-else-if="!hidePleaseSelect" value="" :disabled="disableOption"> Please Select </option>
            <option v-for="opt in options" :value="opt.value" :disabled="disableOption">{{ opt.label }}</option>

        </select>
        <span class="error text-red-400 text-xs" v-show="!valid">{{ errorMessage }}</span>
    </div>
</template>

<script setup lang="ts">
import { watch } from 'vue';
import type { selectOption } from '../interface/crud-essentials';
import type { fieldChange } from '@/components/crud/interface/crud-essentials'

const emit = defineEmits<{
    (e: 'change', change: fieldChange): void
}>()

const props = defineProps<{
    name: string,
    label: string,
    required?: Boolean,
    placeholder?: string,
    is_valid?: Function,
    error_message?: String | undefined,
    type?: string,
    value?: any,
    options: Array<selectOption>,
    hidePleaseSelect?: Boolean | false
    disableOption?: any
}>();

const isValid = () => {
    let v = true;
    v = props.required && value.value == '' ? false : true;
    v = props.is_valid ? props.is_valid(value.value) : v;
    valid.value = v;
    return valid.value;
}
let errorMessage: String | undefined = props.error_message ? props.error_message : "Please Select an option";
let valid = ref<boolean>(true);
let value = ref<any>(props.value ? props.value : '');
defineExpose({
    isValid,
    props,
    value
});

watch(value, (n, o) => {
    isValid(),
        emit('change', {
            new: n,
            old: o
        })
});

watch(() => props.error_message, (n, o) => {
    errorMessage = n;
});

</script>