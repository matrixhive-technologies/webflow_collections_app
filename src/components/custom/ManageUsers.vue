<template>
    <div class="flex flex-row-reverse">
        <button type="button" class=" ml-2  right-0 bg-blue-500 text-white px-4 py-2 mb-2 rounded-md" @click="$emit('manageCollections')"> Manage Collections </button>
        <button type="button" class=" ml-2  right-0 bg-blue-500 text-white px-4 py-2 mb-2 rounded-md" @click="addUser" > Add User </button>
    </div>
    <List :columns="colums" :items="users" :loading="loading">
        <template #cell(actions)="{ item }" > 
           <PrimaryButton class="text-white" @click="editUser(item)">
                <SvgIcon name="edit"/>  
           </PrimaryButton>

           <PrimaryButton @click="deleteUser(item)">
                <SvgIcon name="delete"/>  
           </PrimaryButton>
        </template>
    </List>

    <Modal :is-visible="editUserModal || addUserModal" @close="editUserModal = false;addUserModal = false">
        <template #header > Edit User </template>
        <template #modal_content >
            
<form @submit="(event) => { event.preventDefault(); saveUser()}">
    <div class="grid gap-6 mb-6 md:grid-cols-2">
        <div>
            <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First name</label>
            <input v-model="selectedUser.first_name" type="text" id="first_name"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="John" required>
            
        </div>
        <div>
            <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last name</label>
            <input v-model="selectedUser.last_name" type="text" id="last_name"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Doe" required>
        </div>
       
    </div>
    <div class="mb-6">
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email address</label>
        <input type="email" v-model="selectedUser.email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="john.doe@company.com" required>
    </div> 
    <div v-if="editUserModal" class="m-2 text-sm"> Fill the password field to update your password or leave it empty. </div>
    <div class="mb-6">
        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
        <input type="password" v-model="selectedUser.password"  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="•••••••••" :required="addUserModal" >
    </div> 
   
    
    <button type="submit" @click="saveUser" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
</form>

        </template>
    </Modal>
</template>

<script lang="ts" setup>
    import { List } from '../crud';
    import { PrimaryButton, SvgIcon , Modal } from '../functional';
    let colums = [
       
        { key : 'first_name', label : 'First Name'},
        { key : 'last_name', label : 'Last Name'},
        { key : 'email', label : 'Email'},
        { key: 'actions', label: 'Actions' },
    ];

    let users = ref<any>([ ]);
    let loading = ref(false);
    let editUserModal = ref(false);
    let submitted = ref(false);
    let addUserModal = ref(false);

    let selectedUser = ref<any>(false);
    const editUser = (user:any) => {
        selectedUser.value = user;
        editUserModal.value = true;
        console.log(user);  
    }

    const loadUsers = () => {
        loading.value = true;
        // To Do :  make ajax call to load users 
        // set the below two values in ajax response 
        users.value = [{first_name : 'Rupesh' , last_name: 'Patel', 'email' : 'rupesh.patel@matrixhive.com' }];
        loading.value = false;

    }

    const deleteUser = (user:any) => {
        // call ajax to delete the user
    }

    const addUser = () => {
        selectedUser.value = {
            first_name : '',
            last_name : '',
            email : '',
            password : ''
        }
        addUserModal.value = true;
    }

    const saveUser = () => {
       // call ajax to save user
       loadUsers();
    }

    loadUsers();
</script>