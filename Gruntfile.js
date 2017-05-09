
'use strict';

// Load Grunt
module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		phpdocumentor: {
				dist: {
            options: {
	            directory : './src',
							target : 'docs'
            }
        }
    },

		clean: {
		  docs: ['docs']
		},

		php: {
        serve: {
            options: {
								port: 8000,
								open: true,
								keepalive: true,
								base: 'src'
            }
        },
				develop: {
            options: {
								port: 8000,
								open: true,
								base: 'src'
            }
        }
    },

		watch: {
			gruntfile: {
				options: {
					spawn: false,
					reload: true
				},
				files: [ 'Gruntfile.js' ]
			}
		}

	});

	// Load Grunt plugins
	require('load-grunt-tasks')(grunt);

	// Register Grunt tasks
	grunt.registerTask('default', [ 'php:serve' ]);
	grunt.registerTask('develop', [ 'php:develop', 'watch' ]);
	grunt.registerTask('serve', [ 'default' ]);
	grunt.registerTask('docs', [ 'clean', 'phpdocumentor' ]);

};
