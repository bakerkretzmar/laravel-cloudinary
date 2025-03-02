Laravel Cloudinary
==================

[![Build Status](https://travis-ci.org/bakerkretzmar/laravel-cloudinary.svg?branch=master)](https://travis-ci.org/bakerkretzmar/laravel-cloudinary)
[![StyleCI](https://github.styleci.io/repos/201132752/shield?branch=master&style=flat)](https://github.styleci.io/repos/201132752)
<!-- [![Scrutinizer code quality](https://scrutinizer-ci.com/g/bakerkretzmar/laravel-mapbox/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bakerkretzmar/laravel-mapbox/?branch=master) -->
[![Latest stable version](https://img.shields.io/packagist/v/bakerkretzmar/laravel-cloudinary.svg?style=flat)](https://packagist.org/packages/bakerkretzmar/laravel-cloudinary)
[![Total downloads](https://img.shields.io/packagist/dt/bakerkretzmar/laravel-cloudinary.svg?style=flat)](https://packagist.org/packages/bakerkretzmar/laravel-cloudinary)
[![MIT license](https://img.shields.io/packagist/l/bakerkretzmar/laravel-cloudinary.svg?style=flat)](https://github.com/bakerkretzmar/laravel-cloudinary/blob/master/LICENSE)

[Cloudinary](https://cloudinary.com/) API wrapper for Laravel.

Installation
------------

```bash
composer require bakerkretzmar/laravel-mapbox
```

Configuration
-------------

Add the following to your `.env` file:

```bash
# Required
CLOUDINARY_CLOUD_NAME={your Cloudinary cloud name}
CLOUDINARY_API_KEY={your Cloudinary API key}
CLOUDINARY_API_SECRET={your Cloudinary API secret}

# Optional
CLOUDINARY_BASE_URL={custom base asset URL}
CLOUDINARY_SECURE_URL={secure custom base asset URL}
```

Optionally, you can publish the package’s config file:

```bash
php artisan vendor:publish --provider="Bakerkretzmar\LaravelCloudinary\LaravelCloudinaryServiceProvider"
```








## Usage

### upload()

```php
Cloudder::upload($filename, $publicId, array $options, array $tags);
```

with:

* `$filename`: path to the image you want to upload
* `$publicId`: the id you want your picture to have on Cloudinary, leave it null to have Cloudinary generate a random id.
* `$options`: options for your uploaded image, check the [Cloudinary documentation](http://cloudinary.com/documentation/php_image_upload#all_upload_options) to know more
* `$tags`: tags for your image

returns the `CloudinaryWrapper`.

### uploadVideo()

```php
Cloudder::uploadVideo($filename, $publicId, array $options, array $tags);
```

with:

* `$filename`: path to the video you want to upload
* `$publicId`: the id you want your video to have on Cloudinary, leave it null to have Cloudinary generate a random id.
* `$options`: options for your uploaded video, check the Cloudinary documentation to know more
* `$tags`: tags for your image

returns the `CloudinaryWrapper`.

### getPublicId()

```php
Cloudder::getPublicId()
```

returns the `public id` of the last uploaded resource.

### getResult()

```php
Cloudder::getResult()
```

returns the result of the last uploaded resource.

### show() + secureShow()

```php
Cloudder::show($publicId, array $options)
Cloudder::secureShow($publicId, array $options)
```

with:

* `$publicId`: public id of the resource to display
* `$options`: options for your uploaded resource, check the Cloudinary documentation to know more

returns the `url` of the picture on Cloudinary (https url if secureShow is used).

### showPrivateUrl()

```php
Cloudder::showPrivateUrl($publicId, $format, array $options)
```

with:

* `$publicId`: public id of the resource to display
* `$format`: format of the resource your want to display ('png', 'jpg'...)
* `$options`: options for your uploaded resource, check the Cloudinary documentation to know more

returns the `private url` of the picture on Cloudinary, expiring by default after an hour.

### rename()

```php
Cloudder::rename($publicId, $toPublicId, array $options)
```

with:

* `$publicId`: publicId of the resource to rename
* `$toPublicId`: new public id of the resource
* `$options`: options for your uploaded resource, check the cloudinary documentation to know more

renames the original picture with the `$toPublicId` id parameter.

### destroyImage() + delete()

```php
Cloudder::destroyImage($publicId, array $options)
Cloudder::delete($publicId, array $options)
```

with:

* `$publicId`: publicId of the resource to remove
* `$options`: options for the image to delete, check the cloudinary documentation to know more

removes image from Cloudinary.

### destroyImages()

```php
Cloudder::destroyImages(array $publicIds, array $options)
```

with:

* `$publicIds`: array of ids, identifying the pictures to remove
* `$options`: options for the images to delete, check the cloudinary documentation to know more

removes images from Cloudinary.

### addTag()

```php
Cloudder::addTag($tag, $publicIds, array $options)
```

with:

* `$tag`: tag to apply
* `$publicIds`: images to apply tag to
* `$options`: options for your uploaded resource, check the cloudinary documentation to know more

### removeTag()

```php
Cloudder::removeTag($tag, $publicIds, array $options)
```

with:

* `$tag`: tag to remove
* `$publicIds`: images to remove tag from
* `$options`: options for your uploaded image, check the Cloudinary documentation to know more

### createArchive()

```php
Cloudder::createArchive(array $options, $archiveName, $mode)
```

with:

* `$options`: options for your archive, like name, tag/prefix/public ids to select images
* `$archiveName`: name you want to give to your archive
* `$mode`: 'create' or 'download' ('create' will create an archive and returns a JSON response with the properties of the archive, 'download' will return the zip file for download)

creates a zip file on Cloudinary.

### downloadArchiveUrl()

```php
Cloudder::downloadArchiveUrl(array $options, $archiveName)
```

with:

* `$options`: options for your archive, like name, tag/prefix/public ids to select images
* `$archiveName`: name you want to give to your archive

returns a `download url` for the newly created archive on Cloudinary.

## Running tests

`phpunit`

## Example

You can find a working example in the repo [cloudder-l5-example](https://github.com/jrm2k6/cloudder-l5-sample-project)






Credits
-------

Based on Jeremy Dagorn’s [`cloudder`](https://github.com/jrm2k6/cloudder).
