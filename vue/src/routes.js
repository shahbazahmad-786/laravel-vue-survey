import { createRouter, createWebHistory } from "vue-router";
import store from "./store";

const routes = [
  /* <=== Index Pages ===> */
  {
    path: "/",
    name: "IndexLayout",
    redirect: "/dashboard",
    meta: { requiresAuth: true },
    component: () => import("./components/IndexLayout.vue"),
    children: [
      {
        path: "/dashboard",
        name: "Dashboard",
        component: () => import("./views/Dashboard.vue"),
      },
      {
        path: "/surveys",
        name: "Surveys",
        component: () => import("./views/Surveys.vue"),
      },
      {
        path: "/surveys/create",
        name: "SurveyCreate",
        component: () => import("./views/SurveyView.vue"),
      },
      {
        path: "/surveys/:id",
        name: "SurveyView",
        component: () => import("./views/SurveyView.vue"),
      },
    ],
  },
  {
    path: "/view/survey/:slug",
    name: "SurveyPublicView",
    component: () => import("./views/SurveyPublicView.vue"),
  },

  /* <=== Auth Pages ===> */
  {
    path: "/auth",
    name: "Auth",
    redirect: "/login",
    meta: { isGuest: true },
    component: () => import("./components/AuthLayout.vue"),
    children: [
      {
        path: "/register",
        name: "Register",
        component: () => import("./views/auth/Register.vue"),
      },
      {
        path: "/login",
        name: "Login",
        component: () => import("./views/auth/Login.vue"),
      },
    ],
  },

  /* <==> NotFound Page <==> */
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: () => import("./components/NotFound.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({ name: "Login" });
  } else if (store.state.user.token && to.meta.isGuest) {
    next({ name: "Dashboard" });
  } else {
    next();
  }
});

export default router;
