## SSH settings
set :client, "???"
set :host, "ssh???.webhosting.be"

## Production/Staging URL's
set :production_url, "https://production.???.be" # or www.
set :staging_url, "https://staging.???.be"

## Only set once
set :domain_name, "???.be"
set :repo_url, "git@bitbucket.org:siesqo/???.git"

### DO NOT EDIT BELOW ###
set :deploytag_utc, false
set :deploytag_time_format, "%Y%m%d-%H%M%S"
set :files_dir, %w(src/Frontend/Files)
