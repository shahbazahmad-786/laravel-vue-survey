<!-- ### Display All Surveys Page  ### -->
<template>
  <PageLayout>
    <template #heading>Surveys</template>
    <template #content>
      <!-- Loading Component -->
      <Loading v-if="surveys.loading" />
      <!--/ Loading Component -->

      <template v-else-if="surveys.data.length">
        <!-- Display All Surveys Component -->
        <SurveyListItem
          v-for="survey in surveys.data"
          :key="survey.id"
          :survey="survey"
          @delete="deleteSurvey(survey)"
        />
        <!--/ Display All Surveys Component -->

        <!-- Pagination -->
        <div class="row">
          <div class="col-12">
            <nav class="pagination-nav" aria-label="Page navigation">
              <ul class="pagination">
                <li
                  v-for="(link, index) of surveys.links"
                  :key="index"
                  class="page-item"
                  aria-current="page"
                >
                  <!-- (!) [index === 0] = previous button coding,
                   [index === surveys.links.length - 1] = previous button coding -->
                  <a
                    v-html="link.label"
                    :disabled="!link.url"
                    @click.prevent="getForPage(link)"
                    :class="[
                      link.active ? 'active-link' : '',
                      index === 0 ? '' : '',
                      index === surveys.links.length - 1 ? '' : '',
                    ]"
                    href="#"
                    class="page-link"
                  >
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!--/ Pagination -->
      </template>

      <div v-else class="text-center bold">Your don't have surveys yet</div>
    </template>
  </PageLayout>
</template>

<script setup>
import { computed } from "vue";
import PageLayout from "../components/PageLayout.vue";
import SurveyListItem from "../components/SurveyListItem.vue";
import Loading from "../components/Loading.vue";
import store from "../store";

/* Display All Surveys */
const surveys = computed(() => store.state.surveys);

/* Call the Function for Display All Surveys */
store.dispatch("getSurveys");

/* Delete Survey */
const deleteSurvey = (survey) => {
  if (confirm(`Are you sure you want to delete this survey? Operation can't be undone!!`)) {
    store.dispatch("deleteSurvey", survey.id).then(() => {
      /* For Notification */
      store.commit("notify", {
        type: "success",
        message: "The survey was successfully deleted.",
      });
      /* Data display after deletion */
      store.dispatch("getSurveys");
    });
  }
};

/* For Pagination Click Events */
const getForPage = (link) => {
  if (!link.url || link.active) {
    return;
  }

  /* Call the Function for Display Pagination Surveys */
  store.dispatch("getSurveys", { url: link.url });
};
</script>

<style scoped>
/* Pagination Styling */
.pagination-nav {
  width: 50%;
  margin: auto;
  display: flex;
  justify-content: center;

  .page-link {
    color: var(--app-color);
  }

  .active-link {
    color: #ffffff;
    background-color: var(--app-color) !important;
  }
}
</style>
