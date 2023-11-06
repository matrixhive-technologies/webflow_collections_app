<template>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <span class="text-gray-600 mx-2" v-if="checkItems">Selected Items : {{ checkedItems.length }}</span>
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
                                    <slot :name="`header(${column.key})`">
                                        {{ column.label }}
                                    </slot>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

                            <template v-if="items.length > 0" :columns="columns" v-for="item, index in items" :item="item"
                                :index="index">
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="w-4 p-4" v-if="checkItems">
                                        <div class="flex items-center">
                                            <input aria-describedby="checkbox-1" type="checkbox" name="checkbox[]"
                                                class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                                :value="item[ckColumn]" v-model="checkedItems">
                                        </div>
                                    </td>

                                    <td v-for="column in columns"
                                    class="break-words"
                                        :class="column.class ? column.class : ' p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400'">
                                        <slot :name="`cell(${column.key})`" :item="item" :index="index">
                                            <CellItem :column_key="column.key" :item_value="item[column.key]"
                                                :item_id="item['item_id']" @editEvent="editHandler"
                                                :item_type="column.item_type" :validations="column.validations"
                                                :referenceItemData="props.referenceItemData">
                                            </CellItem>
                                        </slot>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td :colspan="columns.length + 1" class="text-center">
                                        <slot v-if="loading" name="loading">
                                            Loading ...
                                        </slot>
                                        <slot v-else name="no_records">
                                            No Records
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
import CellItem from "@/components/custom/CellItem.vue"
const emits = defineEmits(["action", "editEvent"]);
let checkedItems: any = ref<Array<any>>([]);
const props = defineProps<{
    items: Array<any>,
    loading?: boolean,
    columns: any,
    checkItems?: boolean,
    checkColumn?: string,
    className?: string,
    editData?: any,
    referenceItemData?: Array<any>,
}>();

let ckColumn = props.checkColumn ? props.checkColumn : "id";

async function editCheckedData() {
    if (props.editData && props.editData.contacts) {
        props.editData.contacts.forEach((val: any, index: any) => {
            checkedItems.value[index] = val[ckColumn]
        });
    }
}
editCheckedData()

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

defineExpose({
    checkedItems
});
</script>