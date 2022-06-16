module.exports = function (grunt) {

	require('load-grunt-tasks')(grunt);

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		composerBin: 'vendor/bin',

		shell: {
			phpcs: {
				options: {
					stdout: true
				},
				command: '<%= composerBin %>/phpcs'
			},

			phpcbf: {
				options: {
					stdout: true
				},
				command: '<%= composerBin %>/phpcbf'
			},

			phpstan: {
				options: {
					stdout: true
				},
				command: '<%= composerBin %>/phpstan analyze .'
			},

			phpunit: {
				options: {
					stdout: true
				},
				command: '<%= composerBin %>/phpunit'
			},
		},

		gitinfo: {
			commands: {
				'local.tag.current.name': ['name-rev', '--tags', '--name-only', 'HEAD'],
				'local.tag.current.nameLong': ['describe', '--tags', '--long']
			}
		},

		addtextdomain: {
			options: {
				textdomain: 'wp-local-media-proxy',    // Project text domain.
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: ['*.php', '**/*.php', '!node_modules/**', '!tests/**', '!scripts/**', '!vendor/**', '!wordpress/**']
			},
		},

		checkrepo: {
			deploy: {
				tagged: true, // Check that the last commit (HEAD) is tagged
				tag: {
					eq: '<%= pkg.version %>' // Check if highest repo tag is equal to pkg.version
				}
			}
		},

		checktextdomain: {
			options: {
				text_domain: 'wp-local-media-proxy',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_x:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				],
			},
			files: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!dist/**',
					'!tests/**',
					'!vendor/**',
					'!wordpress/**',
					'!*~',
				],
				expand: true,
			},
		},

		// Bump version numbers
		version: {
			class: {
				options: {
					prefix: "const VERSION = '"
				},
				src: ['<%= pkg.name %>.php']
			},
			header: {
				options: {
					prefix: '\\* Version:\\s+'
				},
				src: ['<%= pkg.name %>.php']
			},
			readme: {
				options: {
					prefix: 'Stable tag:\\s+'
				},
				src: ['readme.txt']
			}
		}

	});

	grunt.registerTask('phpcs', ['shell:phpcs']);
	grunt.registerTask('phpcbf', ['shell:phpcbf']);
	grunt.registerTask('phpstan', ['shell:phpstan']);
	grunt.registerTask('phpunit', ['shell:phpunit']);
	grunt.registerTask('i18n', ['addtextdomain']);
	grunt.registerTask('readme', ['wp_readme_to_markdown']);
	grunt.registerTask('test', ['checktextdomain', 'phpcs']);
	grunt.registerTask('build', ['gitinfo', 'test', 'i18n']);
	grunt.registerTask('release', ['checkbranch:HEAD', 'checkrepo', 'gitinfo', 'checktextdomain']);

};

