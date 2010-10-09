# Installation and configuration

## Minimum Requirements

Mediator has several requirements:

*   PHP >= 5.2.4+, that you can get from [php.net](http://www.php.net/),
*   [Symfony](http://www.symfony-project.org/) 1.2+,
*   Symfony's [Doctrine](http://www.doctrine-project.org/) plugin, which is bundled in the default Symfony's distribution,
*   and eventually some PHP modules or external binaries like PHP_GD, Imagemagick, etc.

## Basic install

Installing Mediator can be done in some simple steps:

1.   download an install Symfony.
2.   install the plugin:


         $ ./symfony plugin:install MediatorPlugin


3.   rebuild the model, the forms and the filters

## Advanced configuration

Mediator offers advanced configuration options, which allow to take the best out of the plugin

### Choosing the file storage

For performance, volume or reliability considerations, you may want to store the files on a separate storage. This would allow a larger and eventually cheaper media storage volume, better performance, and an improved security in case of a failure of the server's filesystem.


### Asynchronous thumbnail creation

The cleverFilesystemPlugin comes with a filesystem watching task which, when runned, notifies events describing the filesystem modifications :

*   filesystem.folder.
*   filesystem.folder.remove
*   filesystem.file.add
*   filesystem.file.change
*   filesystem.file.remove

You may want to deactivate the on-the-fly variations creation by switching the `app_MediatorPlugin_asynchronous` configuration directive to `true` :

    all:
      MediatorPlugin:
        asynchronous:    true

Then, the `filesystem:watch` task must be regularly runned in order to detect a file creation or deletion. When notified with filesystem events, the media library will generate the appropriate variations.

    $ ./symfony filesystem:watch FILESYSTEM_NAME


>**CAUTION**
> Please take care that some filesystems do not provide important informations that help optimize the filesystem watching process. For instance, the `ftp` filesystem does not offer the possibility to get the last modification date of a directory, and therefore each time the `filesystem:watch` task is runned, all the filesystem tree will get analyzed and compared to the previous known version.


### Using optimized adapters

The media handlers rely on adapters to effectively perform operations on the media: creation, deletion, frame extraction, resizing, etc. For instance, the `image` media handler relies either on the ImageMagick` or the `GD` adapters, depending on which one is installed on the system. Using efficient adapters will help improve the performance of the media manager.


## alternative installation modes

### Sandbox download

### Subversion dependancy installation