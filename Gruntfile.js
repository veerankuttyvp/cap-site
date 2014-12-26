module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    less: {
      options: {
        paths: ['bower_components/bootstrap/less','bower_components/components-font-awesome/less'],
        compress: false,
        yuicompress: true,
        optimization: 2
      },
      "style":{
        "files": {
          'public/css/style.css':'less/style.less'
        }
      },
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
        /*
        mangle: {
        	except: ['jQuery']
        },
        */
        mangle:false,
        beautify: true,
        //compress:true,
        compress:false,
      },
      "global": {
      	"files": {
      		'public/js/cap.min.js':[
            'bower_components/jquery/jquery.js',
            'bower_components/bootstrap/js/modal.js',
            'bower_components/bootstrap/js/transition.js',
            'bower_components/bootstrap/js/collapse.js',
            'bower_components/bootstrap/js/alert.js',
            'bower_components/bootstrap/js/carousel.js',
            'bower_components/bootstrap/js/tooltip.js',
            'bower_components/bootstrap/js/dropdown.js',
            'bower_components/bootstrap/js/popover.js',
            'bower_components/bootstrap/js/tab.js',
            'bower_components/angular/angular.js',
            'bower_components/angular-route/angular-resource.js',
            'bower_components/angular-route/angular-route.js',
            'bower_components/angular-cookies/angular-cookies.js',
            'bower_components/angular-animate/angular-animate.js',
            'angular/controllers.js',
            'angular/directives.js',
            'angular/services.js',
            'angular/router.js',
            'angular/filters.js'
          ]
      	}
      },
      "login": {
        "files": {
          'public/js/login.min.js':[
            'angular/controllers/login.js',
            'angular/apps/login.js',
          ]
        }
      },
      "admin": {
        "files": {
          'public/js/admin.min.js':[
            'angular/apps/admin.js',
            'angular/controllers/admin.js',
          ]
        }
      },
      "mentor": {
        "files": {
          'public/js/mentor.min.js':[
            'angular/apps/mentor.js',
            'angular/controllers/mentor.js',
          ]
        }
      },
      "mentee": {
        "files": {
          'public/js/mentee.min.js':[
            'angular/apps/mentee.js',
            'angular/controllers/mentee.js',
          ]
        }
      },
      "questionnaire": {
        "files": {
          'public/js/questionnaire.min.js':[
            'angular/apps/questionnaire.js',
            'angular/controllers/questionnaire.js',
          ]
        }
      },

    },
    watch: {
      styles: {
        files: ['less/**/*.less'],
        tasks:['less'],
        options: {
          spawn: false
        }
      },
      js: {
        files: ['angular/**/*.js'],
        tasks:['uglify'],
        options: {
          spawn: false
        }
      },
    }
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['watch']);
  grunt.registerTask('watch-invitation',['watch:invitation']);

};
