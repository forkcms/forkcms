set :production_server, "#{production_hostname}"
set :user, "#{production_account}"
set :app_path, "apps/#{application}"
set :document_root, "#{production_document_root}"
set :branch, "master"

# Uncomment the variables below if the DB-credentials are different from
# the credentials on the staging server
#set :db_username, ""
#set :db_password, ""
#set :db_host, ""
