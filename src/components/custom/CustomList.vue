<template>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <span class="text-gray-600 mx-2" v-if="checkItems">Selected Items : {{ checkedItems.length }}</span>
                    <button type="button" class="absolute top-2 right-0 bg-blue-500 text-white px-4 py-2 mb-2 rounded-md"
                        @click="confirmDelete" title="Click to reload collection">Load latest version</button>

                    <Modal :isVisible="isDeleteConfirmationVisible" @close="cancelDelete">
                        <template v-slot:header>
                            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Are you sure you want to
                                remove cache?</h3>
                        </template>
                        <template v-slot:modal_content>
                            <span v-if="cacheRemoveSuccess" class="text-xl font-medium text-green-600 dark:text-green mb-1"> {{ cacheRemoveSuccess }}</span>

                            <div class="mt-2 flex justify-between">
                                <button
                                    class="focus:outline-none text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 float-right"
                                    @click="cancelDelete">Cancel
                                </button>

                                <button
                                    class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                    @click="removeCache">
                                    Yes, Delete
                                </button>

                            </div>

                        </template>
                    </Modal>

                    <br />

                    <div id="userMessage">

                    </div>
                    <table :class="className ? className : 'min-w-full'"
                        class="w-full text-sm text-left text-gray-500 dark:text-gray-800">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3" v-if="checkItems">
                                    <div class="flex items-center">
                                        <input id="checkbox-all" aria-describedby="checkbox-1" type="checkbox"
                                            class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                            @change="allCheck($event)">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>

                                <th v-for="column in columns" scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        <slot :name="`header(${column.key})`">
                                            {{ column.label }}
                                        </slot>
                                        <button @click="sortItems(column.key, 'ASC')"
                                            v-if="column.item_type == 'PlainText' || column.item_type == 'Color' || column.item_type == 'Number'">
                                            <svg class="w-3 h-3 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                                            </svg>
                                        </button>

                                        <button @click="sortItems(column.key, 'DESC')"
                                            v-if="column.item_type == 'PlainText' || column.item_type == 'Color' || column.item_type == 'Number'">
                                            <svg class="w-3 h-3 ml-0 dark:text-white dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M5 1v12m0 0 4-4m-4 4L1 9" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Search Start Here -->
                                    <div v-if="column.item_type == 'PlainText' || column.item_type == 'Color'">
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 
                dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 
                dark:focus:border-primary-500" placeholder="search..."
                                            @keyup="(event) => { handleFilterChange({ 'item_type': column.item_type, 'column_key': column.key }, event) }" />
                                    </div>

                                    <div v-else-if="column.item_type == 'Option'">
                                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 
                    block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                    dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                            @change="(event) => { handleFilterChange({ 'item_type': column.item_type, 'column_key': column.key }, event) }">
                                            <option value="">select option</option>
                                            <option v-for="option in column.validations.options" :value=option.id>{{
                                                option.name }}</option>
                                        </select>
                                    </div>

                                    <div v-else-if="column.item_type == 'RichText'">
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 
                dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 
                dark:focus:border-primary-500" placeholder="search..."
                                            @keyup="(event) => { handleFilterChange({ 'item_type': column.item_type, 'column_key': column.key }, event) }" />
                                    </div>
                                    <!-- Search Ends Here -->
                                </th>


                                <th v-if="columns.length > 0 && showDates">Created On
                                    <button @click="sortItems('createdOn', 'ASC')">
                                        <svg class="w-3 h-3 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                                        </svg>
                                    </button>

                                    <button @click="sortItems('createdOn', 'DESC')">
                                        <svg class="w-3 h-3 ml-0 dark:text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 1v12m0 0 4-4m-4 4L1 9" />
                                        </svg>
                                    </button>
                                </th>
                                <th v-if="columns.length > 0 && showDates">Updated On
                                    <button @click="sortItems('lastUpdated', 'ASC')">
                                        <svg class="w-3 h-3 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4" />
                                        </svg>
                                    </button>

                                    <button @click="sortItems('lastUpdated', 'DESC')">
                                        <svg class="w-3 h-3 ml-0 dark:text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 1v12m0 0 4-4m-4 4L1 9" />
                                        </svg>
                                    </button>

                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

                            <template v-if="items.length > 0" :columns="columns" v-for="item, index in items" :item="item"
                                :index="index" :key="item.slug">
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" v-if="filterRecord(item)">
                                    <td class="w-4 p-4" v-if="checkItems">
                                        <div class="flex items-center">
                                            <input aria-describedby="checkbox-1" type="checkbox" name="checkbox[]"
                                                class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                                :value="item[ckColumn]" v-model="checkedItems">
                                        </div>
                                    </td>

                                    <td v-for="column in columns" class="break-words"
                                        :class="column.class ? column.class : ' p-4 text-sm font-normal text-gray-500 dark:text-gray-400'">
                                        <slot :name="`cell(${column.key})`" :item="item" :index="index">
                                            <CellItem :column_key="column.key" :item_value="item[column.key]"
                                                :item_id="item['item_id']" @editEvent="editHandler"
                                                :item_type="column.item_type" :validations="column.validations"
                                                :referenceData="referenceData" :aspectRatios="aspectRatios"
                                                :collectionID="collectionID">
                                            </CellItem>
                                        </slot>
                                    </td>

                                    <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"
                                        v-if="columns.length > 0 && showDates">{{
                                            moment(item.createdOn).format('MMMM Do YYYY, h:mm:ss a') }}</td>

                                    <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"
                                        v-if="columns.length > 0 && showDates">{{
                                            moment(item.lastUpdated).format('MMMM Do YYYY, h:mm:ss a') }}</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td :colspan="columns.length + 2" class="text-center">
                                        <slot v-if="loading" name="loading">
                                            Loading ...
                                        </slot>
                                        <slot v-else name="no_records">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">No
                                                Records</span>
                                        </slot>
                                    </td>

                                </tr>
                            </template>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import ajax from "@/accessories/ajax";
import CellItem from "@/components/custom/CellItem.vue"
import moment from 'moment';
import { Modal } from "../functional";
const emits = defineEmits(["action", "editEvent", "sortItemsEvent"]);
let checkedItems: any = ref<Array<any>>([]);
const props = defineProps<{
    items: Array<any>,
    loading?: boolean,
    columns: any,
    checkItems?: boolean,
    checkColumn?: string,
    className?: string,
    editData?: any,
    referenceData?: any,
    showDates?: boolean,
    aspectRatios?: Array<any>,
    collectionID?: any
}>();

let ckColumn = props.checkColumn ? props.checkColumn : "id";
let cacheRemoveSuccess = ref('');
let filters = ref<any>({});

let isDeleteConfirmationVisible = ref(false);

const handleFilterChange = (filterItem: any, event: any) => {
    filterItem.value = event.target.value;
    filters.value[filterItem.column_key] = filterItem;
};

const filterRecord = (item: any) => {
    let response = true;
    let enabledSearch = ['PlainText', 'Color', 'RichText'];
    for (let i in filters.value) {
        let fl = filters.value[i];

        if (enabledSearch.includes(fl.item_type)) {
            console.log("item value original string--", item[fl.column_key]);
            console.log("fal value string to be matched--", fl.value.toLowerCase());
            if ('undefined' != typeof (item[fl.column_key]) && !item[fl.column_key].toLowerCase().includes(fl.value.trim().toLowerCase())) {
                response = false;
            }

        } else if (fl.item_type == 'Option') {
            console.log("Option", item[fl.column_key]);
            console.log("fal value string to be matched", fl.value);
            if (fl.value != item[fl.column_key]) {
                response = false;
            }

        }
    }
    return response;

};



async function editCheckedData() {
    if (props.editData && props.editData.contacts) {
        props.editData.contacts.forEach((val: any, index: any) => {
            checkedItems.value[index] = val[ckColumn]
        });
    }
}
editCheckedData();

const sortItems = (key: string, sortOrder: string) => {
    emits('sortItemsEvent', { 'key': key, "sortOrder": sortOrder });
}



const allCheck = (event: any) => {
    if (event.target.checked) {
        props.items.forEach((val: any, index: any) => {
            checkedItems.value[index] = val.id
        });
    } else {
        checkedItems.value = []
    }
}

const editHandler = (data: any) => {
    emits('editEvent', data);
}

const confirmDelete = () => {
    isDeleteConfirmationVisible.value = true;
}

const cancelDelete = () => {
    isDeleteConfirmationVisible.value = !isDeleteConfirmationVisible.value;
}

async function removeCache() {
    let aj = new (ajax as any)();
    let result = await aj.get("/removeCache.php");
    if (result.data.code == 200) {
        cacheRemoveSuccess.value = 'Cached Removed Successfully';
        setTimeout(function () {
            cacheRemoveSuccess.value = '';
            window.location.href = window.location.href;
        }, 2000);
    }
}

defineExpose({
    checkedItems
});
</script>

<style scoped>
.delete-confirmation {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>