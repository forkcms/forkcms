namespace :siesqo do
  namespace :files do
    desc 'Uploads the local files to the remote server'
    task :put do
      on roles(:web) do
        fetch(:files_dir).each do |path|
          upload! path, "#{current_path}/#{File.dirname(path)}", recursive: true
        end
      end
    end

    desc 'Downloads the remote files to the local instance'
    task :get do
      on roles(:web) do
        fetch(:files_dir).each do |path|
          download! "#{current_path}/#{path}", File.dirname(path), recursive: true
        end
      end
    end
  end
end
