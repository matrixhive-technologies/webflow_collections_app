import { createRouter, createWebHistory } from 'vue-router'

import IndexPage from '@/pages/IndexPage.vue'

import loginVue from "@/modules/auth/login.vue"


const routes = [
  {
    path: '/',
    component: IndexPage,
    meta: {
      title: 'Webflow Collection Editor by PixlFlo',
    },
  },
  {
    path: '/login',
    name: 'login',
    component: loginVue
  }
]

const router = createRouter({
  history: createWebHistory('/localhost/projects/webflow_collections_app/dist/app'),
  routes,
})

export default router
