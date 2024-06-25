<template>
  <fieldset>
    <!-- Question Title & Description -->
    <legend style="font-weight: 500">{{ `${index + 1}. ${question.question}` }}</legend>
    <p style="color: #404040">{{ question.description }}</p>
    <!--/ Question Title & Description -->

    <!-- Select Question Type -->
    <div v-if="question.type === 'select'">
      <div class="mb-3">
        <select
          :value="modelValue"
          @change="emits('update:modelValue', $event.target.value)"
          class="form-select form-select-md"
        >
          <option value="">Please Select One</option>
          <option v-for="option in question.data.options" :key="option.uuid" :value="option.text">
            {{ option.text }}
          </option>
        </select>
      </div>
    </div>
    <!--/ Select Question Type -->

    <!-- Radio Question Type -->
    <div v-else-if="question.type === 'radio'">
      <div v-for="(option, ind) of question.data.options" :key="option.uuid" class="form-check">
        <input
          :id="option.uuid"
          :name="'question' + question.id"
          :value="option.text"
          @change="emits('update:modelValue', $event.target.value)"
          type="radio"
          class="form-check-input"
        />
        <label :for="option.uuid" class="form-check-label">
          {{ option.text }}
        </label>
      </div>
    </div>
    <!--/ Radio Question Type -->

    <!-- Checkbox Question Type -->
    <div v-else-if="question.type === 'checkbox'">
      <div v-for="(option, ind) of question.data.options" :key="option.uuid" class="form-check">
        <input
          :id="option.uuid"
          v-model="model[option.text]"
          @change="onCheckboxChange"
          type="checkbox"
          class="form-check-input"
        />
        <label :for="option.uuid" class="form-check-label">
          {{ option.text }}
        </label>
      </div>
    </div>
    <!--/ Checkbox Question Type -->

    <!-- Text Question Type -->
    <div v-else-if="question.type === 'text'">
      <div class="mb-3">
        <input
          :value="modelValue"
          @input="emits('update:modelValue', $event.target.value)"
          type="text"
          placeholder="Write the answer in short words..."
          class="form-control"
        />
      </div>
    </div>
    <!--/ Text Question Type -->

    <!-- Textarea Question Type -->
    <div v-else-if="question.type === 'textarea'">
      <div class="mb-3">
        <textarea
          :value="modelValue"
          @input="emits('update:modelValue', $event.target.value)"
          rows="3"
          placeholder="Write the answer in details..."
          class="form-control"
        />
      </div>
    </div>
    <!--/ Textarea Question Type -->
  </fieldset>
  <hr class="py-2" />
</template>

<script setup>
import { ref } from "vue";

/* Define Props */
const { question, index, modelValue } = defineProps({
  question: Object,
  index: Number,
  modelValue: [String, Array],
});

/* Define Emits */
const emits = defineEmits(["update:modelValue"]);

/* Options of Questions */
const shouldHaveOptions = () => {
  return ["select", "radio", "checkbox"].includes(question.type);
};

/* For Checkbox */
let model;
if (question.type === "checkbox") {
  model = ref({});
}

/* For Changing Checkbox */
const onCheckboxChange = ($event) => {
  const selectedOptions = [];
  for (let uuid in model.value) {
    if (model.value[uuid]) {
      selectedOptions.push(uuid);
    }
  }
  emits("update:modelValue", selectedOptions);
};
</script>
