set :application, "production"
set :branch, "master"

# define roles
set :user, "vs09050"
# For our projects, this will be gallium most of the times.
server "dev.soononline.be", :app, :web, :db, :primary => true

set :deploy_to, "/opt/www/#{user}/web/dev.soononline.be/apps/#{application}"
set :document_root, "/opt/www/#{user}/web/dev.soononline.be/default_www"

set :composer_bin, "#{shared_path}/composer.phar"
set :use_composer, true
