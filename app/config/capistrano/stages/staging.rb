server "#{fetch :host}", user: "#{fetch :client}", roles: %w{app db web}

set :project, "staging"

set :deploy_to, "/data/sites/web/#{fetch :client}/apps/#{fetch :project}"
set :document_root, "/data/sites/web/#{fetch :client}/subsites/#{fetch :project}.#{fetch :domain_name}"

# Currently not available on Combell hosting
#set :opcache_reset_strategy, "fcgi"
#set :opcache_reset_fcgi_connection_string, "/data/jail/var/sock/fpm/#{fetch :client}.sock"

set :opcache_reset_strategy, "file"
set :opcache_reset_base_url, "#{fetch :staging_url}"

### DO NOT EDIT BELOW ###
set :branch, "#{fetch :project}"
set :application, "#{fetch :project}"
set :keep_releases, 2
set :php_bin, "php"

SSHKit.config.command_map[:composer] = "#{fetch :php_bin} #{shared_path.join("composer.phar")}"
SSHKit.config.command_map[:php] = fetch(:php_bin)
