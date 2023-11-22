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
                    <Modal :key="renderKey" :isVisible="modalVisible" @close="closeModal" :class="modalClass">
                        <template v-slot:header>
                            <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Item Image</h3>
                        </template>
                        <template v-slot:modal_content>
                            <template v-if="showInitialContent">
        <!-- Initial Content -->
        <span v-if="optimiseMessage" class="text-xl font-medium text-green-600 dark:text-green mb-1">{{ optimiseMessage }}</span>
        <img :src="displayValue?.url" class="w-full mb-2">
        <p v-if="originalBytes > 0" class="mb-4 text-m font-medium text-gray-900 dark:text-white">
          Original Size: {{ originalBytes }} Bytes
        </p>
        <!-- ... other content ... -->

        <button :class="{ 'opacity-50 cursor-not-allowed': optimiseButtonDisable }"
                                class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                @click="optimiseImage(column_key)" :disabled="optimiseButtonDisable">Optimise</button>
        
        <button
          class="focus:outline-none text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 float-right"
          @click="onUploadNewClick"
        >
          Upload New
        </button>
      </template>
      <template v-else>
        <!-- Form for Upload New -->
        <span v-if="uploadMessage" class="text-xl font-medium text-green-600 dark:text-green mb-1">{{ uploadMessage }}</span>
        
        <form @submit.prevent="onUploadFormSubmit">
          <input type="file"  ref="fileInputRef"  accept="image/*" required />
          <button type="submit" :class="{ 'opacity-50 cursor-not-allowed': uploadButtonDisable }" class="bg-green-500 text-white px-4 py-2 rounded-lg mt-2" :disabled="uploadButtonDisable">Upload</button>
        </form>
      </template>
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
import ajax from "@/accessories/ajax";
import { Modal } from "@/components/functional";
let editMode = ref<boolean>(false);

let modalClass = 'w-[750px]';

let renderKey = ref<number>(0);

const emits = defineEmits(["editEvent"]);
const showInitialContent = ref(true);

const modalVisible = ref<boolean>(false)

let fileInputRef = ref(null);

let uploadMessage = ref('');
let uploadButtonDisable = ref(false);
const onUploadNewClick = () => {
      showInitialContent.value = false;
}
async function onUploadFormSubmit() {
    uploadButtonDisable.value = true;
    let aj = new (ajax as any)();

    let data =
    {
        uploaded_file: fileInputRef.value.files[0] ,
    };
    let result = await aj.post("/image.php", data);
    // if(result.data.code == 200) {
    //     displayValue.value.url = result.data.url;
    //     modalVisible.value = !modalVisible.value;
    //     console.log('upload result',result);
    // }

    if (result.data.code == 200) {
        displayValue.value.url = result.data.url;
        let fieldData = {};
        fieldData = { "isArchived": false, "isDraft": false, "fieldData": { [props.column_key]: { "url": result.data.url } } };

        let data2 =
        {
            method: 'PATCH',
            endPoint: "collections/" + [props.collectionID] + "/items/" + [props.item_id],
            params: JSON.stringify(fieldData),
        };

        console.log('data2', data2);

        let result2 = await aj.post("/CallApi.php", data2);
        console.log('result 2', result2);
        if (result2.status == 200) {
            let publishData = {};
            let itemIdsArr = [];
            itemIdsArr.push(props.item_id);
            publishData = { itemIds: itemIdsArr };


            let data3 =
            {
                method: 'POST',
                endPoint: "collections/" + [props.collectionID] + "/items/publish",
                params: JSON.stringify(publishData),
            };

            let publishResult = await aj.post("/CallApi.php", data3);


            if (publishResult.data.errors.length > 0) {
                uploadMessage.value = publishResult.data.errors[0];
                return false;
            } else {
                uploadButtonDisable.value = false;
                uploadMessage.value = 'New Image is uploaded to webflow ';
                setTimeout(function() {
                    modalVisible.value = !modalVisible.value;
                    showInitialContent.value = true;
                    uploadMessage.value = '';
                }, 2000);
            }
        }
    } else {
        uploadMessage.value = 'Something went wrong';
    }

    //   showInitialContent.value = true;
       
}

const closeModal = () => {
    modalVisible.value = !modalVisible.value ;
    showInitialContent.value = true;
    optimiseMessage.value = '';
    uploadMessage.value = '';
}

const props = defineProps<{
    item_type?: string,
    column_key: string,
    item_value: any,
    item_id: string,
    validations?: any,
    referenceData?: any,
    aspectRatios?: Array<any>,
    collectionID?: any,
}>();

let editValue = ref<string>(props.item_value);
let displayValue = ref<any>(props.item_value);

let optimiseMessage = ref<string>('');

let optimiseButtonDisable = ref(false);

let originalBytes = ref<number>(0);
let optimisedBytes = ref<number>(0);

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


// optimise the image and convert it to webP.
async function optimiseImage(column_key: any) {
    optimiseButtonDisable.value = true;
    let aj = new (ajax as any)();
    let data =
    {
        image_url: displayValue.value.url ,
    };
    let result = await aj.post("/image.php", data);
    console.log('success optimise', result.data.url);
    if (result.data.code == 200) {
        renderKey.value+=1;
        displayValue.value.url = result.data.url;
        // if (result.data.extension == 'webp') {
        //     optimiseButtonDisable.value = true;
        //     optimiseMessage.value = 'Already optimised';
        //     originalBytes.value = 0;
        //     optimisedBytes.value = 0;
        //     return false;
        // }
        originalBytes.value = result.data.originalBytes;
        optimisedBytes.value = result.data.optimisedBytes;
        let fieldData = {};
        fieldData = { "isArchived": false, "isDraft": false, "fieldData": { [column_key]: { "url": result.data.url } } };

        let data2 =
        {
            method: 'PATCH',
            endPoint: "collections/" + [props.collectionID] + "/items/" + [props.item_id],
            params: JSON.stringify(fieldData),
        };

        console.log('data2', data2);

        let result2 = await aj.post("/CallApi.php", data2);
        console.log('result 2', result2);
        if (result2.status == 200) {
            let publishData = {};
            let itemIdsArr = [];
            itemIdsArr.push(props.item_id);
            publishData = { itemIds: itemIdsArr };


            let data3 =
            {
                method: 'POST',
                endPoint: "collections/" + [props.collectionID] + "/items/publish",
                params: JSON.stringify(publishData),
            };

            let publishResult = await aj.post("/CallApi.php", data3);

            console.log(publishResult.data);
            optimiseButtonDisable.value = false;

            if (publishResult.data.errors.length > 0) {
                optimiseMessage.value = publishResult.data.errors[0];
                return false;
            } else {
                let diff = originalBytes.value - optimisedBytes.value;
                if(diff > 0) {
                optimiseMessage.value = 'Image converted to webP and ' + diff+" bytes are saved.";
                } else {
                    optimiseMessage.value = 'Image is already optimised';
                }
            }
        }
    } else {
        optimiseMessage.value = 'Something went wrong';
        optimiseButtonDisable.value = false;
    }
}

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
            