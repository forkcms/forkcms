<template>
  <div>
    <div class="embed-responsive embed-responsive-16by9" v-if="enabled">
      <iframe
        class="embed-responsive-item"
        :src="fullUrl"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      >
      </iframe>
    </div>
    <div class="card" v-else>
      <div class="card-body">
        <p><i class="fas fa-info-circle mr-3"></i>{{notAllowedMessage}}</p>
        <a href="#" class="btn btn-primary" @click="showConsentDialog">{{allowButtonText}}</a>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name:  'v-embed',
    props: {
      id: {
        type: String,
        required: true
      },
      notAllowedMessage: String,
      allowButtonText: String,
      level: String
    },
    data () {
      return {
        url: 'https://www.youtube-nocookie.com/embed/',
        visitorChoices: jsData.privacyConsent.visitorChoices,
      }
    },
    methods: {
      showConsentDialog() {
        $('#privacyConsentDialog').modal('show')
      }
    },
    computed: {
      fullUrl() {
        return this.url + this.id
      },
      enabled () {
        if (this.level !== undefined && this.visitorChoices[this.level] !== undefined) {
          return this.visitorChoices[this.level]
        }

        return true
      }
    }
  }
</script>
