load "deploy" if respond_to?(:namespace) # cap2 differentiator

# development information
set :client,  ""					# eg: "sumocoders"
set :project, ""					# eg: "site"

# production information
set :production_url, ""				# eg: "http://www.sumocoders.be"
set :production_account, ""			# eg: "sumocoders"
set :production_hostname, ""		# eg: "web02.crsolutions.be"
set :production_document_root, ""	# eg: "/home/#{production_account}/#{production_url.gsub("http://","")}"

# repo information
set :repository, ""					# eg: "git@github.com:sumocoders/forkcms.git"

# stages
set :stages, %w{production staging}
set :stage_dir, "deployment"

require "capistrano/ext/multistage"

require "forkcms_3_deploy"
require "forkcms_3_deploy/defaults"
require "sumodev_deploy"