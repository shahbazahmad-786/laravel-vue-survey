<template>
  <div class="container my-3">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-app bold">
          <slot name="heading"></slot>
        </h1>
      </div>
    </div>
    <div class="row my-3">
      <slot name="content"></slot>
    </div>
  </div>
</template>
