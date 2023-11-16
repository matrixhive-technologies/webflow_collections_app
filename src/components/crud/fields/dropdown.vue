<template>
  <button id="dropdownBgHoverButton" data-dropdown-toggle="dropdownBgHover"
    class="text-white bg-blue-700 border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
    type="button">{{ label }}<svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
      fill="none" viewBox="0 0 10 6">
      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
    </svg>
  </button>

  <!-- Dropdown menu -->
  <div id="dropdownBgHover" class="z-10 hidden w-48 bg-white rounded-lg shadow dark:bg-gray-700">
    <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownBgHoverButton">

      <li>
        <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
          <label for="" class="w-full ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Please
            Select</label>
        </div>
      </li>

      <li v-for="option in options">
        <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
          <input :id="option.key" type="checkbox" :value="option"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
            @change="changeHandler()" v-model="checkedOptionsLocal">
          <label for=option.key class="w-full ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{
            option.label }}</label>
        </div>
      </li>

      <li>
        <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
          <input type="checkbox"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
            checked v-model="showDates">
          <label for=option.key class="w-full ml-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">Show
            Dates</label>
        </div>
      </li>

    </ul>
  </div>
</template>

<script setup lang="ts">

const emit = defineEmits(["change","showDates"]);

const props = defineProps<{
  items?: Array<any>,
  label: string,
  value?: string,
  id?: string,
  for?: string,
  options: Array<any>,
  checkedOptions: Array<any>,
}>();

let checkedOptionsLocal = ref<Array<any>>([]);
let showDates = ref(true);

watch(() => props.checkedOptions, () => {
  console.log('props.checkedOptions', props.checkedOptions);
  checkedOptionsLocal.value = props.checkedOptions;
});

watch(() => showDates.value, () => {
  emit("showDates", showDates.value);
  console.log('showDates.value', showDates.value);
});


function changeHandler() {
  let orderedList = [];
  for (let option in props.options) {
    let isExists = false;

    for (let checkedOption in checkedOptionsLocal.value) {
      if (checkedOptionsLocal.value[checkedOption].key == props.options[option].key) {
        isExists = true;
        break;
      }
    }
    if (isExists) {
      orderedList.push(props.options[option]);
    }

  }

  emit('change', orderedList);

}
</script>