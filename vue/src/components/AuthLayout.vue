<template>
    <div class="container auth-main">
        <div class="row w-50">
            <div class="col-md-12">
                <div style="width: 3rem" class="mx-auto mb-3">
                    <Logo />
                </div>
                <router-view />
            </div>
        </div>
    </div>
</template>

<script setup>
import Logo from "../components/Logo.vue";
</script>



<style>
.auth-main {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.auth-button {
    border: none;
}

.auth-button:hover {
    box-shadow: 1px 2px 5px 0px grey;
}

.auth-button-loading {
    box-shadow: none !important;
    cursor: not-allowed !important;
}
</style>
