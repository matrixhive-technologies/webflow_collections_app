<template>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">

                    <table :class="className ? className : 'min-w-full'"
                        class="w-full text-sm text-left text-gray-500 dark:text-gray-800">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>

                                <th v-for="column in columns" scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        <slot :name="`header(${column.key})`">
                                            {{ column.label }}
                                        </slot>
                                    </div>
                                </th>

                                <th v-if="columns.length > 0">
                                    Created On
                                </th>

                                <th v-if="columns.length > 0">
                                    Action
                                </th>



                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            <template v-if="items.length > 0" :columns="columns" v-for="item, index in items" :item="item"
                                :index="index">
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td v-for="column in columns"
                                        :class="column.class ? column.class : ' p-4 text-sm font-normal text-gray-500 dark:text-gray-400'">
                                        {{ item[column.label] }}
                                    </td>

                                    <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"
                                        v-if="columns.length > 0">
                                        {{
                                            moment(item.createdOn).format('MMMM Do YYYY, h:mm:ss a')
                                        }}
                                    </td>


                                    <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400"
                                        v-if="columns.length > 0">
                                        <button
                                            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                            @click="$emit('deleteRatio', item.id)">Delete</button>
                                    </td>


                                </tr>


                            </template>
                            <template v-else>
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td :colspan="columns.length + 3" class="text-center">
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
import moment from 'moment';

const emit = defineEmits(['deleteRatio']);

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
}>();
</script>