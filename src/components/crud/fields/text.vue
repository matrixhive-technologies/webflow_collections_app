<template>
    <div>
        <label for="name"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" v-if="label">{{  label  }}</label>
        <input :type="type ? type : 'text'" v-model="value" :name="name" :maxlength="max"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 
                dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 
                dark:focus:border-primary-500"
                :placeholder="placeholder" :disabled="disable">
            <span class="error text-red-400 text-xs" v-show="!valid">{{ errorMessage }}</span>                
    </div>
   
</template>

<script setup lang="ts">
    import { watch } from 'vue';
    import type { fieldChange } from '@/components/crud/interface/crud-essentials'

    const emit = defineEmits<{
        (e: 'change', change: fieldChange): void
    }>()

    const props = defineProps<{
        name: string,
        label?: string,
        required?: Boolean,
        placeholder?: string,
        is_valid?:Function,
        is_valid_num?:Function,
        error_message? : String ,
        type?:string ,
        value?: any,
        disable?:boolean,
        max?:Number
    }>();

    const isValid = () =>{
        let v = true;
        v  = props.required && value.value.trim() == '' ? false : true;
        v = props.is_valid ? props.is_valid(value.value) :v;
        if(props.type && props.type == 'url'){
            isValidURL(value.value)
            v = validImage.value
            // console.log(validImage.value, 'vvvvvv')
            if(!validImage.value){
                errorMessage = "Please enter valid url"
            }
        }
        valid.value = v;
        return valid.value;
    }
    const isValidNum = () =>{
        let v = true;
        v  = props.required || value.value == '' ? true : true;
        v = props.is_valid_num ? props.is_valid_num(value.value) :v;
        valid.value = v;
        return valid.value;
    }

    let validImage = ref<boolean>(true)

    async function isValidURL(url:any) { 

        var pattern = new RegExp('^(https\\:\\/\\/)'); 

        validImage.value = pattern.test(url)
        if(pattern.test(url)){
            const res = await fetch(url),
            buff = await res.blob()
            validImage.value  = buff.type.startsWith('image/')
        }
        console.log(validImage.value, "validImage")

        return validImage.value
    }

    let errorMessage:any= props.error_message ? props.error_message : "Please insert valid details";
    let valid  = ref<boolean>(true);
    let value = ref<String>(props.value ? props.value : '');

    onBeforeUpdate(() => {
        props.value,
        value
    })
    defineExpose({
        isValid,
        isValidNum,
        props,
        value
    });

    watch(value, (n,o) => {
        isValid(),
        isValidNum(),
        emit('change',{
            new : n,
            old: o
        })
    });

    watch(() => props.error_message, (n,o) => {
        errorMessage = n;
    });
    
</script>