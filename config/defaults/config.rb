configuration = Capistrano::Configuration.respond_to?(:instance) ? Capistrano::Configuration.instance(:must_exist) : Capistrano.configuration(:must_exist)

configuration.load do

	def pretty_message(msg)
		message = "--> #{msg}"
		puts message.green
	end

	def pretty_error(err)
		error = "--> #{err}"
		puts error.red
	end

	# Define some extra folder to create
	set :shared_children, %w(files app/config)

	# Define the branch we'll use to create our tags
	set :tag_branch, 'production'

	# The file where we'll store the directories that are already deployed.
	set :executed_delta_scripts, "executed_delta_scripts"

	# Link the default_www folder to the current directory
	after 'deploy:setup', 'forkcms:setup'

	# Set the stage environments.
	set :stages, ["development", "staging", "production"]
	set :default_stage, "development"

	# Do a deploy rollback before the wijs rollback
	after 'wijs:rollback', 'deploy:rollback'
	after 'deploy:rollback', 'wijs:symlink_root'

	# This will allow us to install the delta files at the end of the deploy.
	after 'deploy:cleanup' do
		forkcms.clear_cached
		wijs.symlink_root
		wijs.kill_php_processes
		wijs.create_git_tag
	end

	# Always cleanup after you're done.
	after 'deploy:finalize_update', 'deploy:cleanup'

	# Install the delta files
	before 'deploy:finalize_update' do
		composer.update

		transaction do
			forkcms.link_files
			forkcms.link_config
			wijs.migrate
		end
	end

	# We only want 3 backups at a time.
	set :keep_releases, 5

	# Use Git for version control.
	set :scm, :git
	set :copy_strategy, :checkout

	# We don't want to use deploy keys, forward our personal key so we have easy access.
	set :ssh_options, { :forward_agent => true }

	# remote caching will keep a local git repo on the server you're deploying to and simply run a fetch from that
	# rather than an entire clone. This is probably the best option and will only fetch the differences each deploy.
	set :deploy_via, :remote_cache

	# Don't use sudo, on most shared setups we won't have sudo-access.
	set :use_sudo, false

	# We're on a share setup so group_writable isn't allowed.
	set :group_writable, false

	# The functionallity for 'wijs' websites.
	namespace :wijs do

		desc 'Create a git tag with a certain format.'
		task :create_git_tag do

			# Only add tags for a specified environment
			if branch == tag_branch
				pretty_message("creating a new git tag")

				tag_name = "#{release_name}"
				run("cd #{release_path} && git tag #{tag_name} && git push origin tag #{tag_name} -q")
			end
		end

		desc 'Migrate our system.'
		task :migrate do
			pretty_message("migrating the system")

			currentDirectoryExists = capture("if [ ! -e #{current_path} ]; then echo 'yes'; fi").chomp

			# When we're doing a first deploy, we don't want our delta files to
			# be executed. The database should be a clean copy, thus the delta
			# files should already've been executed. This will put the items in
			# the executed folder check file if there is no current directory
			# and execute the delta files if there is a current directory.
			if currentDirectoryExists == 'yes'
				pretty_message("first deploy, saving execute delta files")
				folders = capture("if [ -e #{release_path}/delta ]; then ls -1 #{release_path}/delta; fi").split(/\r?\n/)

				folders.each do |dirname|
					run("echo #{dirname} | tee -a #{shared_path}/#{executed_delta_scripts}")
				end
			else
				wijs.install_delta_files
			end
		end

		# This will install all the files in the /delta/ directory if there are any.
		desc 'Install the delta files.'
		task :install_delta_files do

			pretty_message("looking for new delta files")

			# There was an error, do a rollback!
			on_rollback { wijs.rollback }

			# Fetch the basic delta folder data.
			folders = Array.new
			executedDeltaScripts = capture("cat #{shared_path}/#{executed_delta_scripts}").chomp.split(/\r?\n/)
			folders = capture("if [ -e #{release_path}/delta ]; then ls -1 #{release_path}/delta; fi").split(/\r?\n/)

			# No need to do anything if there are no delta folders.
			if folders.length > 0

				executeDirs = Array.new

				folders.each do |dirname|

					# Not all folders need to be checked, @todo: find better way to do this.
					if dirname != '.' && dirname != '..' && dirname != '.git' && dirname != '.svn'

						# Only add the directory if the it isn't used before.
						executeDirs.push(dirname) if executedDeltaScripts.index(dirname) == nil
					end
				end

				# We don't need to take any action if there are no new files.
				if executeDirs.length > 0

					# There are delta files. This could take a while and there could be
					# database changes. Because of that, we want to show a proper maintenance
					# page to the visitors.
					wijs.symlink_maintenance

					forkcms.backup_database

					# Go trough the delta directories and check for 3 update files, update.sql,
					# update.php and locale.xml. These are the only 3 files that we'll allow to
					# execute actions from.
					executeDirs.each do |dirname|

						pretty_message("executing delta folder '#{dirname}'")

						# Split the directory name to get the number out of it
						dirChunks = dirname.split('-')

						# Set the base path for the delta directory.
						deltaPath = "#{release_path}/delta/#{dirname}"

						# List all the files in the delta directory.
						deltaFiles = capture("ls -1 #{deltaPath}").split(/\r?\n/)

						deltaFiles.each do |filename|

							# Install locale via the Fork CMS locale import.
							run("cd #{release_path}/tools && php install_locale.php -f #{deltaPath}/#{filename} -o") if filename.index('locale.xml') != nil

							# Run update.php script.
							run("cd #{release_path} && php delta/#{dirname}/#{filename}") if filename.index('update.php') != nil

							# Run mysql import
							run("cd #{release_path}/tools && ./mysql_update #{deltaPath}/#{filename}") if filename.index('update.sql') != nil
						end
					end

					# The reason we go over the files again here is because of a rollback.
					# If we add the directory name directly to the executed file, we
					# won't be able to re-run the delta files if there was a rollback.
					# If we get to this point, it means we've successfully executed all
					# files so we can add them properly.
					executeDirs.each do |dirname|
						run("echo #{dirname} | tee -a #{shared_path}/#{executed_delta_scripts}")
					end

				end
			end
		end

		desc 'This will enable the maintenance page so people will know we are working on the website.'
		task :symlink_maintenance do
			pretty_message("symlinking the maintenance page")

			run("rm -rf #{document_root} && ln -sf #{current_path}/maintenance #{document_root}")
		end

		desc 'This will symlink back the document root with the current deployed version.'
		task :symlink_root do
			pretty_message("symlinking the document root")

			run("rm -rf #{document_root} && ln -sf #{current_path} #{document_root}")
		end

		desc 'This will kill the php processes to prevent php realpath caching'
		task :kill_php_processes do
			pretty_message("kill php processes")

			run("pkill -U `id -u` php5-fpm");
		end

		# Do custom wijs rollback stuff.
		desc 'This will rollback our application to a previous state.'
		task :rollback do
			pretty_error("error occured, rolling back")
			pretty_message("restoring the database")

			backup_file = "#{current_path}/mysql_backup.sql"
			run("cd #{release_path}/tools && ./mysql_update #{backup_file}")
		end
	end

	# The functionallity for Fork CMS.
	namespace :forkcms do

		desc 'This will backup the database.'
		task :backup_database do

			pretty_message("backing up the database")

			# Backup MySQL database.
			run("cd #{release_path}/tools && ./mysql_backup #{current_path}/mysql_backup.sql")
		end

		desc 'Link the document root to the current/default_www-folder'
		task :link_document_root do

			# Create symlink for document_root if it doesn't exists.
			documentRootExists = capture("if [ ! -e #{document_root} ]; then ln -sf #{current_path} #{document_root}; echo 'no'; fi").chomp

			# The document root already exists, tell our deployer.
			unless documentRootExists == 'no'
				warn "Warning: Document root (#{document_root}) already exists"
				warn 'to link it to the Fork deploy issue the following command:'
				warn '	ln -sf #{current_path} #{document_root}'
			end

		end

		desc 'Do an initial setup, make all the required shared folders etc.'
		task :setup do

			run %{
				mkdir -p #{shared_path}/app/config &&
				mkdir -p #{shared_path}/files &&
				mkdir -p #{shared_path}/mysql_dumps
			}

			# Check if the parameters.yml file exists.
			parametersFileExists = capture("if [ -f #{shared_path}/app/config/parameters.yml ]; then echo 'yes'; fi").chomp

			# Only create a default file if it doesn't exist yet.
			unless parametersFileExists == 'yes'

				# Create an empty file so we can symlink it in the first release
				run %{
					touch #{shared_path}/app/config/parameters.yml
				}

			end

			# Check if the lastDeltaScript file exists.
			deltaFileExists = capture("if [ -f #{shared_path}/#{executed_delta_scripts} ]; then echo 'yes'; fi").chomp

			# Only create the file if it doesn't exists.
			unless deltaFileExists == 'yes'
				delta_file = ''

				# Insert the data into the file.
				put delta_file, "#{shared_path}/#{executed_delta_scripts}"
			end

			# Link the document root.
			forkcms.link_document_root

		end

		desc 'Clear the frontend and backend cache-folders'
		task :clear_cached do

			pretty_message("clearing the cache")

			# Remove all the cached data, use the built in fork remove_cache script for that.
			run %{
				cd #{release_path}/tools &&
				./remove_cache
			}
		end

		desc 'Create the sym link for the config files.'
		task :link_config do

			pretty_message("symlinking the config files")

			# Get a list of files that are in the config/library path.
			files = capture("ls -1 #{shared_path}/app/config").split(/\r?\n/)

			# Go trough the files and symlink them with our current project.
			files.each do |file|
				run "ln -s #{shared_path}/app/config/#{file} #{release_path}/app/config/#{file}"
			end
		end

		desc 'Create needed symlinks.'
		task :link_files do

			pretty_message("symlinking the frontend files")

			# Get the list of folders in /frontend/files
			folders = capture("ls -1 #{release_path}/src/Frontend/Files").split(/\r?\n/)

			folders.each do |folder|

				# Copy them to the shared path, remove them from the release and symlink them.
				run %{
					mkdir -p #{shared_path}/files/#{folder} &&
					cp -r #{release_path}/src/Frontend/Files/#{folder} #{shared_path}/files/
				}
			end

			# Remove the files directory in our project directory and symlink the shares directory
			run %{
				rm -rf #{release_path}/src/Frontend/Files &&
				ln -s #{shared_path}/files #{release_path}/src/Frontend/Files
			}
		end
	end

	namespace :composer do
		desc 'Get composer'
		task :get do
			pretty_message("getting composer")
			composerFileExists = capture("if [ -f #{composer_bin} ]; then echo 'yes'; fi").chomp

			if composerFileExists != "yes"
				pretty_message("downloading composer")
				run "cd #{shared_path} && curl -s http://getcomposer.org/installer | php"
			else
				pretty_message("updating composer")
				run "#{composer_bin} self-update"
			end
		end

		desc 'Install the vendor files. Since we commit our composer.lock, this can be an update'
		task :update do
			usingComposer = capture("if [ -f #{release_path}/composer.json ]; then echo 'yes'; fi").chomp

			if usingComposer == "yes" && use_composer
				composer.get

				pretty_message("updating dependencies")
				run "cd #{release_path} && #{composer_bin} install -o --no-dev"
			end
		end
	end
end
