<template>
    <div>
        <span v-if="editMode">
            <input type="text" v-model="editValue" @blur="(event) => { blurHandler(event) }">
        </span>

        <span v-else>
            <span v-if="props.item_type == 'Option'">
                <select v-model="editValue" @change="(event) => { blurHandler(event) }">
                    <option v-for="option in validations.options" :value=option.id>
                        {{ option.name }}
                    </option>
                </select>
            </span>
            <span v-else-if="props.item_type == 'Image'">
                <img :src="displayValue?.url" class="w-20">
            </span>
            <span v-else-if="props.item_type == 'RichText'" class="break-words">
                <div v-html="renderedContent"></div>
            </span>

            <span v-else-if="props.item_type == 'Reference' || props.item_type == 'MultiReference'">
                <div v-for="(collectionIdWiseData, collection_id, key) in referenceData" :key="key">
                    <select :multiple="props.item_type == 'MultiReference' ? true : false" v-model="editValue" @change="(event) => { blurHandler(event) }"
                        v-if="collection_id == validations.collectionId">
                        <option v-for="data in collectionIdWiseData" :value=data.id>
                            {{ data.name }}
                        </option>
                    </select>
                </div>
            </span>

            <!-- <span v-else-if="props.item_type == 'MultiReference'">
                <div v-for="(collectionIdWiseData, collection_id, key) in referenceData" :key="key">
                    <select multiple v-model="editValue" @change="(event) => { blurHandler(event) }"
                        v-if="collection_id == validations.collectionId">
                        <option v-for="data in collectionIdWiseData" :value=data.id>
                            {{ data.name }}
                        </option>
                    </select>
                </div>
            </span> -->

            <span v-else @click="editClickHandler">
                {{ displayValue }}
            </span>
        </span>

    </div>
</template>

<script setup lang="ts">

let editMode = ref<boolean>(false);

const emits = defineEmits(["editEvent"]);

const props = defineProps<{
    item_type?: string,
    column_key: string,
    item_value: any,
    item_id: string,
    validations?: any,
    referenceData?: any,
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