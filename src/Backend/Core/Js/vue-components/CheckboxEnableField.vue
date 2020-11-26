<template>
  <div>
    <div class="form-group">
      <div :class="[switchCheck ? 'custom-control custom-switch' : 'custom-control custom-checkbox']">
        <input type="checkbox" class="custom-control-input" :id="checkboxId" v-model="checked">
        <label class="custom-control-label" :for="checkboxId">{{checkboxLabel}}</label>
      </div>
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
      fieldName: String,
      switchCheck: Boolean
    },
    data() {
      return {
        checked: false,
        disabled: true,
        value: ''
      };
    },
    watch: {
      checked: function(newValue) {
        if (newValue === true) {
          this.disabled = false
        } else {
          this.disabled = true
        }
      }
    },
    mounted () {
      let element = document.querySelector('[data-field-value]')
      this.value = element.dataset.fieldValue
    }
  };
</script>

