set :deploy_config_path, 'app/config/capistrano/deploy.rb'
set :stage_config_path, 'app/config/capistrano/stages'

require 'capistrano/setup'
require 'capistrano/deploy'
require 'capistrano/scm/git'
install_plugin Capistrano::SCM::Git
require 'capistrano/forkcms'
require 'capistrano/sumo'
require 'capistrano/deploytags'

set :format_options, log_file: 'var/logs/capistrano.log'

Dir.glob('app/config/capistrano/tasks/*.rake').each { |r| import r }
