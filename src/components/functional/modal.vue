<template>
    <div tabindex="-1" aria-hidden="true" :class="!isVisible ? 'hidden' : '' " class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex bg-[#00000080]">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div :class="class" class="relative bg-white rounded-lg shadow dark:bg-gray-700">

                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        <slot name="header"></slot>
                    </h3>
                    <button @click="closeModal" type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                
                <div class="p-4 md:p-5 space-y-4 text-gray-500 dark:text-gray-400">
                    
                    <slot name="modal_content"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">


const props = defineProps<{
    isVisible : boolean
    class? : string
}>();



const emit =  defineEmits(['close']);

async function closeModal(){
    emit('close');
}
</script>