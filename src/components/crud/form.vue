<template>
    <div class="space-y-4">
        <slot name="fields" :refs="fieldRefs">
        </slot>
    </div>
    <slot name="actions" :save="save">
        <div class="space-y-4 mt-2">
            <button type="submit" @click="save"
                class="focus:outline-none text-white bg-blue-700  hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 mr-2">
                Save
            </button>
        </div>
    </slot>
    <slot name="svg">
        <svg class="w-10 h-10 mt-8 mb-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </slot>
</template>

<script setup lang="ts">
import { useSlots, onBeforeUpdate } from 'vue'
const slots = useSlots()
let fieldRefs = ref<Array<any>>([])

onBeforeUpdate(() => {
    fieldRefs.value = []
})

const emit = defineEmits(['save', 'deletep', 'closeDrawer', 'back'])

/** Technique to load the components from variable dynamically */
/** this function will return the imported component if the name of the component passed  */

let valid: boolean = false;
let data: any = {};
let save = () => {
    if (isValid()) {
        generateData();
        emit('save', data);
    }
    fieldRefs.value = [];
    data = {};
};
let back = () => {
    emit('back', data);
};

const isValid = () => {
    valid = true;
    fieldRefs.value.forEach((refr) => {
        if (refr.isValid) {
            if (!refr.isValid()) {
                valid = false;
            }
        }
    })
    return valid;
}

const generateData = () => {
    fieldRefs.value.forEach((refr) => {
        if (refr.props.name && refr.value) {
            data[refr.props.name] = refr.value;
        }
    })
}
</script>