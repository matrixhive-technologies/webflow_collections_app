<template>
    <div class="flex items-center justify-center mx-auto md:h-screen ">
        <div class="w-1/2 max-w-screen-md ml-auto mr-auto mt-0 mb-0 bg-white rounded-lg drop-shadow-none dark:bg-gray-800">
            <div class="form-box px-10 py-20 shadow-md">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Welcome Back 👋
                </h2>
                {{ msg }}
                <form ref="form" id="login" method="POST" :action="getAction()">
                    <Form @save="login" >

                        <template #fields="{ refs }">
                            <h1 class="text-2xl font-bold text-red-500">{{ validationErr }}</h1>
                            <TextField name="email" label="Email" :is_valid="emailValidation"
                                :ref="(ele: any) => { refs.push(ele) }"></TextField>
                            <TextField name="password" type="password" label="Password" :is_valid="passwordValidation"
                                :error_message="passwordErrorMessage" :required="true"
                                :ref="(ele: any) => { refs.push(ele) }">
                            </TextField>

                        </template>
                        <template #deleteHeader>
                            <p></p>
                        </template>
                        <template #deleteSubHeader>
                            <p></p>
                        </template>
                        <template #svg>
                            <p></p>
                        </template>
                        <template #actions="{ save }" class="inline-block w-full">
                            <button type="button"
                                class="my-3 w-full justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" @click="save()">Sign
                                In</button>
                        </template>

                        <template #actionsDelete>
                            <p></p>
                        </template>
                    </Form>
                </form>
            </div>
        </div>
    </div>
</template>
    
<script setup lang="ts">
import { TextField } from '@/components/crud';
import ajax from '@/accessories/ajax';
import { userStore } from '@/stores/user';
import type { user } from '@/stores/user';
import { useRouter } from 'vue-router';
import { ref } from 'vue';
import { Form } from '@/components/crud';
import { $ } from 'vue/macros';

const userStoreObj = userStore();
const router = useRouter()
const emailValidation = (value: string) => {
    const expression: RegExp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
    email = value
    validationErr.value = '';
    const result: boolean = expression.test(email)
    if (result) {
        return true;
    }
    else {
        return false;
    }
}
let passwordErrorMessage = ref<String>("");
let validationErr = ref<String>("");
const passwordValidation = (value: string) => {
    password = value
    validationErr.value = '';
    let v = true;
    if (value == '') {
        passwordErrorMessage.value = 'Password is required.'
        v = false;
    }
    return v;
}

let email: string = ''
let password: string = ''
let msg: string = ''

const form = ref(null);

function getAction() {
    return import.meta.env.VITE_API_URL + "/login.php";
}

const ajaxObj = new (ajax as any)();

const initiate = () => {
    // if (userStoreObj.isLoggedIn) {
    //     router.push(userStoreObj.afterLoginRoute);
    // } else {
    //     userStoreObj.removeUser();
    //     router.push('login');
    // }

}

const login = () => {
    // let details = {
    //     'email': email,
    //     'password': password
    // };
    // console.log(form);
    window.form = form;
    form.value?.submit();
    // let { data } = await ajaxObj.post(, details, {});

    // if (data.success) {
    //     let userObj: user = {
    //         firstName: data.name,
    //         lastName: 'Patel',
    //         'email': email,
    //         'password': password,
    //         authToken: data.token
    //     }
    //     userStoreObj.setUser(userObj);
    //     router.push(userStoreObj.afterLoginRoute);
    // }
    // else {
    //     console.log(data.message)
    //     validationErr.value = 'Please insert valid details'
    //     return false;
    // }
}
initiate();
</script>