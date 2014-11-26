# Do not output DEBUG/TRACE messages
logger.level = Logger::INFO

# Check if we have the Capistrano extension 'Multistage' installed.
begin
	require 'capistrano/ext/multistage'
	require 'colored'
	require File.dirname(__FILE__) + '/defaults/config.rb'
rescue LoadError
    $stderr.puts <<-INSTALL
You need the both the capistrano-ext and colored gems to deploy this application
Install the gems like this:
	gem install capistrano-ext
	gem install colored
	INSTALL
	exit 1
end

# Our git repository.
# Fill in your git repository. This should be automated though.
set :repository, "git@bitbucket.org:siesqo/fork-cms-tester.git"

# Overwrite some defaults because this isn't a Rails app.
namespace :deploy do
	task :setup do
		# define folders to create
		dirs = [deploy_to, releases_path, shared_path]

		# add folder that aren't standard
		dirs += shared_children.map { |d| File.join(shared_path, d) }

		# create the dirs
		run %{
			#{try_sudo} mkdir -p #{dirs.join(' ')} &&
			#{try_sudo} chmod g+w #{dirs.join(' ')}
		}
	end

	task :finalize_update, :except => { :no_release => true } do
		# Fork CMS isn't a Rails-application so don't do Rails specific stuff
		run 'chmod -R g+w #{latest_release}' if fetch(:group_writable, true)
	end

	task :migrate do
		# Nothing.
	end

	desc 'This will do all the required restart actions, deleting capistrano files.'
	task :restart do
		run "rm -rf #{current_path}/config && rm -f #{current_path}/Capfile"
	end
end
