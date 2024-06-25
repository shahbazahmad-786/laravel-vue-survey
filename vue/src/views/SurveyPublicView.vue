<!-- ### Display Survey Public Page ### -->
<template>
  <div class="container my-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card text-dark bg-light shadow">
          <div class="card-body">
            <h2 class="card-title text-center bg-app text-light py-2 rounded">
              Survey Public Answers
            </h2>
            <p class="card-text">
              <!-- Loading Component -->
              <Loading v-if="loading" class="pt-2" />
              <!--/ Loading Component -->

              <!-- Submit Form -->
            <form v-else @submit.prevent="submitSurvey">
              <!-- Show some data about Survey -->
              <div class="row">
                <div class="col-md-2">
                  <img :src="survey.image_url || 'http://localhost:5000/src/assets/default.png'" :alt="survey.title"
                    class="p-3" style="max-width: 100%;">
                </div>
                <div class="col-md-10">
                  <h3 class="pt-2">{{ survey.title }}</h3>
                  <p v-html="survey.description"></p>
                </div>
              </div>
              <!--/ Show some data about Survey -->

              <hr />

              <!-- Submit Another Response -->
              <div v-if="surveyFinished" class="row">
                <div class="col-md-6 mx-auto bg-app text-center text-light rounded py-3">
                  <h4>Thank you 😍 for participating in this survey.</h4>
                  <button @click="submitAnotherResponse" type="button" class="btn btn-outline-light mt-2">
                    Submit Another Response
                  </button>
                </div>
              </div>
              <!--/ Submit Another Response -->

              <div v-else class="row">
                <!-- Survey Questions Viewer Component -->
                <div v-for="(question, ind) of survey.questions" :key="question.id" class="col-md-12">
                  <QuestionViewer v-model="answers[question.id]" :question="question" :index="ind" />
                </div>
                <!--/ Survey Questions Viewer Component -->

                <div class="col-12">
                  <button type="submit" class="btn bg-app text-light">
                    Submit Answers
                  </button>
                </div>
              </div>
            </form>
            <!--/ Submit Form -->
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useRoute } from "vue-router";
import Loading from '../components/Loading.vue';
import QuestionViewer from '../components/viewer/QuestionViewer.vue';
import store from "../store";

const route = useRoute();

/* V-Model */
const answers = ref({});

/* Show after Form Submit */
const surveyFinished = ref(false);

/* Display Survey Data by Slug */
const survey = computed(() => store.state.currentSurvey.data);
/* Display Loading */
const loading = computed(() => store.state.currentSurvey.loading);

/* Call Function for Get Survey By Slug  */
store.dispatch("getSurveyBySlug", route.params.slug);

/* Submit Answers Survey */
const submitSurvey = () => {
  console.log(JSON.stringify(answers.value, undefined, 2));
  store
    .dispatch("saveSurveyAnswer", {
      surveyId: survey.value.id,
      answers: answers.value,
    })
    .then((response) => {
      if (response.status === 201) {
        surveyFinished.value = true;
      }
    });
};

/* Submit Another Response */
const submitAnotherResponse = () => {
  answers.value = {};
  surveyFinished.value = false;
};
</script>
