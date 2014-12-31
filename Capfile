load "deploy" if respond_to?(:namespace) # cap2 differentiator

# development information
set :client,  ""                                  # eg: "dev"
set :project, ""                                  # eg: "site"
set :theme, "Bootstrap"                           # eg: "Bootstrap"

# production information, ignore these items during development
set :production_url, ""                           # eg: "http://fork.sumocoders.be"
set :production_account, ""                       # eg: "sumocoders"
set :production_hostname, ""                      # eg: "web01.crsolutions.be"
set :production_document_root, ""                 # eg: "/home/#{production_account}/#{production_url.gsub("http://","")}"
#set :production_db, ""                           # eg: "client_db"
set :production_errbit_api_key, ""

# repo information
set :repository, ""                               # eg: "git@github.com:sumocoders/forkcms.git"

# stages
set :stages, %w{production staging}
set :default_stage, "staging"
set :stage_dir, "deployment"

require "capistrano/ext/multistage"
require "sumodev_deploy"
require "forkcms_deploy"
require "forkcms_deploy/defaults"

# compile sass on deploy
after 'deploy:update_code', 'assets:precompile'
