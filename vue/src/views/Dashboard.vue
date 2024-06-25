<!-- ### Dashboard or Home Page ### -->
<template>
  <PageLayout>
    <template #heading>Dashboard</template>
    <template #content>
      <!-- Loading Component -->
      <Loading v-if="loading" />
      <!--/ Loading Component -->

      <!-- Dashboard Cards -->
      <template v-else>
        <!-- Latest Survey -->
        <div class="col-xlg-3 col-lg-4 col-md-6 col-sm-12 mb-4">
          <div class="card shadow">
            <h4 class="py-3 text-center">Latest Survey</h4>
            <div v-if="data.latestSurvey">
              <img class="card-img-top px-3"
                :src="data.latestSurvey.image_url || 'http://localhost:5000/src/assets/default.png'" />
              <div class="card-body">
                <h4 class="card-title">{{ data.latestSurvey.title }}</h4>
                <p class="card-text">
                <div>
                  <span>Create Date:</span>
                  <span class="float-end">{{ data.latestSurvey.created_at }}</span>
                </div>
                <div>
                  <span>Expire Date:</span>
                  <span class="float-end">{{ data.latestSurvey.expire_date }}</span>
                </div>
                <div>
                  <span>Status:</span>
                  <span class="float-end">{{ data.latestSurvey.status ? "Active" : "Draft" }}</span>
                </div>
                <div>
                  <span>Questions:</span>
                  <span class="float-end">{{ data.latestSurvey.questions }}</span>
                </div>
                <div>
                  <span>Answers:</span>
                  <span class="float-end">{{ data.latestSurvey.answers }}</span>
                </div>
                <div class="mt-3 btn-div">
                  <router-link :to="{ name: 'SurveyView', params: { id: data.latestSurvey.id } }"
                    class="app-btn shadow-sm">
                    <font-awesome-icon :icon="['fas', 'fa-edit']" /> Edit Survey
                  </router-link>
                  <a :href="`/view/survey/${data.latestSurvey.slug}`" target="_blank" class="app-btn shadow-sm">
                    <font-awesome-icon :icon="['fas', 'fa-eye']" /> View Survey
                  </a>
                </div>
                </p>
              </div>
            </div>
            <div v-else class="text-center py-2 bold">Your don't have surveys yet</div>
          </div>
        </div>
        <!--/ Latest Survey -->

        <!-- Total Surveys & Answers -->
        <div class="col-lg-4 mb-4 text-center">
          <div class="card shadow">
            <h4 class="pt-3">Total Surveys</h4>
            <div class="card-body">
              <h1 class="card-title pb-3" style="font-size: 5rem">
                {{ data.totalSurveys }}
              </h1>
            </div>
          </div>
          <div class="card shadow mt-4">
            <h4 class="pt-3">Total Answers</h4>
            <div class="card-body">
              <h1 class="card-title pb-3" style="font-size: 5rem">
                {{ data.totalAnswers }}
              </h1>
            </div>
          </div>
        </div>
        <!--/ Total Surveys & Answers -->

        <!-- Latest 5 Answers -->
        <div class="col-lg-4 mb-4">
          <div class="card shadow">
            <h4 class="py-3 text-center">Latest Answers</h4>
            <div v-if="data.latestAnswers.length" class="card-body">
              <a v-for="answer, index of data.latestAnswers" :key="answer.id" href="#" class="text-black d-block"
                style="text-decoration: none;">
                <h6 class="card-title mb-0">
                  {{ `${++index}. ${answer.survey.title}` }}
                </h6>
                <small>
                  Answer Made at:
                  <i>{{ answer.end_date }}</i>
                </small>
                <hr />
              </a>
            </div>
            <div v-else class="text-center py-2 bold">
              Your don't have answers yet
            </div>
          </div>
        </div>
        <!--/ Latest 5 Answers -->
      </template>
      <!--/ Dashboard Cards -->

    </template>
  </PageLayout>
</template>

<script setup>
import PageLayout from "../components/PageLayout.vue";
import Loading from "../components/Loading.vue";
import { computed } from "vue";
import { useStore } from "vuex";

const store = useStore();

/* Display Loading & Dashboard Data */
const loading = computed(() => store.state.dashboard.loading);
const data = computed(() => store.state.dashboard.data);

/* Call Function to Fetch Dashboard Data */
store.dispatch("getDashboardData");
</script>

<style scoped>
.btn-div {
  display: flex;
  justify-content: space-between;
  align-items: center;

  .app-btn {
    width: 47%;
    text-align: center;
    color: var(--app-color);
    border-radius: 5px;
    text-decoration: none;
  }
}
</style>
