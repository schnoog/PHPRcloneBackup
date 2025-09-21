# PHPRcloneBackup

## A small script to make recurring backups of big data dumps faster

### Motivation and idea
#### Motivation
I'm a data hoarder. My homeserver/NAS has 300GB of photos, 100s of GBs of videos, an immich server to make the videos available and so on.  
I have a cloud drive to which I backup everything on a "regular" base.  
"Regular" because it simply takes over an hour for rclone to go though the directory tree and check file for file if something changed and it needs to be re-uploaded. And not that full of patience....      
I identified some characteristcs of my directories and thought there must be a better way.  
And I think I found one.
#### Idea
OK, the idea is rather simple:  
Let's say we have the following directory with a ton of documents unsorted also, in directories.  
Now we don't simply copy/sync the root directoy /documents but look when the last file within this directory was changed.
If that was before that last backup of the diretory content happened, no need to do it again.  
Since files are also located in the /documents itself, I use 'sublevel' = 0

```
/documents
./Income.xls  
./Prove_that_PI_is_exactly_3.pdf  
├── letters  
│   ├── employer    
│   ├── insurances  
│   ├───── car  
│   ├───── house
├── flamewars  
│   ├── employer    
│   ├── other half  
```
OK, that's the easy part. Now I have a second directory. With all my records, photo and smartphone cam, 100s of GB.  
The previous approach would work, but as soon as I add a single new file to the directory (or any of its' subdirectories)  
it would go again through all the files and directories.  
I don't have any files in the /images_and_videos directory itself but only in it's subdirectories.  
So I use 'sublevel' = 1  
That means it checks for each year if a change happened instead of the root directory and udapted only the directories which actually changed.

```
/images_and_videos
├── 2022  
│   ├── 01  
│   ├── 03  
│   ├── 04  
│   ├── 06  
│   ├── 07  
│   ├── 08  
│   └── 10  
├── 2023  
│   ├── 01  
│   ├── 09  
│   ├── 10  
│   └── 11  
├── 2023  
│   ├── 01  
│   ├── 09  
│   ├── 10  
└── 2025   
    ├── 02  
    ├── 03  
    └── 07  

```

### Requirements
  - rclone installed and at least on endpoint configured
  - php-cli

### Usage
First you have to download the files from this package and place it where ever you want.  
Then it's about to copy the config.php.dist to config.php and change it to fit your requirements.  

Then you can start it in the terminal by running  
```
php backup.php  
```
The first run takes as long as a rclone sync/copy would take since the "database" which holds the date/time of the last backup for the directiers managed.

### But why php?
Quite easy: I really do like php. Easy to write and read, no speial requirements, no libs just pure php.

### Was it worth it?
Absolutely. To write (and test) this took me maybe 4 hours.  
A standards rclone copy (or sync) on my to be backed up directories with my Cloud endpoint takes ~3 hours (f nothing changed!)  
After the first run, which as I explained took like the regular run roughly 3 hours and my "database" was initiated, each further run takes rather seconds than minutes.  
I use sublevel = 1 quite often, so there are 1000s of directories for which the timestamps are checked.
 

