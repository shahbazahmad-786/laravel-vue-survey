<!-- ### Survey Create or Update & Display Single Survey Page ### -->
<template>
  <!-- Loading Component -->
  <Loading v-if="surveyLoading" class="mt-5" />
  <!--/ Loading Component -->

  <PageLayout v-else>
    <template #heading>
      <!-- Create Survey Heading or Survey Title -->
      {{ route.params.id ? model.title : "Create a Survey" }}
      <!--/ Create Survey Heading or Survey Title -->

      <div class="float-end">
        <!-- View Public Link -->
        <a
          :href="`/view/survey/${model.slug}`"
          target="_blank"
          v-if="model.slug"
          class="btn bg-app text-light rounded-1 me-2"
        >
          <font-awesome-icon :icon="['fas', 'fa-eye']" />
          View Public Link
        </a>
        <!--/ View Public Link -->

        <!-- Delete Survey -->
        <button v-if="route.params.id" @click="deleteSurvey()" class="btn btn-danger rounded-1">
          <font-awesome-icon :icon="['fas', 'fa-trash']" />
          <!-- Delete -->
        </button>
        <!--/ Delete Survey -->
      </div>
    </template>

    <template #content>
      <div class="col-md-12">
        <div class="bg-light p-4">
          <!-- Submit Form -->
          <form @submit.prevent="saveSurvey">
            <div class="mb-3 row">
              <div class="col-12">
                <!-- File Upload -->
                <img v-if="model.image_url" :src="model.image_url" width="100" alt="file" />
                <font-awesome-icon
                  v-else
                  :icon="['fas', 'fa-image']"
                  style="font-size: 7rem; color: #dee2e6"
                />
                <input @change="onImageChoose" type="file" id="file" style="display: none" />
                <div>
                  <label
                    for="file"
                    class="btn btn-light mt-2"
                    style="border: 1px solid #dee2e6; color: gray"
                  >
                    Change File
                  </label>
                </div>
                <!--/ File Upload -->
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-6">
                <label for="title" class="form-label">Title</label>
                <input v-model="model.title" type="text" class="form-control" id="title" />
              </div>
              <div class="col-6">
                <label for="date" class="form-label">Expire Date</label>
                <input v-model="model.expire_date" type="date" class="form-control" id="date" />
              </div>
            </div>
            <hr />
            <div class="mb-3 row">
              <div class="col-12">
                <label for="dsc" class="form-label">Description</label>
                <textarea v-model="model.description" class="form-control" rows="2" id="dsc" />
              </div>
            </div>
            <div class="form-check">
              <input v-model="model.status" class="form-check-input" type="checkbox" id="active" />
              <label class="form-check-label" for="active">Active</label>
            </div>
            <hr />
            <!-- Question Index -->
            <div class="mb-3 row">
              <div class="col-6">
                <h4>Questions :</h4>
              </div>
              <div class="col-6">
                <button
                  @click="addQuestion()"
                  type="button"
                  class="btn btn-dark float-end rounded-1"
                >
                  <font-awesome-icon :icon="['fas', 'fa-plus']" />
                  Add Question
                </button>
              </div>
            </div>
            <div class="mb-3 row">
              <div v-if="!model.questions.length" class="col-12 text-center">
                You don't have any questions created
              </div>
              <!-- Question Editor Component -->
              <div v-for="(question, index) in model.questions" :key="question.id">
                <QuestionEditor
                  :question="question"
                  :index="index"
                  @change="questionChange"
                  @addQuestion="addQuestion"
                  @deleteQuestion="deleteQuestion"
                />
              </div>
              <!--/ Question Editor Component -->
            </div>
            <!--/ Question Index -->
            <div class="row">
              <div class="col-sm-12">
                <button type="submit" class="btn bg-app text-light">Save Survey</button>
              </div>
            </div>
          </form>
          <!--/ Submit Form -->
        </div>
      </div>
    </template>
  </PageLayout>
</template>

<script setup>
import { v4 as uuidv4 } from "uuid";
import { computed, ref, watch } from "vue";
import PageLayout from "../components/PageLayout.vue";
import Loading from "../components/Loading.vue";
import QuestionEditor from "../components/editor/QuestionEditor.vue";
import { useRoute, useRouter } from "vue-router";
import store from "../store";

const router = useRouter();
const route = useRoute();

/* Form's V-Models */
let model = ref({
  title: "",
  slug: "",
  status: false,
  description: null,
  image: null,
  image_url: null,
  expire_date: null,
  questions: [],
});

/* Get survey loading state, which only changes when we fetch survey from backend */
const surveyLoading = computed(() => store.state.currentSurvey.loading);

/* Watch to current survey data change and when this happens we update local model */
watch(
  () => store.state.currentSurvey.data,
  (newVal, oldVal) => {
    model.value = {
      ...JSON.parse(JSON.stringify(newVal)),
      // status: !!newVal.state,
    };
  }
);

/* If the current component is rendered on survey update route we make a request to fetch survey */
if (route.params.id) {
  store.dispatch("getSurvey", route.params.id);
}

/* Add Questions */
const addQuestion = (index) => {
  const newQuestion = {
    id: uuidv4(),
    type: "text",
    question: "",
    description: null,
    data: {},
  };

  model.value.questions.splice(index, 0, newQuestion);
};

/* Delete Questions */
const deleteQuestion = (question) => {
  model.value.questions = model.value.questions.filter((q) => q !== question);
};

/* Questions Change */
const questionChange = (question) => {
  // Important to explicitelly assign question.data.options, because otherwise it is a Proxy object
  // and it is lost in JSON.stringify()
  if (question.data.options) {
    question.data.options = [...question.data.options];
  }

  model.value.questions = model.value.questions.map((q) => {
    if (q.id === question.id) {
      return JSON.parse(JSON.stringify(question));
    }
    return q;
  });
};

/* File Change */
function onImageChoose(e) {
  const file = e.target.files[0];

  const reader = new FileReader();
  reader.onload = () => {
    // The field to send on backend and apply validations
    model.value.image = reader.result;

    // The field to display here
    model.value.image_url = reader.result;
    e.target.value = "";
  };
  reader.readAsDataURL(file);
}

/* Create or Update Survey */
const saveSurvey = () => {
  let action = "created";
  if (model.value.id) {
    action = "updated";
  }
  store.dispatch("saveSurvey", { ...model.value }).then(({ data }) => {
    /* For Notification */
    store.commit("notify", {
      type: "success",
      message: `The survey was successfully ${action}.`,
    });

    router.push({
      name: "SurveyView",
      params: { id: data.data.id },
    });
  });
};

/* Delete Survey */
const deleteSurvey = () => {
  if (confirm(`Are you sure you want to delete this survey? Operation can't be undone!!`)) {
    store.dispatch("deleteSurvey", model.value.id).then(() => {
      /* For Notification */
      store.commit("notify", {
        type: "success",
        message: "The survey was successfully deleted.",
      });

      router.push({ name: "Surveys" });
    });
  }
};
</script>

<style scoped>
.form-check-input:checked {
  background-color: var(--app-color);
  border-color: var(--app-color);
}
</style>
