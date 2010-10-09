# Programmable interface

Even if you'll like adding media files in the library using the bundled web interface, you may also like adding files right from a program. That would be the case if you offer an upload field to you users, and want to store the unloaded content right in the media library. In that goal, Mediator offers an API which allows a programmatic management of the media gallery.


## Directories management

Common operations can be performed on directories using the API:

    [php]
    // retrieve the root of the media library
    $root = Doctrine::getTable('CcMediaFolder')->fetchRoot();

    // create a new directory
    $folder = new ccMediaFolder();
    $fields = array(
      'name'       => '2009',
      'created_by' => $sf_user->getId(),
      'updated_by' => $sf_user->getId(),
      'parent'     => $root
    );
    $folder->update($fields);
    $folder->save();

    // retrieve a directory from its path
    $folder = Doctrine::getTable('CcMediaFolder')->getByPath('2007/old/directory');
    $folder->delete();

>**CAUTION**
> Deleting a directory will also recursively delete all its content, and all the associated variations. Therefore, please take care that:
> 1.   it can take long, if the directory contains a lot of nested subfolders and medias
> 2.   calling the `delete()` method on the root library will remove all the content of the library - a sort of equivalent to `# rm -Rf`, so take care!

## Files management

Similar operations can be performed on files:

    [php]
    // retrieve a directory from its path
    $folder = Doctrine::getTable('CcMediaFolder')->getByPath('2008/12');

    // create a new media
    $image = new ccMedia();
    $fields = array(
      'cc_media_folder'     => $folder,
      'source'              => '/path/to/an/existing/file.png',
      'filename'            => 'my-new-filename.png',
      'updated_by'          => $sf_user->getId(),
      'created_by'          => $sf_user->getId(),
    );
    $image->update($fields);
    $image->save();

Some parameters only are required:

*    `cc_media_folder`, which gives the name of the folder in which the file will be stored
*    `updated_by` : the id of the sfGuardUser who created the file - which means that you must bind the file uploads either:
   *    to a fixed "admin" user. In that case, you would write:

          [php]
          $uploader_id = Doctrine::getTable('CcMedia')->retrieveByUsername('root')->getId();


   *    to the actually logged in user, if you use sfGuard as the support for your frontend sessions

And the same for file deletion:

     [php]
     // retrieve a file from its path
     $media = Doctrine::getTable('CcMedia')->getByFilename('2008/12/my-new-filename.png');
     $media->delete();

When deleting a file, all of its variations will also get deleted.

