# Introduction

## What is Mediator?

Mediator is a media management solution written in PHP5 and distributed as a plugin for the [Symfony](http://www.symfony-project.org/) framework.

## Key Concepts

The items stored in a Mediator library are organized in folders within a tree. Each item is called a *media*, while the folders are simply called *folders*. Physically, the files are stored on a *filesystem*, which access is provided by the [cleverFilesystem](http://www.symfony-project.org/plugins/cleverFilesystemPlugin) plugin. Both medias and folders can have *metadata*, some of which might be automatically extracted when the media is added to the library.

Each media type is managed by a *media handler*, which drives in detail all the operations that can be performed on this type of file. For instance, an Image handler will create simple thumbnails whn an image is added to the media library, and it will unlink all the thumbnails when the image is deleted from the library.

For this, the media handlers use a set of *adapters* :

*   some of them to open and access the file. For example, Mediator bundles a Imagemagick and a GD adapter, which allow to open and transform images.
*   some for writing the file to the filesystem. These adapters are included in the cleverFilesystem Plugin.

The arborescent structure of the tree is stored in the database using a nested tree. As for the moment, there can only be one tree root.