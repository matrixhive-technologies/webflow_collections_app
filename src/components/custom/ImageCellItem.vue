<template>
    <div>
        <img :src="displayValue?.url" class="w-20 cursor-pointer" @click="modalVisible = !modalVisible">
        <span>
            <Modal :key="renderKey" :isVisible="modalVisible" @close="closeModal" :class="modalClass">
                <template v-slot:header>
                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Item Image</h3>
                </template>
                <template v-slot:modal_content>
                    <template v-if="showInitialContent">
                        <!-- Initial Content -->
                        <span v-if="optimiseMessage" class="text-l text-yellow-400 mb-1">{{
                            optimiseMessage
                        }}</span>
                        <img :src="displayValue?.url" class="w-full mb-2">

                        <p v-if="originalBytes > 0" class="mb-4 text-m font-medium text-gray-900 dark:text-white">
                            Original Size: {{ originalBytes }} KBs
                        </p>

                        <p v-if="optimisedBytes > 0" class="mb-4 text-m font-medium text-gray-900 dark:text-white">
                            Optimised Size: {{ optimisedBytes }} KBs
                        </p>

                        <p v-if="originalBytes - optimisedBytes > 0"
                            class="mb-4 text-m font-medium text-gray-900 dark:text-white">
                            Saved KBs: {{ diff }} KBs
                        </p>
                        <!-- ... other content ... -->

                        <button :class="{ 'opacity-50 cursor-not-allowed': optimiseButtonDisable }"
                            class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                            @click="optimiseImage(column_key)" :disabled="optimiseButtonDisable">Optimise</button>

                        <button
                            class="focus:outline-none text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 float-right"
                            @click="onUploadNewClick">
                            Upload New
                        </button>
                    </template>
                    <template v-else>
                        <!-- Form for Upload New -->
                        <span v-if="uploadMessage" class="text-xl font-medium text-green-600 dark:text-green mb-1">{{
                            uploadMessage
                        }}</span>

                        <form @submit.prevent="onUploadFormSubmit">
                            <div class="flex flex-row mb-3">
                                <input type="file" ref="fileInputRef" accept="image/*" required
                                    @change="handleFileChange" />
                            </div>
                            <div class="flex flex-row mb-3">
                                <div v-if="imagePreview">
                                    <VuePictureCropper :boxStyle="{
                                        width: '100%',
                                        height: '100%',
                                        backgroundColor: '#f8f8f8',
                                        margin: 'auto',
                                    }" :img="imagePreview" 
                                    :options="{
                                        viewMode: 1,
                                        dragMode: 'crop',
                                        zoomable: false,
                                     
                                        aspectRatio: selectedAspectRatio,
                                    }" @crop="handleCrop" />
                                    <!-- <img :src="imagePreview" alt="Preview" /> -->
                                </div>
                            </div>
                            <div class="flex flex-row mb-3">

                                <SelectDropdown name="aspect_ratios" label="Aspect Ratios"
                                    :key="'ar_'+item_id+column_key"
                                    :value="aspectRationProp"
                                    :options="props.aspectRatios ? props.aspectRatios.concat([{value:'add_new' , label:'Add New'}]) : [{value:'add_new' , label:'Add New'}]" @change="setAspectRatio">
                                </SelectDropdown>

                                <div class="ml-4">
                                    <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" > Max Width</label>
                        <input type="number" v-model="maxWidth" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 
                                dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 
                                dark:focus:border-primary-500"
                               >
                                </div>
                                
                                
                            </div>

                            <!-- <div class="flex flex-row mb-3">
                                <Text name="max_width" label="Enter Max Width (Enter Numeric value)"
                                    placeholder="200px..."></Text>
                            </div> -->

                            <button type="submit" :class="{ 'opacity-50 cursor-not-allowed': uploadButtonDisable }"
                                class="bg-green-500 text-white px-4 py-2 rounded-lg mt-2"
                                :disabled="uploadButtonDisable">Upload</button>
                        </form>
                    </template>
                </template>
            </Modal>
        </span>
    </div>
</template>

<script lang="ts" setup>
import ajax from '@/accessories/ajax';
import VuePictureCropper, { cropper } from 'vue-picture-cropper'
import { Modal } from "@/components/functional";
import { SelectDropdown } from '@/components/crud'
import { userStore } from '@/stores/user';

const userObj = userStore();

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


let displayValue = ref<any>(props.item_value);
let renderKey = ref<number>(0);
let modalClass = 'w-[750px]';
let optimiseMessage = ref<string>('');

let optimiseButtonDisable = ref(false);

let originalBytes = ref<number>(0);
let optimisedBytes = ref<number>(0);

let selectedAspectRatio = ref<any>();
let aspectRationProp = ref(props.aspectRatios && props.aspectRatios[0] ? props.aspectRatios[0].value : '');

let imagePreview = ref<any>();
let diff = ref<number>(0);

let uploadMessage = ref('');
let uploadButtonDisable = ref(false);
const showInitialContent = ref(true);
const modalVisible = ref<boolean>(false);

const onUploadNewClick = () => {
    showInitialContent.value = false;
    uploadButtonDisable.value = false;
}

const setAspectRatio = (change: any) => {
    if(change.new == 'add_new') {
        userObj.setAspectRatioModal(true);
        aspectRationProp.value = 0;
        nextTick(()=>{
            aspectRationProp.value = '';
        })
        
    } else {
        selectedAspectRatio.value = change.new;
        aspectRationProp.value = change.new;
        let temp = imagePreview.value;
        imagePreview.value = false;
        nextTick(()=>{
            imagePreview.value = temp;
        })
        console.log("selectedAspectRatio", selectedAspectRatio.value);
    }
    
}

const handleFileChange = (event: any) => {
    const file = event.target.files[0];

    console.log("fileee", file);

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = () => {
            console.log("reader.result", reader.result);
            imagePreview.value = reader.result;
        };

        reader.readAsDataURL(file);
    } else {
        imagePreview.value = null;
    }
};
const closeModal = () => {
    modalVisible.value = !modalVisible.value;
    showInitialContent.value = true;
    optimiseMessage.value = '';
    uploadMessage.value = '';
    uploadButtonDisable.value = false;
}

// optimise the image and convert it to webP.
async function optimiseImage(column_key: any) {
    optimiseButtonDisable.value = true;
    let aj = new (ajax as any)();
    let data =
    {
        image_url: displayValue.value.url,
    };
    let result = await aj.post("/image.php", data);
    console.log('success optimise', result.data.url);
    if (result.data.code == 200) {
        renderKey.value += 1;
        displayValue.value.url = result.data.url;
        if (result.data.optimisedBytes == 0) {
            optimiseButtonDisable.value = false;
            optimiseMessage.value = 'Already optimised';
            originalBytes.value = 0;
            optimisedBytes.value = 0;
            return false;
        }
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
            displayValue.value.url = result2.data.fieldData?.[props.column_key].url;
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
                diff.value = originalBytes.value - optimisedBytes.value;
                if (diff.value > 0) {
                    diff.value = Number(diff.value.toFixed(2));
                    optimiseMessage.value = 'Image converted to webP and ' + diff.value + " KBs are saved.";
                } else {
                    optimiseMessage.value = 'Image is already optimised';
                }
                unlinkImage(result.data.outputPath);
            }
        }
    } else {
        optimiseMessage.value = 'Something went wrong';
        optimiseButtonDisable.value = false;
    }
}

async function unlinkImage(img: any) {
    let aj = new (ajax as any)();
    let data =
    {
        "action": "unlinkImg",
        img_path: img,
    };
    let result = await aj.post("/image.php", data);
    if (result) {
        console.log(result);
    }
}
let maxWidth = ref(1000);
watch(maxWidth,() => {
    let temp = imagePreview.value;
    imagePreview.value = false;
    nextTick(()=>{
        imagePreview.value = temp;
    })
})
const handleCrop = (event:any) => {
    
    console.log(event.detail, "Crop event");
    let imageData = cropper?.getImageData();
    //let xRatio = imageData?.width/imageData?.naturalWidth;
    //let yRatio = imageData?.height/imageData?.naturalHeight;

   // console.log( (event.detail.width*imageData?.width)/imageData?.naturalWidth , event.detail.width/xRatio );
    
    if(event.detail.width > maxWidth.value && imageData){
        
        let cropperData =  cropper?.getCropBoxData(); 
        console.log(cropperData);
        
        if(cropperData){
            let orWidth = cropperData.width;
            cropperData.width = (maxWidth.value*imageData?.width)/imageData?.naturalWidth;
            console.log('From '+orWidth+' To : '+cropperData.width);
            cropper?.setCropBoxData(cropperData);
        
        }
        
    }
    /*cropper?.getFile().then((file) => {
        console.log('File', file)
    })*/
}

async function onUploadFormSubmit() {
    uploadButtonDisable.value = true;
    let aj = new (ajax as any)();
    
    cropper?.getFile().then(async (file) => {
        let data ={
            uploaded_file: file,
            aspectRatio: selectedAspectRatio.value
        };
        let result = await aj.postForm("/image.php", data);
        if(result.data.code == 200){
            processImageWebPResponse(result);
          /*  emits('editEvent', {
                item_id: props.item_id,
                old_value: props.item_value,
                new_value: {url: result.data.url},
                column_key: props.column_key
            });*/
        }
        
    })
    return false;
}

async function processImageWebPResponse(result:any){
    let aj = new (ajax as any)();
    if (result.data.code == 200) {
        displayValue.value.url = result.data.url;
        let fieldData = {};
        fieldData = { "isArchived": false, "isDraft": true, 
            "fieldData": { 
                [props.column_key]: { 
                    "url":   result.data.url 
                } 
            } 
        };

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
            displayValue.value.url = result2.data.fieldData?.[props.column_key].url;

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
                setTimeout(function () {
                    modalVisible.value = !modalVisible.value;
                    showInitialContent.value = true;
                    uploadMessage.value = '';
                }, 2000);
                unlinkImage(result.data.outputPath);

            }
        }
    } else {
        uploadMessage.value = 'Something went wrong';
        uploadButtonDisable.value = false;
    }
}

</script>