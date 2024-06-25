<template>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <slot name="button" />
        <strong>
            <slot name="message" />
        </strong>
    </div>
</template>

<script setup></script>