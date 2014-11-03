module.exports = (grunt) ->

  # Project configuration
  grunt.initConfig
    pkg: grunt.file.readJSON 'package.json'
    theme_src: 'frontend/themes/<%= pkg.theme %>/src'
    theme_build: 'frontend/themes/<%= pkg.theme %>/core'
    uglify:
      options:
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      build:
        src: '<%= theme_src %>/js/lib.js'
        dest: '<%= theme_build %>/js/lib.min.js'
    coffee:
      compileJoined:
        options:
          bare: true
        files:
          '<%= theme_build %>/js/theme.js': [
            '<%= theme_src %>/coffee/theme.coffee'
          ]
    concat:
      options:
        seperator: ';'
      dist:
        src: [
          '<%= theme_src %>/js/lib/*.js'
        ]
        dest: '<%= theme_src %>/js/lib.js'
    compass:
      dist:
        options:
          config: '<%= theme_src %>/layout/config.rb'
          sassDir: '<%= theme_src %>/layout/sass'
          cssDir: '<%= theme_build %>/layout/css'
    sync:
      templates:
        files: [
          cwd: '<%= theme_src %>/layout/templates/'
          src: '**'
          dest: '<%= theme_build %>/layout/templates/'
        ]
      images:
        files: [
          cwd: '<%= theme_src %>/layout/images/'
          src: '**'
          dest: '<%= theme_build %>/layout/images/'
        ]
      fonts:
        files: [
          cwd: '<%= theme_src %>/layout/fonts/'
          src: '**'
          dest: '<%= theme_build %>/layout/fonts/'
        ]
    copy:
      templates:
        expand: true
        cwd: '<%= theme_src %>/layout/templates/'
        src: '**'
        dest: '<%= theme_build %>/layout/templates/'
      images:
        expand: true
        cwd: '<%= theme_src %>/layout/images/'
        src: '**'
        dest: '<%= theme_build %>/layout/images/'
      fonts:
        expand: true
        cwd: '<%= theme_src %>/layout/fonts/'
        src: '**'
        dest: '<%= theme_build %>/layout/fonts/'
    imagemin:
      dynamic:
        files: [
          expand: true
          cwd: '<%= theme_src %>/layout/images/'
          src: ['**/*.{png,jpg,gif}']
          dest: '<%= theme_build %>/layout/images/'
        ]
    fontgen:
      all:
        options:
          stylesheet: false
        files: [
          src: [
            '<%= theme_src %>/layout/fonts/*.ttf'
            '<%= theme_src %>/layout/fonts/*.otf'
          ]
          dest: '<%= theme_build %>/layout/fonts/'
        ]
    clean:
      templates: [
        '<%= theme_build %>/layout/templates/'
      ]
      images: [
        '<%= theme_build %>/layout/images/'
      ]
      fonts: [
        '<%= theme_build %>/layout/fonts/'
      ]
      fontsCss: [
        '<%= theme_build %>/layout/fonts/*.css'
      ]
    watch:
      #options:
      #  livereload: 80
      options:
        atBegin: true
      coffee:
        files: ['<%= theme_src %>/coffee/*']
        tasks: ['coffee']
      lib:
        files: ['<%= theme_src %>/js/lib/*.js']
        tasks: [
          'concat'
          'uglify'
        ]
      compass:
        files: [
          '<%= theme_src %>/layout/sass/*'
          '<%= theme_src %>/layout/images/*'
        ]
        tasks: ['compass']
      templates:
        files: ['<%= theme_src %>/layout/templates/**']
        tasks: [
          'sync:templates'
        ]
      images:
        files: ['<%= theme_src %>/layout/images/**']
        tasks: [
          'sync:images'
        ]
      fonts:
        files: ['<%= theme_src %>/layout/fonts/**']
        tasks: [
          'fontgen'
          'clean:fontsCss'
        ]

  # Load the plugin that provides the necessary task
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-coffee'
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-compass'
  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-contrib-copy'
  grunt.loadNpmTasks 'grunt-contrib-imagemin'
  grunt.loadNpmTasks 'grunt-contrib-clean'
  grunt.loadNpmTasks 'grunt-sync'
  grunt.loadNpmTasks 'grunt-fontgen'

  # Default task(s)
  grunt.registerTask 'default', [
    'watch'
  ]

  # Production task
  grunt.registerTask 'production', [
    'coffee'
    'concat'
    'compass'
    'uglify'
    'sync:templates'
    'sync:fonts'
    'imagemin'
  ]
