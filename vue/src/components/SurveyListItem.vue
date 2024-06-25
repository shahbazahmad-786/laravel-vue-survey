<template>
  <div class="col-xlg-3 col-lg-4 col-md-6 col-sm-12 mb-4">
    <div class="card shadow">
      <img class="card-img-top p-3" :src="survey.image_url || 'http://localhost:5000/src/assets/default.png'"
        :alt="survey.title" />
      <div class="card-body">
        <h4 class="card-title">{{ survey.title }}</h4>
        <p class="card-text">{{ survey.description }}
        <div class="mt-3 btn-div">
          <a :href="`/view/survey/${survey.slug}`" target="_blank" class="app-btn shadow-sm">
            <font-awesome-icon :icon="['fas', 'fa-eye']" /> View
          </a>
          <router-link :to="{ name: 'SurveyView', params: { id: survey.id } }" type="button" class="app-btn shadow-sm">
            <font-awesome-icon :icon="['fas', 'fa-edit']" /> Edit
          </router-link>
          <a href="#" @click.prevent="emit('delete', survey)" type="button" class="app-btn shadow-sm">
            <font-awesome-icon :icon="['fas', 'fa-delete-left']" /> Delete
          </a>
        </div>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
const { survey } = defineProps({
  survey: Object
});

const emit = defineEmits('delete');
</script>

<style scoped>
.btn-div {
  display: flex;
  justify-content: space-between;
  align-items: center;

  .app-btn {
    width: 30%;
    text-align: center;
    color: var(--app-color);
    border-radius: 5px;
    text-decoration: none;
  }
}
</style>