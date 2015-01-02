module.exports = (grunt) ->

  # Project configuration
  grunt.initConfig
    pkg: grunt.file.readJSON 'package.json'
    theme_src: 'src/Frontend/Themes/<%= pkg.theme %>/Src'
    theme_build: 'src/Frontend/Themes/<%= pkg.theme %>/Core'
    coffee:
      compileJoined:
        options:
          bare: true
        files:
          '<%= theme_build %>/Js/theme.js': [
            '<%= theme_src %>/Coffee/theme.coffee'
          ]
    compass:
      dist:
        options:
          config: '<%= theme_src %>/Layout/config.rb'
          sassDir: '<%= theme_src %>/Layout/sass'
          cssDir: '<%= theme_build %>/Layout/Css'
          imageDir: '<%= theme_build %>/Layout/images'
          fontsDir: '<%= theme_build %>/Layout/fonts'
          relativeAssets: true
          bundleExec: true
    replace:
      head:
        options:
          patterns: [
            {
              match: /\<script\ src=\"\/src/g
              replacement: '<script src="{$THEME_URL}/src'
            }
            {
              match: /\<script\ src=\"\/Core\/Js/g
              replacement: '<script src="{$THEME_URL}/Core/Js'
            }
          ]
        files: [
          src: '<%= theme_src %>/Layout/Templates/Head.tpl'
          dest: '<%= theme_build %>/Layout/Templates/'
          flatten: true
          expand: true
        ]
    useminPrepare:
      options:
        root: '<%= theme_src %>/../'
        dest: '<%= theme_src %>/../'
      html: '<%= theme_src %>/Layout/Templates/Head.tpl'
    usemin:
      html: '<%= theme_build %>/Layout/Templates/Head.tpl'
      options:
        blockReplacements:
          js: (block) ->
            '<script src="{$THEME_URL}' + block.dest + '"></script>'
    clean:
      fontsCss: [
        '<%= theme_build %>/Layout/Fonts/*.css'
      ]
      iconfont: [
        '<%= theme_build %>/Layout/Fonts/icons-*.*'
      ]
      aftericonfont: [
        '<%= theme_src %>/Layout/Fonts/icons-*.*'
      ]
      dist: [
        '.tmp'
      ]
    autoprefixer:
      dist:
        src: '<%= theme_build %>/Layout/Css/**.css'
    copy:
      templates:
        files: [
          expand: true
          cwd: '<%= theme_src %>/Layout/Templates/'
          src: '**'
          dest: '<%= theme_build %>/Layout/Templates/'
        ]
      svg:
        files: [
          expand: true
          cwd: '<%= theme_src %>/Layout/Images/'
          src: '*.svg'
          dest: '<%= theme_build %>/Layout/Images/'
        ]
        updateAndDelete: true
      fonts:
        files: [
          expand: true
          cwd: '<%= theme_src %>/Layout/Fonts/'
          src: '**'
          dest: '<%= theme_build %>/Layout/Fonts/'
        ]
    imagemin:
      dynamic:
        files: [
          expand: true
          cwd: '<%= theme_src %>/Layout/Images/'
          src: ['**/*.{png,jpg,gif,jpeg}']
          dest: '<%= theme_build %>/Layout/Images/'
        ]
    fontgen:
      all:
        options:
          stylesheet: false
        files: [
          src: [
            '<%= theme_src %>/Layout/Fonts/*.ttf'
            '<%= theme_src %>/Layout/Fonts/*.otf'
          ]
          dest: '<%= theme_build %>/Layout/Fonts/'
        ]
    webfont:
      icons:
        src: '<%=theme_src %>/Layout/icon-sources/*.svg'
        dest: '<%= theme_src %>/Layout/Fonts/'
        destCss: '<%= theme_src %>/Layout/Sass/'
        classPrefix: 'icon-'
        options:
          stylesheet: 'scss'
          htmlDemo: false
          template: '<%= theme_src %>/Layout/Sass/_icons-template.scss'
          templateOptions:
            classPrefix: 'icon-'
    watch:
      coffee:
        files: ['<%= theme_src %>/Coffee/*']
        tasks: ['coffee']
      lib:
        files: ['<%= theme_src %>/Js/lib/*.js']
        options:
          livereload: true
      sass:
        files: ['<%= theme_src %>/Layout/**/*.scss']
        tasks: ['compass:dist', 'autoprefixer:dist']
      templates:
        files: ['<%= theme_src %>/Layout/Templates/**']
        tasks: [
          'copy:templates'
          'replace:head'
        ]
      images:
        files: ['<%= theme_src %>/Layout/Images/**']
        tasks: [
          'imagemin'
        ]
      fonts:
        files: ['<%= theme_src %>/Layout/Fonts/**']
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

  # Load all grunt tasks
  require('load-grunt-tasks')(grunt);

  # Default task(s)
  grunt.registerTask 'default', [
    'serve'
  ]

  # Development tasks
  grunt.registerTask 'serve', [
    'replace:head'
    'watch'
  ]

  grunt.registerTask 'iconfont', [
    'clean:iconfont'
    'webfont'
    'copy:fonts'
    'clean:aftericonfont'
  ]

  # Production task
  grunt.registerTask 'build', [
    'clean:iconfont'
    'compass:dist'
    'autoprefixer'
    'coffee'
    'copy:templates'
    'useminPrepare'
    'concat:generated'
    'uglify:generated'
    'usemin'
    'copy:svg'
    'imagemin'
    'iconfont'
    'fontgen'
    'copy:fonts'
    'clean:fontsCss'
    'clean:dist'
  ]
