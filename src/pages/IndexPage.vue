<script setup lang="ts">
import CollectionEditor from '@/components/CollectionEditor.vue'
import { SelectDropdown } from '@/components/crud'
import ajax from "@/accessories/ajax";
import { userStore } from '@/stores/user';

// useRoute, useHead, and HelloWorld are automatically imported. See vite.config.ts for details.
const route = useRoute()

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
let sites = ref<Array<any>>([]);
let collections = ref<Array<any>>([]);

// Site Ids list
async function listOfSites() {
  try {
    let aj = new (ajax as any)();
    let data = [
      {
        endPoint: "sites",
      },

    ];

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
  let data = [
    {
      endPoint: "sites/" + selectedSiteId.value + "/collections",
      params: selectedSiteId.value,
    },
  ];

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
async function logout() {
  userStoreObj.removeUser();
  window.location.href = import.meta.env.VITE_API_URL + '/logout.php'
}
const router = useRouter()
if (userStoreObj.isLoggedIn) {
  router.push(userStoreObj.afterLoginRoute);
} else {
  userStoreObj.removeUser();
  router.push('login');
}

</script>

<template>
  <div class="container mx-auto mt-5">
    <div class="text-right">
      <button
        class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
        type="button" @click.prevent="logout()">Logout
      </button>
    </div>
    <div class="flex items-end">
      <SelectDropdown :options="sites" name="site" label="Select Site" @change="siteCollection" class="w-1/3">
      </SelectDropdown>

      <SelectDropdown :options="collections" name="collections" @change="collectionChangeHandler"
        label="Select Collection" class="w-1/3 ml-2">
      </SelectDropdown>

      <div id="columnsDropdown" class="w-1/3 ml-2"></div>

    </div>

    <CollectionEditor :selectedSiteId=selectedSiteId :selectedCollectionId=selectedCollectionId></CollectionEditor>

  </div>
</template>
