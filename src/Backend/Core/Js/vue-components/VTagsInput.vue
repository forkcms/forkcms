<template>
  <div>
    <v-tags-input
      v-model="tag"
      :tags="tags"
      :autocomplete-items="filteredTags"
      @tags-changed="newTags => tags = newTags"
    />
    <input :id="inputId" :name="inputId" type="hidden" :value="tagsInputValue" />
  </div>
</template>

<script>
  import VTagsInput from '@johmun/vue-tags-input';
  import axios from 'axios'
  import { Data } from '../Components/Data'
  import { Messages } from '../Components/Messages'

  export default {
    components: {
      VTagsInput ,
    },
    props: {
      currentTags: {
        type: String,
        default: ''
      },
      inputId: {
        type: String,
        required: true
      }
    },
    data() {
      return {
        tag: '',
        tags: null,
        tagsInputValue: '',
        filteredTags: []
      };
    },
    watch: {
      tags (newValue) {
        let text = []

        for (const [key, value] of Object.entries(newValue)) {
          text.push(value.text)
        }
        this.tagsInputValue = text.join(',')
      },
      tag (newValue) {
        this.getFilteredTags(newValue)
      }
    },
    methods: {
      getFilteredTags (value) {
        axios.post('/backend/ajax',
          {
            fork: {
              module: 'Tags',
              action: 'GetAllTags'
            },
            filter: value
          },
          {
            timeout: 1000,
            headers: {'X-CSRF-Token': Data.get('csrf-token')}
          }
        )
        .then((response) => {
          this.filteredTags = response.data.data.map(text => ({ text }))
        })
        .catch(error => {
          console.log(error)
          Messages.add('danger', window.backend.locale.err('SomethingWentWrong'), '')
        })
      }
    },
    created() {
      this.tags = this.currentTags.length > 0 ? this.currentTags.split(',').map(text => ({ text })) : []
    }
  }
</script>
