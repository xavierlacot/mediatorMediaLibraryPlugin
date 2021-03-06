# Media types
## What is a media type

In the language of Mediator, a *media type* is a way to manage a type of file. "managing" a type of file means:

*   be able to recognize a file as belonging to a media type
*   be able to create/update/delete the variations when the file is created/updated/deleted
*   be able to render the file or its variations for a web display
*   (eventually) be able to extract metadatas from the file
*   (eventually) be able to perform extra-operations on the file (for instance, "crop" for the "image" media type)

### Media type handlers

A media type is managed by a *media type handler*, which is responsible for all these operations. You may want to create your own media handler. Imagine, for example, that your application must handle a zip file containing images. This could be represented  as a "mediaGallery" media type, and you just should have to write a media handler for managing it.

### Adapters


## Supported media types

Mediator comes bundled with several handlers:

*   an "image" handler, which simply generates the thumbnails of the file as described in the configuration file
*   a PDF handler, which extracts one frame for each page of the document,
*   a text handler, which is able to count the number of pages of the document

Several other media handler could be created

## Variations

A *variation* is a different representation of the original version of a media, automatically generated by Mediator. For instance, the variations of an image are the different thumbnails size that have been defined in the configuration file.

### Defining variations

Defining variations can be done in the Mediator configuration file, under the "variations" key:

    all:
      MediatorPlugin:
        variations:
          original:
            directory:  original
          large:
            directory:  large
            overwrite:  true
            media_types:
              image:
                width:  670
                height: 670
          medium:
            directory:  medium
              image:
                width:  200
                height: 200
                crop:   true

Each variation proposes various options. For a `myVariation` variation, here are the possible options:

*   directory: this required key gives the name of the directory storing all the variations of type `myVariation`
*   overwrite: whether or existing variations should be overwritten when the file is updated / modified (default : true)
*   media_types: this directive lists some variables associated to media types, so that the media handler knows what to do for each media type in this variation. If the key for a given media type does not exist, no variation will be generated for this media type.

The `original` variation is a special directory, in which the original media is stored, without modification.

The filesystem structure of the media gallery is directly related to the variations definition. For instance:

    /medias/
     |
     + orignal/
     |    + 2009/
     |    |    + sflive2009.png
     |    |    + ponny.jpg
     |    |    + hadopute-watches-you.jpg
     |    + 2008/
     |    |    + php-white-paper.pdf
     |    |    + README.txt
     |
     + large/
     |    + 2009/
     |    |    + sflive2009.png
     |    |    + ponny.jpg
     |    |    + hadopute-watches-you.jpg
     |    + 2008/
     |    |    + php-white-paper-1.png
     |    |    + php-white-paper-2.png
     |    |    + php-white-paper-3.png
     |    |    + README-1.png
     |    |    + README-2.png
     |
     + medium/
     |    + 2009/
     |    |    + sflive2009.png
     |    |    + ponny.jpg
     |    |    + hadopute-watches-you.jpg
     |    + 2008/
     |    |    + php-white-paper-1.png
     |    |    + php-white-paper-2.png
     |    |    + php-white-paper-3.png
     |    |    + README-1.png
     |    |    + README-2.png

### Filesystems support

As the storage of the media library items is abstract, it is necessary to have a local cache of the files before these get transformed. Therefore,

## How to implement a new media type


