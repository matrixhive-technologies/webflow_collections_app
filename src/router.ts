import { createRouter, createWebHistory } from 'vue-router'

import IndexPage from '@/pages/IndexPage.vue'


const routes = [
  {
    path: '/',
    component: IndexPage,
    meta: {
      title: 'Site Editor site map page',
    },
  },


]

const router = createRouter({
  history: createWebHistory('/localhost/projects/webflow_applications/webflow_collections_ui/dist'),
  routes,
})

export default router
