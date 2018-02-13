set :client,  "$client"
set :project, "$project"
set :repo_url, "$repo-url"
set :production_url, "$production-url"

### DO NOT EDIT BELOW ###
set :application, "#{fetch :project}"

set :deploytag_utc, false
set :deploytag_time_format, "%Y%m%d-%H%M%S"

set :files_dir, %w(src/Frontend/Files)
