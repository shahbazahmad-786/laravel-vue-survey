<template>
  <div class="container" style="margin-top: 140px">
    <div class="row">
      <div class="col">
        <div class="row align-items-md-stretch">
          <div class="col-md-12">
            <div class="h-100 p-5 text-white bg-app border rounded-3">
              <h2>404 | Page Not Found</h2>
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Eos praesentium nobis sit
                corrupti similique tempore et alias minima, minus, enim labore impedit, adipisci
                dicta at veniam nam fuga suscipit blanditiis?
              </p>
              <RouterLink to="/" class="btn btn-outline-light"> Back to Home </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";
</script>
