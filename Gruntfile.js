// Comment out if you want to use Fieldbook
const fs = require('fs');

module.exports = function (grunt) {
    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({
        clean: {
            before : ['dist/*', '.tmp/'],
            after : ['dist/css/scss', 'dist/js/*']
        },

        sass: {
            options : {
                style : 'compressed'
            },

            dist : {
                files : {
                    'app/css/style.css' : 'app/css/scss/style.scss'
                }
            }
        },

        autoprefixer : {
            dist : {
                files : {
                    'app/css/style.css' : 'app/css/style.css'
                }
            }
        },

        watch : {
            css : {
                files : 'app/css/scss/*.scss',
                tasks : ['style']
            }
        },

        copy: {
            all: {
                files: [
                    {
                        expand: true,
                        cwd : 'app',
                        src: '**',
                        dest: 'dist'
                    }
                ]
            }
        },

        browserify : {
            src : {
                src : 'app/js/app.js',
                dest : 'app/bundle.js',
                options : {
                    alias : {
                        "vue": "vue/dist/vue.common.js"
                    },
                    browserifyOptions : {
                        debug : true
                    },
                    transform : [
                        'vueify',
                        ["babelify", {
                            presets : ["env"]
                        }]
                    ],
                    watch : true,
                    keepAlive : false
                }
            },

            dist : {
                src : 'dist/js/app.js',
                dest : 'dist/bundle.js',
                options : {
                    alias : {
                        "vue": "vue/dist/vue.min.js"
                    },
                    browserifyOptions : {
                        debug : false
                    },
                    transform : [
                        'vueify',
                        ["babelify", {
                            presets : ["env"]
                        }]
                    ]
                }
            }
        },

        asset_cachebuster : {
            options : {
                buster : Date.now()
            },

            build : {
                files : {
                    'dist/index.html' : ['dist/index.html']
                }
            }
        },

        uglify : {
            build : {
                src : 'dist/bundle.js',
                dest : 'dist/bundle.js'
            }
        }
    });

    grunt.registerTask('init', [
        'style',
        'browserify:src'
    ]);

    grunt.registerTask('build', [
        'clean:before',
        'copy:all',
        'sass',
        'autoprefixer',
        'browserify:dist',
        'uglify',
        'asset_cachebuster',
        'clean:after'
    ]);

    grunt.registerTask('watch-all', [
        'browserify:src',
        'watch'
    ]);

    grunt.registerTask('style', [
        'sass',
        'autoprefixer'
    ]);

    grunt.registerTask('default', ['build']);
};