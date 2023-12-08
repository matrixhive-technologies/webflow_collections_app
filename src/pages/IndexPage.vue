<script setup lang="ts">
import CollectionEditor from '@/components/CollectionEditor.vue'
import { SelectDropdown } from '@/components/crud'
import ajax from "@/accessories/ajax";
import { userStore } from '@/stores/user';
import imageUrl from '@/assets/images/PL-logo-350.png';

import AspectRatio from "@/components/custom/AspectRatio.vue";
import ManageUsers from "@/components/custom/ManageUsers.vue";
import { Form } from "@/components/crud"
// useRoute, useHead, and HelloWorld are automatically imported. See vite.config.ts for details.
const route = useRoute()

let modalClass = 'w-[750px]';

const modalVisible = ref<boolean>(false);

useHead({
  title: route.meta.title,
  meta: [
    {
      property: 'og:title',
      content: route.meta.title,
    },
    {
      name: 'twitter:title',
      content: route.meta.title,
    },
  ],
});

let selectedSiteId = ref<number>(0);
let selectedCollectionId = ref<number>(0);
let collectionAspect = ref<number>(0);
let sites = ref<Array<any>>([]);
let collections = ref<Array<any>>([]);
let renderKey = ref<number>(0);
let renderDrodpown = ref<number>(0);
let success = ref<String>('');

let savedRatios = ref<Array<any>>([]);
let ratioColumns = ref<any>();

let manageUsers = ref(false);

// Site Ids list
async function listOfSites() {
  try {
    let aj = new (ajax as any)();
    let data =
    {
      endPoint: "sites",
    };



    let result = await aj.post("/CallApi.php", data);

    if (result.status == 200) {
      sites.value = [];
      let SiteDetails = [];
      for (let i in result.data.sites) {
        let st = result.data.sites[i];
        SiteDetails.push({ value: st.id, label: st.displayName })
      }
      sites.value = SiteDetails;
    } else {
      return false;
    }
  } catch (error) {
    console.log("Site List error:--" + error);
  }
}
listOfSites();

// Selected Site Id's collections list
async function siteCollection(siteId: any) {
  selectedSiteId.value = siteId.new;

  let aj = new (ajax as any)();
  let data =
  {
    endPoint: "sites/" + selectedSiteId.value + "/collections",
    params: selectedSiteId.value,
  };

  let result = await aj.post("/CallApi.php", data);
  if (result.status == 200) {
    collections.value = [];
    let collectionDetails = [];
    for (let i in result.data.collections) {
      let st = result.data.collections[i];
      collectionDetails.push({ value: st.id, label: st.displayName })
    }
    collections.value = collectionDetails;
  }
}

function collectionChangeHandler(change: any) {
  selectedCollectionId.value = change.new;
}
const userStoreObj = userStore();

const router = useRouter()
if (userStoreObj.isLoggedIn) {
  console.log(userStoreObj.authToken, "The auth token");
} else {
  userStoreObj.removeUser();
  router.push('login');
}


window.setInterval(function () {
  let aj = new (ajax as any)();
  let result = aj.post("/persist.php");
  console.log(result);
}, 30000);

async function saveAspectRatio(data: any) {
  let aj = new (ajax as any)();
  data.collection_id = 0;
  data.selectedSiteId = selectedSiteId.value;
  data.action = "save";
  let result = await aj.post("/aspectRatio.php", data);
  console.log('success', result);
  if (result.status == 200) {
    renderKey.value += 1;
    success.value = result.data.message;
    listAspectRatio(selectedSiteId.value, collectionAspect.value ? collectionAspect.value : selectedCollectionId.value);
  }
}

function closeModal() {
  success.value = '';
  savedRatios.value = [];
  userStoreObj.setAspectRatioModal(false);
  modalVisible.value = !modalVisible.value;
  collectionAspect.value = 0;
}
function openModal() {
  modalVisible.value = !modalVisible.value;
  userStoreObj.setAspectRatioModal(true);
  listAspectRatio(selectedSiteId.value, selectedCollectionId.value);
  renderDrodpown.value += 1;
}

ratioColumns.value =
  [
    { value: 'width', label: 'width' },
    { value: 'height', label: 'height' },
    { value: 'aspectRatio', label: 'aspectRatio' },
  ];

async function listAspectRatio(siteId: any, collectionID: any) {
  collectionID = 0;
  let aj = new (ajax as any)();
  let data =
  {
    action: "list",
    siteId: siteId,
    collectionID: collectionID,
  };
  console.log('collection ID--', collectionID);
  let result = await aj.post("/aspectRatio.php", data);
  console.log('success list', result);
  if (result.status == 200) {
    savedRatios.value = result.data.listData;
    console.log('savedRatios.value', savedRatios.value);

  }

}

function getCollectionId(change: any) {

  collectionAspect.value = change.new;
  if (collectionAspect.value) {
    listAspectRatio(selectedSiteId.value, collectionAspect.value);
  }
}

async function deleteRatio(item_id: any) {
  let aj = new (ajax as any)();
  let data =
  {
    action: "delete",
    itemId: item_id,
  };
  let result = await aj.post("/aspectRatio.php", data);
  console.log('success list', result);
  if (result.status == 200) {
    success.value = result.data.message;
    listAspectRatio(selectedSiteId.value, collectionAspect.value ? collectionAspect.value : selectedCollectionId.value);
  }
}

let disableOption = ref(false);
const checkEdited = (editedCount: any) => {
  console.log('edited count', editedCount);
  if (editedCount > 0) {
    disableOption.value = true;
  } else {
    disableOption.value = false;
  }
}


</script>

<template>
  <div class="dark ">
    <div class=" mx-auto py-5 px-10 min-h-screen bg-gray-900">
      <div class="" v-if="!manageUsers">
        <div class="flex items-end">

          <div class=" -mt-4">
            <img class="w-[60px] px-2" :src="imageUrl" />
          </div>

          <!-- <button
            class="focus:outline-none text-white bg-blue-700  hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 mr-2"
            type="button" @click="openModal" v-if="selectedSiteId && selectedCollectionId">Settings
          </button> -->

          <span>
            <Modal style="z-index: 99999;" :key="renderKey" :isVisible="userStoreObj.aspectRatioModal" @close="closeModal"
              :class="modalClass">
              <template v-slot:header>
                <h3 class="mb-4 text-s font-medium text-gray-900 dark:text-white">
                  {{
                    sites.find(item => item.value === selectedSiteId)
                    ?
                    'Aspect Ratio For ' + selectedCollectionId + "--" + sites.find(item => item.value
                      === selectedSiteId).label
                    : ''
                  }}
                </h3>
              </template>
              <template v-slot:modal_content>
                <p v-if="success" class="text-xl font-medium text-green-600 dark:text-green mb-1">{{ success }}</p>

                <Form @save="saveAspectRatio">
                  <slot name="fields">

                  </slot>
                  <template #fields="{ refs }">
                    <!-- <Select :key="renderDrodpown" name="collection_id" label="Select Collection"
                      :value="collectionAspect ? collectionAspect : selectedCollectionId" :options="collections"
                      :required="true" :ref="(ele: any) => { refs.push(ele) }" error_message="Please select collection"
                      @change="getCollectionId">
                    </Select> -->
                    <div class="text-white"> <label> Enter Aspect Ratio </label></div>
                    <div class="flex">
                      <Text name="width" placeholder="Enter Numeric value" error_message="Width is required"
                        :required="true" :ref="(ele: any) => { refs.push(ele) }">
                      </Text>
                      <span class="text-white m-2">:</span>
                      <Text name="height" placeholder="Enter Numeric value" error_message="Height is required"
                        :required="true" :ref="(ele: any) => { refs.push(ele) }">
                      </Text>
                    </div>

                  </template>
                  <template #svg>
                    <p></p>
                  </template>
                </Form>

                <AspectRatio class="mt-2" :items="savedRatios" :columns="ratioColumns" @delete-ratio="deleteRatio">
                </AspectRatio>

              </template>
            </Modal>
          </span>


          <SelectDropdown :options="sites" name="site" label="Select Site" @change="siteCollection" class="w-1/4"
            :disableOption="disableOption">
          </SelectDropdown>

          <SelectDropdown :options="collections" name="collections" @change="collectionChangeHandler"
            label="Select Collection" class="w-1/4 ml-2" :disableOption="disableOption">
          </SelectDropdown>

          <button
            class="hidden focus:outline-none text-white bg-blue-700  hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 mr-2 ml-4"
            type="button" @click="openModal" v-if="selectedSiteId && selectedCollectionId">Settings
          </button>

          <div id="columnsDropdown" class="w-1/2 ml-2"></div>

          <button class=" ml-2  right-0 bg-blue-500 text-white px-4 py-2 mb-2 rounded-md" @click="manageUsers = true">Users</button>

        </div>

        <CollectionEditor :selectedSiteId=selectedSiteId :selectedCollectionId=selectedCollectionId
          @isEdited="checkEdited"></CollectionEditor>
      </div>
      <div v-else>
        <ManageUsers @manageCollections="manageUsers = false">

        </ManageUsers>
      </div>
    </div>
  </div>
</template>
