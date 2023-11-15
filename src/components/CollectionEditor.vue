<template>
    <div class="relative py-8">
        <div v-if="editedCount > 0" class="text-right -mt-[93px] ">
            <button type="button"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 my-5 ml-2"
                @click="updateCollectionList()">
                Save & Publish {{ editedCount == 1 ? editedCount + " Record" : (editedCount > 1 ? editedCount + " Records" :
                    '') }}
            </button>
        </div>

        <div v-if="editMessage" class="text-gray-50">{{ editMessage }} </div>

        <teleport to="#columnsDropdown" v-if="elementExists">
            <Dropdown :options="listCols" :checkedOptions="checkedOptions" label="Select Columns to Display "
                @change="columnChangeHandler">
            </Dropdown>
        </teleport>

        <CustomList :columns="visibleColumns" :items="listItems" class="table-auto" @editEvent="editHandler"
            :referenceData="referenceData"></CustomList>
    </div>
</template>

<script setup lang="ts">
import ajax from "@/accessories/ajax";
import CustomList from "@/components/custom/CustomList.vue";
import Dropdown from "@/components/crud/fields/dropdown.vue";

const props = defineProps({
    selectedSiteId: { type: Number, default: 0 },
    selectedCollectionId: { type: Number, default: 0 },
})

let listCols = ref<Array<any>>([]);
let checkedOptions = ref<Array<any>>([]);
let listItems = ref<Array<any>>([]);
let editHistory = ref<Array<any>>([]);
let editMessage = ref<string>('');
let editedData: any = {};
let editedCount = ref<number>(0);

let visibleColumns = ref<Array<any>>([]);

let referenceData = ref<any>({});

const elementExists = ref(false);

onMounted(() => {
    // Check if the element exists on mount
    elementExists.value = document.body.contains(document.querySelector('#columnsDropdown'));
});


// watch works directly on a ref
watch(() => props.selectedCollectionId, (first, second) => {
    console.log(
        "Watch props.selected function called with args:",
        first,
        second
    );
    collectionFields();
    collectionList();
    editedData = {};
    editedCount.value = 0;
});


// Selected Collection's Field Type
async function collectionFields() {
    let aj = new (ajax as any)();
    let data =
    {
        endPoint: "collections/" + props.selectedCollectionId,
        params: props.selectedCollectionId,
    };

    let result = await aj.post("/CallApi.php", data);


    if (result.status == 200) {
        let requiredFields = [];
        let optionalFields = [];
        for (let i in result.data.fields) {

            if (result.data.fields[i]['isRequired'] == true) {
                requiredFields.push({ key: result.data.fields[i]['slug'], label: result.data.fields[i]['displayName'], item_type: result.data.fields[i]['type'], validations: result.data.fields[i]['validations'] });
                console.log("required", requiredFields);

            } else {
                optionalFields.push({ key: result.data.fields[i]['slug'], label: result.data.fields[i]['displayName'], item_type: result.data.fields[i]['type'], validations: result.data.fields[i]['validations'] });

                console.log("optionalFields", optionalFields);
            }

            if (result.data.fields[i]['type'] == 'Reference' || result.data.fields[i]['type'] == 'MultiReference') {
                loadReferencedData(result.data.fields[i]['validations'].collectionId);
            }

        }
        listCols.value = requiredFields.concat(optionalFields);

        console.log('final array', listCols.value);

        let aj = new (ajax as any)();
        let params = {
            collectionId: props.selectedCollectionId
        };
        let storedCollections = await aj.get("/collections.php", params);
        console.log(storedCollections);
        if (storedCollections.data.selectedCols) {
            visibleColumns.value = storedCollections.data.selectedCols;
            checkedOptions.value = storedCollections.data.selectedCols;
        }
        else {
            visibleColumns.value = listCols.value;
            checkedOptions.value = listCols.value;
        }



    } else {
        return false;
    }
}


async function loadReferencedData(collection_id: any) {
    let aj = new (ajax as any)();
    let data =
    {
        endPoint: "collections/" + collection_id + "/items/",
    };

    let result = await aj.post("/CallApi.php", data);

    if (result.status == 200) {
        referenceData.value[collection_id] = {};

        result.data.items.forEach((item: any, index: any) => {
            referenceData.value[collection_id][index] = { 'id': item.id, 'name': item.fieldData.name };
        });

        console.log('Referenced Data', referenceData.value);
    }
}

// Selected Collection's Item List
async function collectionList() {

    let aj = new (ajax as any)();
    let data =
    {
        endPoint: "collections/" + props.selectedCollectionId + "/items",
        params: props.selectedCollectionId,
    };

    let result = await aj.post("/CallApi.php", data);


    if (result.status == 200) {
        let itemData = [];
        for (let i in result.data.items) {
            result.data.items[i].fieldData.item_id = result.data.items[i].id;
            itemData.push(result.data.items[i].fieldData);
        }
        listItems.value = itemData;

    } else {
        return false;
    }


}

let contentVisible = ref(false);
onMounted(() => {
    teleportContent();

    document.addEventListener('teleport-content', () => {
        contentVisible.value = true;
    });
});

const teleportContent = () => {
    const event = new Event('teleport-content');
    document.dispatchEvent(event);
};


function columnChangeHandler(updatedList: any) {
    visibleColumns.value = updatedList;

    let aj = new (ajax as any)();
    let data =
    {
        collectionId: props.selectedCollectionId,
        selectedCols: JSON.stringify(updatedList),
    };

    aj.post("/collections.php", data);


}

function editHandler(data: any) {
    console.log("Edited", data);
    if (data.old_value != data.new_value) {
        editHistory.value.push(data);
        editedData[data.item_id] = editedData[data.item_id] ? editedData[data.item_id] : {};
        editedData[data.item_id][data.column_key] = data.new_value;
        editedCount.value = Object.keys(editedData).length;
    }
}

async function updateCollectionList() {
    console.log('collection_id', props.selectedCollectionId);
    console.log("Edited Data from update", editedData);
    let updateData = {};
    let aj = new (ajax as any)();
    let index = 1;
    let itemIdsArr = [];
    let publishData = {};
    for (let i in editedData) {
        updateData = { "isArchived": false, "isDraft": false, fieldData: editedData[i] };
        let data =
        {
            method: 'PATCH',
            endPoint: "collections/" + props.selectedCollectionId + "/items/" + i,
            params: JSON.stringify(updateData),
        };


        editMessage.value = "Editing" + index + " Record Out of " + editedCount.value;

        let result = await aj.post("/CallApi.php", data);

        if (result.status == 200) {
            // Publish the items to site only if update is successful - starts here.
            itemIdsArr.push(i);
            publishData = { itemIds: itemIdsArr };

            let data =
            {
                method: 'POST',
                endPoint: "collections/" + props.selectedCollectionId + "/items/publish",
                params: JSON.stringify(publishData),
            };


            let publishResult = await aj.post("/CallApi.php", data);


            if (publishResult.status == 200) {
                editMessage.value = "Updated & Published Items Successfully";
            } else {
                editMessage.value = "Something Went Wrong at " + i + " th record";
                return false;
            }
            // Publish the items to site only if update is successful - ends here.

        } else {
            editMessage.value = "Something Went Wrong at " + i + " th record";
            return false;
        }

    }
    editMessage.value = "All Records Updated Successfully!";
    window.setTimeout(function () {
        editMessage.value = '';
        editedCount.value = 0;
    }, 3000);
    console.log('Updated Data pushed', updateData);

}



</script>