<template>
  <!-- Question index -->
  <div class="row">
    <div class="col-md-6">
      <h5>{{ index + 1 }}. {{ model.question }}</h5>
    </div>
    <div class="col-md-6">
      <div class="float-end">
        <!-- Add new question -->
        <button
          @click="addQuestion()"
          type="button"
          class="btn btn-dark btn-sm rounded-1 py-0 me-2"
        >
          <font-awesome-icon :icon="['fas', 'fa-plus']" />
          Add
        </button>
        <!--/ Add new question -->

        <!-- Delete question -->
        <button
          @click="deleteQuestion()"
          type="button"
          class="btn btn-outline-danger btn-sm rounded-1 py-0"
        >
          <font-awesome-icon :icon="['fas', 'fa-trash']" />
          Delete
        </button>
        <!-- Delete question -->
      </div>
    </div>
  </div>
  <!--/ Question index -->

  <div class="row my-3">
    <!-- Question Text -->
    <div class="col-md-9">
      <label :for="`question_text_${model.data}`" class="form-label">Question Text</label>
      <input
        v-model="model.question"
        type="text"
        class="form-control"
        @change="dataChange"
        :id="`question_text_${model.data}`"
      />
    </div>
    <!--/ Question Text -->

    <!-- Question Type -->
    <div class="col-md-3">
      <label for="type" class="form-label">Select Question Type</label>
      <select
        v-model="model.type"
        @change="typeChange"
        class="form-select form-select-md"
        id="type"
      >
        <option v-for="type in questionTypes" :key="type" :value="type">
          {{ upperCaseFirst(type) }}
        </option>
      </select>
    </div>
    <!--/ Question Type -->
  </div>

  <!-- Question Description -->
  <div class="row">
    <div class="col-md-12">
      <label :for="`question_description_${model.id}`" class="form-label">
        Question Description
      </label>
      <textarea
        v-model="model.description"
        @change="dataChange"
        class="form-control"
        rows="2"
        :id="`question_description_${model.id}`"
      />
    </div>
  </div>
  <!--/ Question Description -->

  <!-- Data for Options -->
  <div v-if="shouldHaveOptions()">
    <div class="row my-3">
      <div class="col-md-6">Options</div>
      <div class="col-md-6">
        <button
          @click="addOption()"
          type="button"
          class="btn btn-dark btn-sm rounded-1 py-0 me-2 float-end"
        >
          <font-awesome-icon :icon="['fas', 'fa-plus']" />
          Add Option
        </button>
      </div>
    </div>
    <div class="row">
      <div v-if="!model.data.options.length" class="col-12 text-center">
        You don't have any questions created
      </div>
    </div>
    <div class="row">
      <!-- Options -->
      <div class="col-12 mt-2" v-for="(option, index) in model.data.options" :key="option.uuid">
        <b>{{ index + 1 }}. &nbsp;</b>
        <input
          type="text"
          tabindex="1"
          v-model="option.text"
          @change="dataChange"
          class="form-control"
          style="display: inline; width: 95%"
        />
        <button
          @click="removeOption(option)"
          type="button"
          class="btn text-danger btn-sm rounded-1 py-0"
        >
          <font-awesome-icon :icon="['fas', 'fa-trash']" />
        </button>
      </div>
      <!--/ Options -->
    </div>
  </div>
  <!--/ Data for Options -->

  <hr class="my-4" />
</template>

<script setup>
import { v4 as uuidv4 } from "uuid";
import { computed, ref } from "vue";
import store from "../../store";

const props = defineProps({
  question: Object,
  index: Number,
});

const emit = defineEmits(["change", "addQuestion", "deleteQuestion"]);

/* For String First Upper Case Letter */
const upperCaseFirst = (str) => {
  return str.charAt(0).toUpperCase() + str.slice(1);
};

/* Re-create the whole question data to avoid unintentional reference change */
const model = ref(JSON.parse(JSON.stringify(props.question)));

/* Get question types from vuex */
const questionTypes = computed(() => store.state.questionTypes);

/* Get and Set Options */
const getOptions = () => {
  return model.value.data.options;
};
const setOptions = (options) => {
  model.value.data.options = options;
};

/* Check if the question should have options */
const shouldHaveOptions = () => {
  return ["select", "radio", "checkbox"].includes(model.value.type);
};

/* Add Options */
const addOption = () => {
  setOptions([...getOptions(), { uuid: uuidv4(), text: "" }]);
  dataChange();
};

/* Remove Options */
const removeOption = (op) => {
  setOptions(getOptions().filter((opt) => opt !== op));
  dataChange();
};

/* Type Change */
const typeChange = () => {
  if (shouldHaveOptions()) {
    setOptions(getOptions() || []);
  }
  dataChange();
};

/* Emit the data change */
const dataChange = () => {
  const data = model.value;
  if (!shouldHaveOptions()) {
    delete data.data.options;
  }

  emit("change", data);
};

/* Add Question */
const addQuestion = () => {
  emit("addQuestion", props.index + 1);
};

/* Delete Question */
const deleteQuestion = () => {
  emit("deleteQuestion", props.question);
};
</script>
