module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({

        sass: {
            dist: {
                options: {
                    style: 'compressed'
                },
                files: {
                    './assets/css/mey.channels.main.css': './assets/scss/**/*.scss'
                }
            }
        },

        //I love globbing yes I do, I love globbing how 'bout you?
        watch: {
            sass: {
                files: [
                    './assets/scss/**/*.scss'
                ],
                tasks: ['sass']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task.
    grunt.registerTask('default', ['sass']);
};
