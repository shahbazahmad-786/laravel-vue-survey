<template>
  <nav class="navbar navbar-expand-sm navbar-light bg-light shadow-sm">
    <div class="container">
      <router-link :to="{ name: 'Dashboard' }" class="navbar-brand" style="width: 2rem">
        <!-- Logo Component -->
        <Logo />
        <!--/ Logo Component -->
      </router-link>
      <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavId"
        aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavId">

        <!-- Navbar Routes -->
        <ul class="navbar-nav me-auto mt-2 mt-lg-0 tabs">
          <li class="nav-item">
            <router-link :to="{ name: 'Dashboard' }" class="nav-link bold"> Dashboard </router-link>
          </li>
          <li class="nav-item">
            <router-link :to="{ name: 'Surveys' }" class="nav-link bold"> Surveys </router-link>
          </li>
          <li class="nav-item">
            <router-link :to="{ name: 'SurveyCreate' }" class="nav-link bold"> Create </router-link>
          </li>
        </ul>
        <!--/ Navbar Routes -->

        <div class="d-flex my-2 my-lg-0">
          <div class="dropdown pointer">
            <div class="form-control text-app" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">

              <!-- User Name -->
              <span class="me-2 text-capitalize">
                Hey, {{ user.name }}
              </span>
              <!--/ User Name -->

              <font-awesome-icon :icon="['fas', 'fa-user-circle']" size="lg" />
            </div>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
              <li>

                <!-- Logout Button -->
                <a class="dropdown-item" @click="logout" href="#">
                  Logout
                </a>
                <!--/ Logout Button -->

              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import Logo from "../components/Logo.vue";
import store from "../store";

const router = useRouter();

/* Show User Data */
const user = computed(() => store.state.user.data);

/* Call Function to Fetch User Data */
store.dispatch("getUser");

/* Logout */
const logout = () => {
  store
    .dispatch("logout")
    .then(() => {
      store.commit('notify', {
        message: 'You, Successfully Logged Out.',
        type: 'success'
      });

      router.push({ name: "Login" });
    })
    .catch((err) => console.log(err));
};
</script>

<style scoped>
.router-link-exact-active {
  color: var(--app-color);
}

.tabs a:hover {
  color: var(--app-color);
}
</style>
