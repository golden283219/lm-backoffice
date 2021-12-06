# Installation
Clone Repo
```sh
$ git clone git@gitlab.com:lookmovie-websites/lm-backoffice.git
```

Install required assets
```sh
$ npm install && composer install
```
Copy and edit .env
```sh
$ cp .env.sample .env
```

# CONSOLE COMMANDS

Run command to generate collections.
```sh
$ console/yii collections/create 
```


Run command to generate featured movies
```sh
$ console/yii featured-movies/update
```

# Metadata Commands
```sh
# Add all movies to metadata download queue that don't have title/poster/backdrop etc..
$ php console/yii metadata-download-queue-manager/scrape-movies-meta 
# Add all episodes to metadata download queue that don't have title/poster/backdrop etc..
$ php console/yii metadata-download-queue-manager/scrape-episodes-meta 
# Scrape all actors metadata
$ php console/yii metadata-download-queue-manager/scrape-actor
```

# Standart commands of metadata download queue
```sh
$ php console metadata-download-queue
$ php console metadata-download-queue/clear
# Run all metadata download queue jobs 
$ php console metadata-download-queue/exec
$ php console metadata-download-queue/info
# Listen for updates in jobs queue, and execute new
$ php console metadata-download-queue/listen
$ php console metadata-download-queue/remove
$ php console metadata-download-queue/run
```

# IMDB Datasets Update Commands
Use `imdb` param for ratings to update dataset database and `site` param for update site database
```sh
# Update All Datasets
$ php console/yii imdb-download-queue/update-datasets
# Update IMDB Title Basics
$ php console/yii imdb-download-queue/update-datasets title-basics
# Update Title Ratings Dataset
$ php console/yii imdb-download-queue/update-datasets title-ratings {imdb|site}
# Update Title Akas Dataset
$ php console/yii imdb-download-queue/update-datasets title-akas
# Update Title Crew Dataset
$ php console/yii imdb-download-queue/update-datasets title-crew
# Update Title Episode Dataset
$ php console/yii imdb-download-queue/update-datasets title-episode
# Update Title Principal Dataset
$ php console/yii imdb-download-queue/update-datasets title-principal
# Update Name Basics Dataset
$ php console/yii imdb-download-queue/update-datasets name-basics
```

# Actors Update Commands
```sh
# Update all actors avatars 
$ php console/yii people/update-avatar
# Update actor avatar 
$ php console/yii people/update-avatar {imdb_id}|number
# Update all actors biographies 
$ php console/yii people/update-biography
# Update actor biography 
$ php console/yii people/update-biography {imdb_id}|number
# Update actor name 
$ php console/yii people/update-name {imdb_id}|number
# Update all actors with TMDB API 
$ php console/yii people/update-biography
# Update actor with TMDB 
$ php console/yii people/update-biography {imdb_id}|number
```

# Work Queues
