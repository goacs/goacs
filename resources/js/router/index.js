import Vue from 'vue'
import VueRouter from 'vue-router'
import Base from "@/views/Base";
import LoginBase from "@/views/LoginBase";

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    component: Base,
    children: [
      {
        path: '',
        redirect: 'dashboard'
      },
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/views/home/Dashboard.vue')
      },
      {
        path: 'devices',
        name: 'devices-list',
        component: () => import('@/views/device/DeviceList.vue')
      },
      {
        path: 'devices/:id',
        name: 'devices-view',
        component: () => import('@/views/device/DeviceView.vue')
      },

      {
        path: 'templates',
        name: 'template-list',
        component: () => import('@/views/template/TemplateList.vue')
      },
      {
        path: 'templates/:id',
        name: 'template-view',
        component: () => import('@/views/template/TemplateView.vue')
      },
      {
        path: 'files',
        name: 'file-list',
        component: () => import('@/views/files/FileList.vue')
      },
      {
        path: 'settings',
        name: 'settings',
        component: () => import('@/views/settings/SettingsView.vue'),
        children: [
          {
            path: '',
            name: 'settings-main',
            component: () => import('@/views/settings/ConfigurationView.vue'),
          },
          {
            path: 'tasks',
            name: 'settings-tasks',
            component: () => import('@/views/settings/tasks/TasksList.vue'),
          },
        ]
      }
    ],
    meta: {
      auth: true,
    },
  },
  {
    path: '/auth',
    name: 'auth',
    component: LoginBase,
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/views/auth/Login.vue')
      },
      {
        path: 'logout',
        name: 'logout',
        component: () => import('@/views/auth/Logout.vue')
      }
    ]

  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

Vue.router = router;

export default router
