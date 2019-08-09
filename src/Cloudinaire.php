<?php

namespace Bakerkretzmar\LaravelCloudinary;

use Cloudinary;

class Cloudinaire
{
    /**
     * Cloudinary library.
     *
     * @var  \Cloudinary
     */
    protected $cloudinary;

    /**
     * Cloudinary uploader.
     *
     * @var  \Cloudinary\Uploader
     */
    protected $uploader;

    /**
     * Cloudinary API.
     *
     * @var  \Cloudinary\Api
     */
    protected $api;

    /**
     * LaravelCloudinary config.
     *
     * @var  array
     */
    protected $config;

    /**
     * Uploaded result.
     *
     * @var array
     */
    protected $uploadedResult;

    /**
     * Create a new instance of the Cloudinary API wrapper.
     *
     * @param  array  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        $this->cloudinary = new Cloudinary;
        $this->uploader = new Cloudinary\Uploader;
        $this->api = new Cloudinary\Api;

        $this->cloudinary->config([
            'cloud_name' => $this->config['cloud_name'],
            'api_key'    => $this->config['key'],
            'api_secret' => $this->config['secret'],
        ]);
    }


    /**
     * Create a fake instance of the Cloudinary API wrapper for testing.
     */
    public static function fake($cloudinary, $uploader, $api)
    {
        $fake = new static(config('laravel-cloudinary'));

        $fake->cloudinary = $cloudinary;
        $fake->uploader = $uploader;
        $fake->api = $api;

        return $fake;
    }

    /**
     * Upload image to cloud.
     *
     * @param  mixed $source
     * @param  string $public_id
     * @param  array $options
     * @param  array $tags
     * @return Cloudinaire
     */
    public function upload($source, string $public_id = null, array $options = [], array $tags = [])
    {
        $this->uploadedResult = $this->uploader->upload($source, array_merge([
            'public_id' => $public_id,
            'tags' => $tags,
        ], $options));

        return $this;
    }

    /**
     * Upload image to cloud.
     *
     * @param  mixed $source
     * @param  string $publicId
     * @param  array $uploadPresets
     * @param  array $uploadOptions
     * @param  array $tags
     * @return Cloudinaire
     */
    public function unsignedUpload($source, $publicId = null, $uploadPresets = [],
        $uploadOptions = [], $tags = [])
    {
        $defaults = array(
            'public_id' => null,
            'tags'      => []
        );

        $options = array_merge($defaults, array(
            'public_id' => $publicId,
            'tags'      => $tags,
        ));

        $options = array_merge($options, $uploadOptions);
        $this->uploadedResult = $this->uploader->unsigned_upload($source, $uploadPresets, $options);

        return $this;
    }

    /**
     * Upload video to cloud.
     *
     * @param  mixed $source
     * @param  string $publicId
     * @param  array $uploadOptions
     * @param  array $tags
     * @return Cloudinaire
    */
    public function uploadVideo($source, $publicId = null, $uploadOptions = [], $tags = [])
    {
        $options = array_merge($uploadOptions, ['resource_type' => 'video']);
        return $this->upload($source, $publicId,  $options, $tags);
    }

    /**
     * Uploaded result.
     *
     * @return array
     */
    public function getResult()
    {
        return $this->uploadedResult;
    }

    /**
     * Uploaded public ID.
     *
     * @return string
     */
    public function getPublicId()
    {
        return $this->uploadedResult['public_id'];
    }

    /**
     * Generate resource URL.
     *
     * @param   string  $public_id
     * @param   array   $options
     * @return  string
     */
    public function show(string $public_id, array $options = [])
    {
        return $this->cloudinary->cloudinary_url($public_id, $options);
    }

    /**
     * Generate secure resource URL.
     *
     * @param   string  $public_id
     * @param   array   $options
     * @return  string
     */
    public function secureShow(string $public_id, array $options = [])
    {
        $options = array_merge(['secure' => true], $options);

        return $this->cloudinary->cloudinary_url($public_id, $options);
    }

    /**
     * @see  privateDownloadUrl
     */
    public function showPrivateUrl(string $public_id, string $format, array $options = [])
    {
        return $this->privateDownloadUrl($public_id, $format, $options);
    }

    /**
     * Generate private resource URL.
     *
     * @param   string  $public_id
     * @param   string  $format
     * @param   array   $options
     * @return  string
     */
    public function privateDownloadUrl(string $public_id, string $format, array $options = [])
    {
        return $this->cloudinary->private_download_url($public_id, $format, $options);
    }

    /**
     * Rename public ID.
     *
     * @param   string  $public_id
     * @param   string  $new_public_id
     * @param   array   $options
     * @return  array
     */
    public function rename(string $public_id, string $new_public_id, array $options = [])
    {
        try {
            return $this->uploader->rename($public_id, $new_public_id, $options);
        } catch (\Exception $e) {
            //
        }

        return false;
    }

    /**
     * @see  destroy
     */
    public function destroyImage(string $public_id, array $options = [])
    {
        return $this->destroy($public_id, $options);
    }

    /**
     * Delete resource from Cloudinary.
     *
     * @param   string  $public_id
     * @param   array   $options
     * @return  array
     */
    public function destroy(string $public_id, array $options = [])
    {
        return $this->uploader->destroy($public_id, $options);
    }

    /**
     * Restore a resource
     *
     * @param  array  $publicIds
     * @param  array  $options
     * @return null
     */
    // public function restore($publicIds = [], $options = [])
    // {
    //     return $this->api->restore($publicIds, $options);
    // }

    /**
     * Alias for deleteResources
     *
     * @param  array $publicIds
     * @param  array $options
     * @return null
     */
    // public function destroyImages($publicIds, $options = [])
    // {
    //     return $this->deleteResources($publicIds, $options);
    // }

    /**
     * Destroy images from Cloudinary
     *
     * @param  array $publicIds
     * @param  array $options
     * @return null
     */
    // public function deleteResources($publicIds, $options = [])
    // {
    //     return $this->api->delete_resources($publicIds, $options);
    // }

    /**
     * Destroy a resource by its prefix
     *
     * @param  string $prefix
     * @param  array  $options
     * @return null
     */
    // public function deleteResourcesByPrefix($prefix, $options=[])
    // {
    //     return $this->api->delete_resources_by_prefix($prefix, $options);
    // }

    /**
     * Destroy all resources from Cloudinary
     *
     * @param  array $options
     * @return null
     */
    // public function deleteAllResources($options = [])
    // {
    //     return $this->api->delete_all_resources($options);
    // }

    /**
     * Delete all resources from one tag
     *
     * @param  string $tag
     * @param  array  $options
     * @return null
     */
    // public function deleteResourcesByTag($tag, $options=[])
    // {
    //     return $this->api->delete_resources_by_tag($tag, $options);
    // }

    /**
     * Delete transformed images by IDs
     *
     * @param  array  $publicIds
     * @param  array  $options
     * @return null
     */
    // public function deleteDerivedResources($publicIds = [], $options=[])
    // {
    //     return $this->api->delete_derived_resources($publicIds, $options);
    // }

    /**
     * Alias of destroy.
     *
     * @return array
     */
    // public function delete($publicId, $options = [])
    // {
    //     $response = $this->destroy($publicId, $options);

    //     return (boolean) ($response['result'] == 'ok');
    // }

    /**
     * Add a tag to the given images.
     * @see  https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     *
     * @param  string  $tag
     * @param  array   $public_ids
     * @param  array   $options
     */
    public function addTag(string $tag, array $public_ids = [], array $options = [])
    {
        return (array) $this->uploader->add_tag($tag, $public_ids, $options);
    }

    /**
     * Remove a tag from the given images.
     * @see  https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     *
     * @param  string  $tag
     * @param  array   $public_ids
     * @param  array   $options
     */
    public function removeTag(string $tag, array $public_ids = [], array $options = [])
    {
        return (array) $this->uploader->remove_tag($tag, $public_ids, $options);
    }

    /**
     * Remove all tags from the given images.
     * @see  https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     *
     * @param  array   $public_ids
     * @param  array   $options
     */
    public function removeAllTags(array $public_ids = [], array $options = [])
    {
        return (array) $this->uploader->remove_all_tags($public_ids, $options);
    }

    /**
     * Replace all tags for the given images.
     * @see  https://cloudinary.com/documentation/image_upload_api_reference#tags_method
     *
     * @param  string  $tag
     * @param  array   $public_ids
     * @param  array   $options
     */
    public function replaceTag(string $tag, array $public_ids = [], array $options = [])
    {
        return (array) $this->uploader->replace_tag($tag, $public_ids, $options);
    }

    /**
     * Create a zip file containing images matching options.
     *
     * @param array  $options
     * @param string $archiveName
     * @param string $mode
     */
    // public function createArchive($options = [], $nameArchive = null, $mode = 'create')
    // {
    //     $options = array_merge($options, ['target_public_id' => $nameArchive, 'mode' => $mode]);
    //     return $this->uploader->create_archive($options);
    // }

    /**
     * Download a zip file containing images matching options.
     *
     * @param array  $options
     * @param string $archiveName
     * @param string $mode
     */
    // public function downloadArchiveUrl($options = [], $nameArchive = null)
    // {
    //     $options = array_merge($options, ['target_public_id' => $nameArchive]);
    //     return $this->cloudinary->download_archive_url($options);
    // }


    /**
     * Show Resources
     *
     * @param  array  $options
     * @return array
     */
    // public function resources($options = [])
    // {
    //     return $this->api->resources($options);
    // }

    /**
     * Show Resources by id
     *
     * @param  array $publicIds
     * @param  array $options
     * @return array
     */
    // public function resourcesByIds($publicIds, $options = [])
    // {
    //     return $this->api->resources_by_ids($publicIds, $options);
    // }

    /**
     * Show Resources by tag name
     *
     * @param  string  $tag
     * @return array
     */
    // public function resourcesByTag($tag, $options = [])
    // {
    //     return $this->api->resources_by_tag($tag, $options);
    // }

    /**
     * Show Resources by moderation status
     *
     * @param  string  $kind
     * @param  string  $status
     * @return array
     */
    // public function resourcesByModeration($kind, $status, $options = [])
    // {
    //     return $this->api->resources_by_moderation($kind, $status, $options);
    // }

    /**
     * Display tags list
     *
     * @param  array  $options
     * @return array
     */
    // public function tags($options = [])
    // {
    //     return $this->api->tags($options);
    // }

    /**
     * Display a resource
     *
     * @param  string  $publicId
     * @param  array  $options
     * @return array
     */
    // public function resource($publicId, $options = [])
    // {
    //     return $this->api->resource($publicId, $options);
    // }

    /**
     * Updates a resource
     *
     * @param  string  $publicId
     * @param  array  $options
     * @return array
     */
    // public function update($publicId, $options = [])
    // {
    //     return $this->api->update($publicId, $options);
    // }

    /**
     * List transformations
     *
     * @param  array  $options
     * @return array
     */
    // public function transformations($options = [])
    // {
    //     return $this->api->transformations($options);
    // }

    /**
     * List single transformation
     *
     * @param  string $transformation
     * @param  array  $options
     * @return array
     */
    // public function transformation($transformation, $options=[])
    // {
    //     return $this->api->transformation($transformation, $options);
    // }

    /**
     * Delete single transformation
     *
     * @param  string $transformation
     * @param  array  $options
     * @return array
     */
    // public function deleteTransformation($transformation, $options=[])
    // {
    //     return $this->api->delete_transformation($transformation, $options);
    // }

    /**
     * Update single transformation
     *
     * @param  string $transformation
     * @param  array  $updates
     * @param  array  $options
     * @return array
     */
    // public function updateTransformation($transformation, $updates = [], $options=[])
    // {
    //     return $this->api->update_transformation($transformation, $updates, $options);
    // }

    /**
     * Create transformation
     * @param  string $name
     * @param  string $definition
     * @param  array  $options
     * @return array
     */
    // public function createTransformation($name, $definition, $options=[])
    // {
    //     return $this->api->create_transformation($name, $definition, $options);
    // }

    /**
     * List Upload Mappings
     *
     * @param  array  $options
     * @return array
     */
    // public function uploadMappings($options=[])
    // {
    //     return $this->api->upload_mappings($options);
    // }

    /**
     * Get upload mapping
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function uploadMapping($name, $options=[])
    // {
    //     return $this->api->upload_mapping($name, $options);
    // }

    /**
     * Create upload mapping
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function createUploadMapping($name, $options=[])
    // {
    //     return $this->api->create_upload_mapping($name, $options);
    // }

    /**
     * Delete upload mapping
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function deleteUploadMapping($name, $options=[])
    // {
    //     return $this->api->delete_upload_mapping($name, $options);
    // }

    /**
     * Update upload mapping
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function updateUploadMapping($name, $options=[])
    // {
    //     return $this->api->update_upload_mapping($name, $options);
    // }

    /**
     * List Upload Presets
     *
     * @param  array  $options
     * @return array
     */
    // public function uploadPresets($options=[])
    // {
    //     return $this->api->upload_presets($options);
    // }

    /**
     * Get upload mapping
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function uploadPreset($name, $options=[])
    // {
    //     return $this->api->upload_preset($name, $options);
    // }

    /**
     * Create upload preset
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function createUploadPreset($name, $options=[])
    // {
    //     return $this->api->create_upload_preset($name, $options);
    // }

    /**
     * Delete upload preset
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function deleteUploadPreset($name, $options=[])
    // {
    //     return $this->api->delete_upload_preset($name, $options);
    // }

    /**
     * Update upload preset
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function updateUploadPreset($name, $options=[])
    // {
    //     return $this->api->update_upload_preset($name, $options);
    // }

    /**
     * List Root folders
     *
     * @param  array  $options
     * @return array
     */
    // public function rootFolders($options=[])
    // {
    //     return $this->api->root_folders($options);
    // }

    /**
     * List subfolders
     *
     * @param  string $name
     * @param  array  $options
     * @return array
     */
    // public function subfolders($name, $options=[])
    // {
    //     return $this->api->subfolders($name, $options);
    // }

    /**
     * Get account usage details.
     *
     * @param   array  $options
     * @return  array
     */
    public function usage(array $options = []): array
    {
        return (array) $this->api->usage($options);
    }

    /**
     * Ping the Cloudinary service.
     *
     * @param   array  $options
     * @return  array
     */
    public function ping(array $options = []): array
    {
        return (array) $this->api->ping($options);
    }
}
