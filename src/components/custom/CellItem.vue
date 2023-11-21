<template>
    <div>
        <span v-if="editMode">
            <input type="text" v-model="editValue" @blur="(event) => { blurHandler(event) }">
        </span>

        <span v-else>
            <span v-if="props.item_type == 'Option'">
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 
                    block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                    dark:focus:ring-primary-500 dark:focus:border-primary-500" v-model="editValue"
                    @change="(event) => { blurHandler(event) }">
                    <option v-for="option in validations.options" :value=option.id>
                        {{ option.name }}
                    </option>
                </select>
            </span>
            <span v-else-if="props.item_type == 'Image'">
                <img :src="displayValue?.url" class="w-20 cursor-pointer" @click="modalVisible = !modalVisible">
                <span>
                    <Modal :isVisible="modalVisible" @close="modalVisible = false" :class="modalClass">
                        <template v-slot:header>
                            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Item Image</h3>
                        </template>
                        <template v-slot:modal_content>
                             {{ aspectRatios }}
                            <img :src="displayValue?.url" class="w-full">
                            <SelectDropdown name="asad" label="asad" :options="aspectRatios">

                            </SelectDropdown>
                        </template>
                    </Modal>
                </span>
            </span>
            <span v-else-if="props.item_type == 'RichText'" class="break-words">
                <div v-html="renderedContent" class="breakContent"></div>
            </span>

            <span v-else-if="props.item_type == 'Reference' || props.item_type == 'MultiReference'">
                <div v-for="(collectionIdWiseData, collection_id, key) in referenceData" :key="key">
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        :multiple="props.item_type == 'MultiReference' ? true : false" v-model="editValue"
                        @change="(event) => { blurHandler(event) }" v-if="collection_id == validations.collectionId">
                        <option v-for="data in collectionIdWiseData" :value=data.id>
                            {{ data.name }}
                        </option>
                    </select>
                </div>
            </span>

            <span v-else @click="editClickHandler">
                <div class="breakContent"> {{ displayValue }} </div>
            </span>
        </span>

    </div>
</template>

<script setup lang="ts">
import { Modal } from "@/components/functional";
import { SelectDropdown } from '@/components/crud'
let editMode = ref<boolean>(false);

let modalClass = 'w-[750px]';

const emits = defineEmits(["editEvent"]);

const modalVisible = ref<boolean>(false)

const props = defineProps<{
    item_type?: string,
    column_key: string,
    item_value: any,
    item_id: string,
    validations?: any,
    referenceData?: any,
    aspectRatios?: Array<any>,
}>();

let editValue = ref<string>(props.item_value);
let displayValue = ref<any>(props.item_value);


const blurHandler = (event: any) => {
    editMode.value = false;
    displayValue.value = editValue.value;
    emits('editEvent', {
        item_id: props.item_id,
        old_value: props.item_value,
        new_value: editValue.value,
        column_key: props.column_key
    });
}

const editClickHandler = () => {
    let editableTypes = ['PlainText', 'Option'];
    if (props.item_type != undefined && editableTypes.includes(props.item_type)) {
        editMode.value = true;
    }
}

// Create a computed property to render the content without HTML tags
const renderedContent = computed(() => {
    const tempElement = document.createElement('div');
    tempElement.innerHTML = displayValue.value;
    tempElement.classList.add('break-all');
    return tempElement.textContent || tempElement.innerText;
});

</script>

<style scoped>
.breakContent {
    width: 350px;
    /* Set your desired width */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
            