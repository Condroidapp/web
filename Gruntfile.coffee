module.exports = (grunt) ->
  grunt.initConfig
    useminPrepare:
      html: [
        'app/**/@*.latte'
      ]
      options:
        dest: '.'

    netteBasePath:
      basePath: 'www'
      options:
        removeFromPath: [
          'app/FrontModule/templates/',
          'app/AdminModule/templates/',
          'app\\FrontModule\\templates\\',
          'app\\AdminModule\\templates\\',
          'app\\templates\\',
          'app/templates/'
        ]
    concat:
      options:
        separator: '\n',
        process: (src, filepath) ->
            cssPatt = new RegExp("^www(/.*/).*.css$")
            #filter out everithing except css files
            file = cssPatt.exec(filepath)
            if file
              urlPatt = /url\(\'?([^\'\:\)]*)\'?\)/g
              console.log "In file: " + filepath

              #replace every url(...) with its absolute path
              return src.replace(urlPatt, (match, p1) ->
                console.log " * " + match + " -> " + "url('" + file[1] + p1 + "')"
                "url('" + file[1] + p1 + "')"
              )
            src
    watch:
      coffee:
        files: 'www/assets/coffee/*.coffee'
        tasks: 'coffee:compile'
        options:
          livereload: true
      less:
        files: 'www/assets/css/*.less'
        tasks: 'less:dev'
        options:
          livereload: true
      misc:
        files: ['www/assets/css/*.css', 'www/assets/js/*.js']
        options:
          livereload: true




  # These plugins provide necessary tasks.
  grunt.loadNpmTasks 'grunt-contrib-less'
  grunt.loadNpmTasks 'grunt-contrib-coffee'
  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-cssmin'
  grunt.loadNpmTasks 'grunt-usemin'
  grunt.loadNpmTasks 'grunt-nette-basepath'

  # Default task.
  grunt.registerTask 'deploy', [
    'useminPrepare'
    'netteBasePath'
    'concat'
    'uglify'
    'cssmin'
  ]

  #grunt.registerTask 'watch', ['watch:less', 'watch2:coffee']
  grunt.registerTask 'compile', [
    'less:dev', 'coffee:compile'
  ]
