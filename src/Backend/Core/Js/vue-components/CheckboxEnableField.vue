<template>
  <div>
    <div class="form-group form-check">
      <input type="checkbox" class="form-check-input" :id="checkboxId" v-model="disabled">
      <label class="form-check-label" :for="checkboxId">{{checkboxLabel}}</label>
    </div>
    <div class="form-group">
      <label :for="fieldId">{{fieldLabel}}</label>
      <input :class="['form-control', disabled && 'disabled']" :id="fieldId" :disabled="disabled" :name="fieldName" v-model="value">
    </div>
  </div>
</template>

<script>
  export default {
    name: 'checkbox-enable-field',
    props: {
      checkboxId: String,
      checkboxLabel: String,
      fieldId: String,
      fieldLabel: String,
      fieldName: String
    },
    data() {
      return {
        disabled: true,
        value: ''
      };
    },
    mounted () {
      let element = document.querySelector('[data-field-value]')
      console.log(element)
      this.value = element.dataset.fieldValue
    }
  };
</script>

