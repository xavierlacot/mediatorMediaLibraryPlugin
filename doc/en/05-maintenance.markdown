# Maintenance
## Maintenance tasks

Mediator adds two tasks to the Symfony command-line tool:

*    The `media:generate` will generate all the variations of the files in the media library, and replace actual variations if already created. This costly process might be useful if you add a new variation definition in the configuration file, after you have used Mediator for a while.
*    The `media:initialize` task creates the root directory of the media library, the subfolders of the variations, and initializes the nested set tree in the database.

## Moving the files to an other filesystem

As your media library gets bigger, you may want to move its content to an other storage solution. This can be achieved rather easily:

1.    disable the access to the media library management module, and disable all the process which might write in the media library
2.    move the files to their new location - eventually a distant filesystem, as long as this filesystem type is supported by the cleverFilesystemPlugin
3.    change the configuration of the media library to use this new filesystem:

        all:
          MediatorPlugin:
            fs:          new_distant_fs
            media_root:  http://domain.tld:port/path/to/the/files

4.    re-open the access to the services

## Files MD5 sum

The model contains a column named `md5sum`, in which a MD5 sum of the content of the file is stored. The only goal of this column is for backup purpose: if the file storage gets corrupted and you loose part or all of the media library content, the MD5 sum of the files will help rebuild the missing parts (assuming, of course, that the files are stored in an other place, eventually as a plain non-arborescent folder).

