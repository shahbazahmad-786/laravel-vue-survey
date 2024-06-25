<template>
  <div class="text-center mb-3">
    <h2 style="font-weight: bold">Registration Form</h2>
    <div>
      Or
      <router-link :to="{ name: 'Login' }" class="text-decoration-none text-app"> login to your account </router-link>
    </div>
  </div>

  <!-- Alert Component -->
  <Alert v-if="Object.keys(errors).length">
    <template #button>
      <button @click.prevent="errors = {}" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </template>
    <template #message>
      <div v-for="(field, i) of Object.keys(errors)" :key="i">
        <div v-for="(error, ind) of errors[field] || []" :key="ind">
          * {{ error }}
        </div>
      </div>
    </template>
  </Alert>
  <!--/ Alert Component -->

  <!-- Register Form -->
  <form @submit.prevent="register">
    <div class="mb-1 row">
      <div class="col-12">
        <input v-model="user.name" type="text" class="form-control" placeholder="Name" />
      </div>
    </div>
    <div class="mb-1 row">
      <div class="col-12">
        <input v-model="user.email" type="email" class="form-control" placeholder="Email" />
      </div>
    </div>
    <div class="mb-1 row">
      <div class="col-12">
        <input v-model="user.password" type="password" class="form-control" placeholder="Password" />
      </div>
    </div>
    <div class="mb-1 row">
      <div class="col-12">
        <input v-model="user.password_confirmation" type="password" class="form-control" placeholder="Confirm Password" />
      </div>
    </div>
    <div class="d-grid gap-2 mt-3">
      <button :disabled="loading" type="submit" :class="{ 'auth-button-loading': loading }"
        class="bg-app text-center text-light py-1 rounded auth-button pointer">
        <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        <font-awesome-icon v-else :icon="['fas', 'fa-lock']" class="me-1" />
        Register
      </button>
    </div>
  </form>
  <!--/ Register Form -->
</template>

<script setup>
import { ref } from "vue";
import Alert from '../../components/Alert.vue';
import store from "../../store";
import { useRouter } from "vue-router";

const user = {
  name: "",
  email: "",
  password: "",
};

const router = useRouter();

let loading = ref(false);
const errors = ref({});

/* Register */
const register = () => {
  /* Show Button Loading */
  loading.value = true;
  store
    .dispatch("register", user)
    .then(() => {
      /* Show Notification */
      store.commit("notify", {
        type: "success",
        message: `You, Successfully Registered.`,
      });

      /* Hide Button Loading */
      loading.value = false;
      router.push({ name: "Dashboard" });
    })
    .catch((error) => {
      /* Hide Button Loading */
      loading.value = false;

      if (error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
    });
};
</script>

