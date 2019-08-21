# PodcastDownloader
Simple podcast downloader for my personal podcast 

Juste made this to download some of the podcasts I listen to during running and trainning. 
(Writing this I discover that Itunes locally downloads all the podcasts in a folder .... anyway that was funny to do it).

# How it works: 
1. Parse the page containning the rss feed to catch the last links for the podcasts.
2. Download and verifies if the rss feed is valid
3. Parse the rss feed to get the informations and links
4. create folders if needed and download content

# TODO : 
- Find a new way to retrieve RSS feed than manually. Some servers redirect the links so you have to investigate to see what the good one.
- Thus, give te possibility to reach any possible podcast cool be usefull
- Upload directly to a dropbox (or equivalent) could be also usefull to not take too much space locally 
- add a functionning sleep option to avoid possible server blacklist
- add more options to how many podcasts you want to download, start from the beginning or the end or download a specific range.
- add a button to pause or cancel the download
