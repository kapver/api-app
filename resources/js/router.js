import { createRouter, createWebHistory } from "vue-router";

const routes = [
  { path: "/", component: () => import("./Pages/HomeRoute.vue") },
  { path: "/users", component: () => import("./Pages/UsersRoute.vue") },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});