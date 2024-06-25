import { createStore } from "vuex";
import axiosClient from "../axios";

const store = createStore({
  state: {
    /* Auth State */
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },

    /* Dashboard State */
    dashboard: {
      loading: false,
      data: {},
    },

    /* Surveys States */
    questionTypes: ["text", "select", "radio", "checkbox", "textarea"],
    surveys: {
      data: [],
      links: [],
      loading: false,
    },
    currentSurvey: {
      data: {},
      loading: false,
    },

    /* Notification State */
    notification: {
      show: false,
      type: "",
      message: "",
    },
  },
  getters: {},
  actions: {
    /* Auth Actions */
    register({ commit }, user) {
      return axiosClient.post("/register", user).then(({ data }) => {
        commit("setUser", data.user);
        commit("setToken", data.token);
        return data;
      });
    },
    login({ commit }, user) {
      return axiosClient.post("/login", user).then(({ data }) => {
        commit("setUser", data.user);
        commit("setToken", data.token);
        return data;
      });
    },
    logout({ commit }) {
      return axiosClient.post("/logout").then((response) => {
        commit("logout");
        return response;
      });
    },
    getUser({ commit }) {
      return axiosClient.get("/user").then((res) => {
        console.log(res);
        commit("setUser", res.data);
      });
    },

    /* Dashboard Action */
    getDashboardData({ commit }) {
      commit("dashboardLoading", true);
      return axiosClient
        .get("/dashboard")
        .then((res) => {
          commit("dashboardLoading", false);
          commit("setDashboardData", res.data);
          return res;
        })
        .catch((error) => {
          commit("dashboardLoading", false);
          return error;
        });
    },

    /* Surveys Actions */
    saveSurvey({ commit }, survey) {
      delete survey.image_url;

      let response;
      if (survey.id) {
        response = axiosClient.put(`/survey/${survey.id}`, survey).then((res) => {
          commit("setCurrentSurvey", res.data);
          return res;
        });
      } else {
        response = axiosClient.post("/survey", survey).then((res) => {
          commit("setCurrentSurvey", res.data);
          return res;
        });
      }

      return response;
    },
    getSurvey({ commit }, id) {
      commit("setCurrentSurveyLoading", true);
      return axiosClient
        .get(`/survey/${id}`)
        .then((res) => {
          commit("setCurrentSurvey", res.data);
          commit("setCurrentSurveyLoading", false);
          return res;
        })
        .catch((err) => {
          commit("setCurrentSurveyLoading", false);
          throw err;
        });
    },
    getSurveys({ commit }, { url = null } = {}) {
      commit("setSurveysLoading", true);
      url = url || "/survey";
      return axiosClient.get(url).then((res) => {
        commit("setSurveysLoading", false);
        commit("setSurveys", res.data);
        return res;
      });
    },
    getSurveyBySlug({ commit }, slug) {
      commit("setCurrentSurveyLoading", true);
      return axiosClient
        .get(`/survey-by-slug/${slug}`)
        .then((res) => {
          commit("setCurrentSurvey", res.data);
          commit("setCurrentSurveyLoading", false);
          return res;
        })
        .catch((err) => {
          commit("setCurrentSurveyLoading", false);
          throw err;
        });
    },
    deleteSurvey({ commit }, id) {
      return axiosClient.delete(`/survey/${id}`).then((res) => {
        return res;
      });
    },
    saveSurveyAnswer({ commit }, { surveyId, answers }) {
      return axiosClient.post(`/survey/${surveyId}/answer`, { answers });
    },
  },
  mutations: {
    /* Auth Mutations */
    setUser: (state, user) => {
      state.user.data = user;
    },
    setToken: (state, token) => {
      state.user.token = token;
      sessionStorage.setItem("TOKEN", token);
    },
    logout: (state) => {
      state.user.data = {};
      state.user.token = null;
      sessionStorage.removeItem("TOKEN");
    },

    /* Dashboard Mutations */
    setDashboardData: (state, data) => {
      state.dashboard.data = data;
    },
    dashboardLoading: (state, loading) => {
      state.dashboard.loading = loading;
    },

    /* Surveys Mutations */
    setSurveys: (state, surveys) => {
      state.surveys.links = surveys.meta.links;
      state.surveys.data = surveys.data;
    },
    setSurveysLoading: (state, loading) => {
      state.surveys.loading = loading;
    },
    setCurrentSurvey: (state, survey) => {
      state.currentSurvey.data = survey.data;
    },
    setCurrentSurveyLoading: (state, loading) => {
      state.currentSurvey.loading = loading;
    },

    /* Notification Mutations */
    notify: (state, { message, type }) => {
      state.notification.show = true;
      state.notification.type = type;
      state.notification.message = message;
      setTimeout(() => {
        state.notification.show = false;
      }, 3000);
    },
  },
  modules: {},
});

export default store;
