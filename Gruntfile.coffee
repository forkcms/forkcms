module.exports = (grunt) ->

  # Project configuration
  grunt.initConfig
    pkg: grunt.file.readJSON 'package.json'
    theme_src: 'src/Frontend/Themes/<%= pkg.theme %>/Src'
    theme_build: 'src/Frontend/Themes/<%= pkg.theme %>/Core'
    uglify:
      options:
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      build:
        src: '<%= theme_build %>/Js/lib.js'
        dest: '<%= theme_build %>/Js/lib.js'
    coffee:
      compileJoined:
        options:
          bare: true
        files:
          '<%= theme_build %>/Js/theme.js': [
            '<%= theme_src %>/Coffee/theme.coffee'
          ]
    concat:
      options:
        seperator: ';'
      dist:
        src: [
          '<%= theme_src %>/Js/lib/*.js'
        ]
        dest: '<%= theme_src %>/Js/lib.js'
    compass:
      dist:
        options:
          config: '<%= theme_src %>/Layout/config.rb'
          sassDir: '<%= theme_src %>/Layout/sass'
          cssDir: '<%= theme_build %>/Layout/Css'
          imageDir: '<%= theme_build %>/Layout/images'
          fontsDir: '<%= theme_build %>/Layout/fonts'
          relativeAssets: true
    sync:
      templates:
        files: [
          cwd: '<%= theme_src %>/Layout/Templates/'
          src: '**'
          dest: '<%= theme_build %>/Layout/Templates/'
        ]
      images:
        files: [
          cwd: '<%= theme_src %>/Layout/images/'
          src: '**'
          dest: '<%= theme_build %>/Layout/images/'
        ]
        updateAndDelete: true
      svg:
        files: [
          cwd: '<%= theme_src %>/Layout/images/'
          src: '*.svg'
          dest: '<%= theme_build %>/Layout/images/'
        ]
        updateAndDelete: true
      fonts:
        files: [
          cwd: '<%= theme_src %>/layout/fonts/'
          src: '**'
          dest: '<%= theme_build %>/layout/fonts/'
        ]
      scripts:
        files: [
          cwd: '<%= theme_src %>/js/'
          src: '**'
          dest: '<%= theme_build %>/js/'
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
        dest: '<%= theme_build %>/Layout/images/'
      fonts:
        expand: true
        cwd: '<%= theme_src %>/Layout/fonts/'
        src: '**'
        dest: '<%= theme_build %>/Layout/fonts/'
    imagemin:
      dynamic:
        files: [
          expand: true
          cwd: '<%= theme_src %>/Layout/images/'
          src: ['**/*.{png,jpg,gif,jpeg}']
          dest: '<%= theme_build %>/Layout/images/'
        ]
    fontgen:
      all:
        options:
          stylesheet: false
        files: [
          src: [
            '<%= theme_src %>/Layout/fonts/*.ttf'
            '<%= theme_src %>/Layout/fonts/*.otf'
          ]
          dest: '<%= theme_build %>/Layout/fonts/'
        ]
    webfont:
      icons:
        src: '<%=theme_src %>/Layout/icon-sources/*.svg'
        dest: '<%= theme_src %>/Layout/fonts/'
        destCss: '<%= theme_src %>/Layout/sass/'
        classPrefix: 'icon-'
        options:
          stylesheet: 'scss'
          htmlDemo: false
          template: '<%= theme_src %>/layout/sass/_icons-template.scss'
          templateOptions:          
            classPrefix: 'icon-'
    clean:
      templates: [
        '<%= theme_build %>/Layout/Templates/'
      ]
      images: [
        '<%= theme_build %>/Layout/images/'
      ]
      fonts: [
        '<%= theme_build %>/Layout/fonts/'
      ]
      fontsCss: [
        '<%= theme_build %>/Layout/fonts/*.css'
      ]
      iconFonts: [
        '<%= theme_build %>/Layout/fonts/icons-*.*'
      ]
      core: [
        '<%= theme_build %>'
      ]
    watch:
      coffee:
        files: ['<%= theme_src %>/coffee/*']
        tasks: ['coffee']
      lib:
        files: ['<%= theme_src %>/Js/lib/*.js']
        tasks: [
          'concat'
        ]
      sass:
        files: ['<%= theme_src %>/Layout/**/*.scss']
        tasks: ['compass:dist']
      templates:
        files: ['<%= theme_src %>/Layout/Templates/**']
        tasks: [
          'sync:templates'
        ]
      images:
        files: ['<%= theme_src %>/Layout/images/**']
        tasks: [
          'sync:images'
        ]
      scripts:
        files: ['<%= theme_src %>/js/**']
        tasks: [
          'sync:scripts'
        ]
      fonts:
        files: ['<%= theme_src %>/Layout/fonts/**']
        tasks: [
          'fontgen'
          'clean:fontsCss'
        ]
      icons:
        files: ['<%= theme_src %>/Layout/icon-sources/**']
        tasks: [
          'iconfont'
        ]
      livereload:
        options:
          livereload: 35729
        files: [
          '<%= theme_build %>/**/*'
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
  grunt.loadNpmTasks 'grunt-webfont'

  # Default task(s)
  grunt.registerTask 'default', [
    'watch'
  ]

  grunt.registerTask 'iconfont', [
    'clean:iconFonts'
    'webfont'
    'sync:fonts'
  ]

  # Development tasks
  grunt.registerTask 'dev', [
    'coffee'
    'concat'
    'sync:templates'
    'sync:images'
    'sync:fonts'
    'fontgen'
    'clean:fontsCss'
    'compass:dist'
  ]

  # Production task
  grunt.registerTask 'production', [
    'coffee'
    'concat'
    'uglify'
    'sync:templates'
    'sync:svg'
    'imagemin'
    'sync:fonts'
    'fontgen'
    'clean:fontsCss'
    'compass:dist'
  ]
