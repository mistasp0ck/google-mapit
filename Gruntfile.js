const sass = require('node-sass');

module.exports = function(grunt) {

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'admin/js/*.js',
        'public/js/*.js'
      ]
    },
    sass: {
      options: {
        implementation: sass
      },
      dev: {
        options: {
          implementation: sass,
          sourceMap: true
        },         
        files: {
          'public/dist/styles.min.css': 'public/scss/styles.scss',
          'admin/dist/admin-styles.min.css': 'admin/scss/admin-styles.scss'
        } 
      },  
      prod: {
        options: {
          implementation: sass,
          style: 'compressed',
          sourceMap: false         
        },      
        files: {
          'public/dist/styles.min.css': 'public/scss/styles.scss',
          'admin/dist/admin-styles.min.css': 'admin/scss/admin-styles.scss'
        } 
      } 
    },
    uglify: {
      dist: {
        files: {
          'public/dist/scripts.min.js': 'public/js/*.js',          
          'admin/dist/admin-scripts.min.js': 'admin/js/*.js'
        },
        options: {
          compress: {
            drop_console: true
          }
        }
      },
      dev: {
        files: {
          'public/dist/scripts.min.js': 'public/js/*.js',          
          'admin/dist/admin-scripts.min.js': 'admin/js/*.js'
        },
        options: {
          // JS source map: to enable, uncomment the lines below and update sourceMappingURL based on your install
          sourceMap : true,
          sourceMapName : 'scripts.min.map'
        }     
      }
    },
    // autoprefixer
    autoprefixer: {
        options: {
            browsers: ['last 2 versions', 'ie 9', 'ios 6', 'android 4'],
            map: true
        },
        files: {
            expand: true,
            flatten: true,
            src: 'public/dist/css/*.css',
            dest: 'public/dist/css'
        },
    },

    // css minify
    cssmin: {
        options: {
            keepSpecialComments: 1
        },
        minify: {
            expand: true,
            cwd: 'public/dist/css',
            src: ['*.css', '!*.min.css'],
            ext: '.css'
        }
    },     
    watch: {
      sass: {
        files: [
          'public/scss/*.scss',
          'admin/scss/admin-styles.scss',
        ],
        tasks: ['sass:dev']
      },

      js: {
        files: [
          '<%= jshint.all %>'
        ],
        tasks: ['uglify:dev']
      }
    },
    browserSync: {
      dev: {
          bsFiles: {
              src : [
                  'admin/dist/*',
                  'public/dist/*',
                  '**/*.php'
              ]
          },
          options: {
              watchTask: true,
              proxy: "tony.local:8888/tonys-site/"
          }
      }
    },
    clean: {
      dist: [
        'library/dist/css',
        'library/dist/js'
      ]
    }
  });

  // Load tasks
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-wp-assets');
  grunt.loadNpmTasks('grunt-browser-sync');

  // Register tasks
  // Register tasks
  grunt.registerTask('default', ['browserSync', 'watch']);

  grunt.registerTask('build', [
    'clean:dist',
    'sass:prod',
    'uglify:dist',
    'autoprefixer',
    'cssmin'

  ]);

  grunt.registerTask('dev', [
    'watch'
  ]);


};
