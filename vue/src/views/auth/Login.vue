<template>
    <div class="text-center mb-3">
        <h2 class="bold">Login Form</h2>
        <div>
            Or
            <router-link :to="{ name: 'Register' }" class="text-decoration-none text-app">
                create to your account
            </router-link>
        </div>
    </div>

    <!-- Alert Component -->
    <Alert v-if="errorMsg">
        <template #button>
            <button @click.prevent="errorMsg = ''" type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </template>
        <template #message>
            {{ errorMsg }}
        </template>
    </Alert>
    <!--/ Alert Component -->

    <!-- Login Form -->
    <form @submit.prevent="login">
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
        <div class="my-3 row">
            <div class="col-12">
                <div class="form-check">
                    <input v-model="user.remember" class="form-check-input" type="checkbox" id="remember" />
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button :disabled="loading" type="submit" :class="{ 'auth-button-loading': loading }"
                class="bg-app text-center text-light py-1 rounded auth-button pointer">
                <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <font-awesome-icon v-else :icon="['fas', 'fa-lock']" class="me-1" />
                Login
            </button>
        </div>
    </form>
    <!--/ Login Form -->
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import Alert from '../../components/Alert.vue';
import store from "../../store";

const user = {
    email: "",
    password: "",
};

const router = useRouter();

let loading = ref(false);
const errorMsg = ref("");

/* Login */
const login = () => {
    /* Show Button Loading */
    loading.value = true;
    store
        .dispatch("login", user)
        .then(() => {
            /* Show Notification */
            store.commit("notify", {
                type: "success",
                message: `You, Successfully Logged In.`,
            });

            /* Hide Button Loading */
            loading.value = false;
            router.push({ name: "Dashboard" });
        })
        .catch((err) => {
            /* Hide Button Loading */
            loading.value = false;
            errorMsg.value = err.response.data.error;
        });
};
</script>