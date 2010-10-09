# Appendix

## Complete configuration file sample


    all:
      MediatorPlugin:
        fs:          new_distant_fs
        media_root:  http://domain.tld:port/path/to/the/files
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
        metadata_exif:
          - Artist
          - Copyright
          - DateTimeOriginal
          - ExposureTime
          - FocalLength
          - FocalLengthIn35mmFilm
          - ISOSpeedRatings
          - Make
          - Model



