/*!
 * Bootstrap's Gruntfile
 * http://getbootstrap.com
 * Copyright 2013-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */


module.exports = function(grunt) {
    // 任务配置
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        jshint: {
            options: {
                undef : true,
                unused: true,
                curly: false,
                eqeqeq: true,
                eqnull: true,
                browser: true,
                globals: {
                    jQuery: true
                }
            }

        },

        less: {
            development: {
                files: {
                    "<%= pkg.pathCss %>/admin.css": "<%= pkg.pathCss %>/_src/admin.less",
                    "<%= pkg.pathCss %>/common.css": "<%= pkg.pathCss %>/_src/common.less",
                    "<%= pkg.pathCss %>/bootstrap.css": "<%= pkg.pathCss %>/_src/bootstrap3.2/bootstrap.less"
                }
            }
        },

        cssmin: {
            minify: {
                expand: true,
                cwd: '<%= pkg.pathCss %>/',
                src: ['*.css', '!*.min.css','*/*.css','!*/*.min.css'],
                dest: '<%= pkg.pathCss %>/',
                ext: '.min.css'
            }

        },

        watch :{
            css :{
                files: [
                    '<%= pkg.pathCss %>/_src/*.less' ,
                    '<%= pkg.pathCss %>/_src/*/*.less',
                    '<%= pkg.pathCss %>/_src/*/*/*.less'
                ],
                tasks:['less','cssmin']

            }
        }

    });

    // 任务加载
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');

    grunt.registerTask('default', ['jshint',"less","cssmin"]);
    grunt.registerTask('watching', ['jshint',"less","cssmin","watch"]);

};
