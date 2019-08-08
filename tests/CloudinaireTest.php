<?php

namespace Bakerkretzmar\LaravelCloudinary\Tests;

use Bakerkretzmar\LaravelCloudinary\Cloudinaire;

use PHPUnit\Framework\TestCase;

use Mockery;

class CloudinaireTest extends TestCase
{
    protected $cloudinaire;

    protected function setUp(): void
    {
        $this->config = Mockery::mock('Illuminate\Config\Repository');
        $this->cloudinary = Mockery::mock('Cloudinary');
        $this->uploader = Mockery::mock('Cloudinary\Uploader');
        $this->api = Mockery::mock('Cloudinary\Api');

        $this->config->shouldReceive('get')->once()->with('laravel-cloudinary.cloud_name')->andReturn('cloud_name');
        $this->config->shouldReceive('get')->once()->with('laravel-cloudinary.key')->andReturn('key');
        $this->config->shouldReceive('get')->once()->with('laravel-cloudinary.secret')->andReturn('secret');

        $this->cloudinary->shouldReceive('config')->once();

        $this->cloudinaire = new Cloudinaire($this->config, $this->cloudinary, $this->uploader, $this->api);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function set_upload_result_after_uploading_image()
    {
        $this->uploader->expects()
            ->upload('file_name', ['public_id' => null, 'tags' => []])
            ->andReturn(['public_id' => '123456789']);

        $this->cloudinaire->upload('file_name');

        $result = $this->cloudinaire->getResult();
        $this->assertEquals(['public_id' => '123456789'], $result);
    }

    /** @test */
    public function it_should_set_uploaded_result_when_uploading_picture_unsigned()
    {
        // given
        $filename = 'filename';
        $defaults_options = [
            'public_id' => null,
            'tags'      => array()
        ];

        $upload_presets = [
            'param' => 1
        ];

        $expected_result = ['public_id' => '123456789'];

        $this->uploader->shouldReceive('unsigned_upload')->once()
            ->with($filename, $upload_presets, $defaults_options)
            ->andReturn($expected_result);

        // when
        $this->cloudinaire->unsignedUpload($filename, null, $upload_presets);

        // then
        $result = $this->cloudinaire->getResult();
        $this->assertEquals($expected_result, $result);
    }

    /** @test */
    public function it_should_set_uploaded_result_when_uploading_private_picture()
    {
        // given
        $filename = 'filename';
        $defaults_options = [
            'public_id' => null,
            'tags'      => array(),
            'type'      => 'private'
        ];

        $expected_result = ['public_id' => '123456789'];

        $this->uploader->shouldReceive('upload')->once()->with($filename, $defaults_options)->andReturn($expected_result);

        // when
        $this->cloudinaire->upload($filename, null, ['type' => 'private']);

        // then
        $result = $this->cloudinaire->getResult();
        $this->assertEquals($expected_result, $result);
    }

    /** @test */
    public function it_should_returns_image_url_when_calling_show()
    {
        // given
        $filename = 'filename';
        $this->config->shouldReceive('get')->with('laravel-cloudinary.scaling')->once()->andReturn(array());
        $this->cloudinary->shouldReceive('cloudinary_url')->once()->with($filename, array());

        // when
        $this->cloudinaire->show($filename);
    }

    /** @test */
    public function it_should_returns_https_image_url_when_calling_secure_show()
    {
        // given
        $filename = 'filename';
        $this->config->shouldReceive('get')->with('laravel-cloudinary.scaling')->once()->andReturn(array());
        $this->cloudinary->shouldReceive('cloudinary_url')->once()->with($filename, ['secure' => true]);

        // when
        $this->cloudinaire->secureShow($filename);
    }

    /** @test */
    public function it_should_returns_image_url_when_calling_show_private_url()
    {
        // given
        $filename = 'filename';
        $this->cloudinary->shouldReceive('private_download_url')->once()->with($filename, 'png', array());

        // when
        $this->cloudinaire->showPrivateUrl($filename, 'png');
    }

    /** @test */
    public function it_should_returns_image_url_when_calling_private_download_url()
    {
        // given
        $filename = 'filename';
        $this->cloudinary->shouldReceive('private_download_url')->once()->with($filename, 'png', array());

        // when
        $this->cloudinaire->privateDownloadUrl($filename, 'png');
    }

    /** @test */
    public function it_should_call_api_rename_when_calling_rename()
    {
        // given
        $from = 'from';
        $to = 'to';

        $this->uploader->shouldReceive('rename')->with($from, $to, array())->once();

        // when
        $this->cloudinaire->rename($from, $to);
    }

    /** @test */
    public function it_should_call_api_destroy_when_calling_destroy_image()
    {
        // given
        $pid = 'pid';
        $this->uploader->shouldReceive('destroy')->with($pid, array())->once();

        // when
        $this->cloudinaire->destroyImage($pid);
    }

    /** @test */
    public function it_should_call_api_destroy_when_calling_destroy()
    {
        // given
        $pid = 'pid';
        $this->uploader->shouldReceive('destroy')->with($pid, array())->once();

        // when
        $this->cloudinaire->destroy($pid);
    }

    /** @test */
    public function verify_delete_alias_returns_boolean()
    {
        // given
        $pid = 'pid';
        $this->uploader->shouldReceive('destroy')->with($pid, array())->once()->andReturn(['result' => 'ok']);

        // when
        $deleted = $this->cloudinaire->delete($pid);
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_should_call_api_add_tag_when_calling_add_tag()
    {
        $pids = ['pid1', 'pid2'];
        $tag = 'tag';

        $this->uploader->shouldReceive('add_tag')->once()->with($tag, $pids, array());

        $this->cloudinaire->addTag($tag, $pids);
    }

    /** @test */
    public function it_should_call_api_remove_tag_when_calling_add_tag()
    {
        $pids = ['pid1', 'pid2'];
        $tag = 'tag';

        $this->uploader->shouldReceive('remove_tag')->once()->with($tag, $pids, array());

        $this->cloudinaire->removeTag($tag, $pids);
    }

    /** @test */
    public function it_should_call_api_rename_tag_when_calling_add_tag()
    {
        $pids = ['pid1', 'pid2'];
        $tag = 'tag';

        $this->uploader->shouldReceive('replace_tag')->once()->with($tag, $pids, array());

        $this->cloudinaire->replaceTag($tag, $pids);
    }

    /** @test */
    public function it_should_call_api_delete_resources_when_calling_destroy_images()
    {
        $pids = ['pid1', 'pid2'];
        $this->api->shouldReceive('delete_resources')->once()->with($pids, array());

        $this->cloudinaire->destroyImages($pids);
    }

    /** @test */
    public function it_should_call_api_delete_resources_when_calling_delete_resources()
    {
        $pids = ['pid1', 'pid2'];
        $this->api->shouldReceive('delete_resources')->once()->with($pids, array());

        $this->cloudinaire->deleteResources($pids);
    }

    /** @test */
    public function it_should_call_api_delete_resources_by_prefix_when_calling_delete_resources_by_prefix()
    {
        $prefix = 'prefix';
        $this->api->shouldReceive('delete_resources_by_prefix')->once()->with($prefix, array());

        $this->cloudinaire->deleteResourcesByPrefix($prefix);
    }

    /** @test */
    public function it_should_call_api_delete_all_resources_when_calling_delete_all_resources()
    {
        $this->api->shouldReceive('delete_all_resources')->once()->with(array());

        $this->cloudinaire->deleteAllResources();
    }

    /** @test */
    public function it_should_call_api_delete_resources_by_tag_when_calling_delete_resources_by_tag()
    {
        $tag = 'tag1';
        $this->api->shouldReceive('delete_resources_by_tag')->once()->with($tag, array());

        $this->cloudinaire->deleteResourcesByTag($tag);
    }

    /** @test */
    public function it_should_call_api_delete_derived_resources_when_calling_delete_derived_resources()
    {
        $pids = ['pid1', 'pid2'];
        $this->api->shouldReceive('delete_derived_resources')->once()->with($pids, array());

        $this->cloudinaire->deleteDerivedResources($pids);
    }

    /** @test */
    public function it_should_set_uploaded_result_when_uploading_video()
    {
        // given
        $filename = 'filename';
        $defaults_options = [
            'public_id' => null,
            'tags'      => array(),
            'resource_type' => 'video'
        ];

        $expected_result = ['public_id' => '123456789'];

        $this->uploader->shouldReceive('upload')->once()->with($filename, $defaults_options)->andReturn($expected_result);

        // when
        $this->cloudinaire->uploadVideo($filename);

        // then
        $result = $this->cloudinaire->getResult();
        $this->assertEquals($expected_result, $result);
    }

    /** @test */
    public function it_should_call_api_create_archive_when_generating_archive()
    {
        // given
        $this->uploader->shouldReceive('create_archive')->once()->with(
          ['tag' => 'kitten', 'mode' => 'create', 'target_public_id' => null]
        );

        // when
        $this->cloudinaire->createArchive(['tag' => 'kitten']);
    }

    /** @test */
    public function it_should_call_api_create_archive_with_correct_archive_name()
    {
        // given
        $this->uploader->shouldReceive('create_archive')->once()->with(
          ['tag' => 'kitten', 'mode' => 'create', 'target_public_id' => 'kitten_archive']
        );

        // when
        $this->cloudinaire->createArchive(['tag' => 'kitten'], 'kitten_archive');
    }

    /** @test */
    public function it_should_call_api_download_archive_url_when_generating_archive()
    {
        // given
        $this->cloudinary->shouldReceive('download_archive_url')->once()->with(
          ['tag' => 'kitten', 'target_public_id' => null]
        );

        // when
        $this->cloudinaire->downloadArchiveUrl(['tag' => 'kitten']);
    }

    /** @test */
    public function it_should_call_api_download_archive_url_with_correct_archive_name()
    {
        // given
        $this->cloudinary->shouldReceive('download_archive_url')->once()->with(
          ['tag' => 'kitten', 'target_public_id' => 'kitten_archive']
        );

        // when
        $this->cloudinaire->downloadArchiveUrl(['tag' => 'kitten'], 'kitten_archive');
    }

    /** @test */
    public function it_should_show_response_when_calling_resources()
    {
        // given
        $this->api->shouldReceive('resources')->once()->with(array());

        // when
        $this->cloudinaire->resources();
    }

    /** @test */
    public function it_should_show_response_when_calling_resources_by_ids()
    {
        $pids = ['pid1', 'pid2'];

        $options = ['test', 'test1'];

        // given
        $this->api->shouldReceive('resources_by_ids')->once()->with($pids, $options);

        // when
        $this->cloudinaire->resourcesByIds($pids, $options);
    }

    /** @test */
    public function it_should_show_response_when_calling_resources_by_tag()
    {
        $tag = 'tag';

        // given
        $this->api->shouldReceive('resources_by_tag')->once()->with($tag, array());

        // when
        $this->cloudinaire->resourcesByTag($tag);
    }

    /** @test */
    public function it_should_show_response_when_calling_resources_by_moderation()
    {
        $kind = 'manual';
        $status = 'pending';

        // given
        $this->api->shouldReceive('resources_by_moderation')->once()->with($kind, $status, array());

        // when
        $this->cloudinaire->resourcesByModeration($kind, $status);
    }

    /** @test */
    public function it_should_show_list_when_calling_tags()
    {
        // given
        $this->api->shouldReceive('tags')->once()->with(array());

        // when
        $this->cloudinaire->tags();
    }

    /** @test */
    public function it_should_show_response_when_calling_resource()
    {
        $pid = 'pid';

        // given
        $this->api->shouldReceive('resource')->once()->with($pid, array());

        // when
        $this->cloudinaire->resource($pid);
    }

    /** @test */
    public function it_should_update_a_resource_when_calling_update()
    {
        $pid = 'pid';
        $options = ['tags' => 'tag1'];

        // given
        $this->api->shouldReceive('update')->once()->with($pid, $options);

        // when
        $this->cloudinaire->update($pid, $options);
    }

    /** @test */
    public function it_should_show_transformations_list_when_calling_transformations()
    {
        // given
        $this->api->shouldReceive('transformations')->once()->with(array());

        // when
        $this->cloudinaire->transformations();
    }

    /** @test */
    public function it_should_show_one_transformation_when_calling_transformation()
    {
        $transformation = "c_fill,h_100,w_150";

        // given
        $this->api->shouldReceive('transformation')->once()->with($transformation, array());

        // when
        $this->cloudinaire->transformation($transformation);
    }

    /** @test */
    public function it_should_delete_a_transformation_when_calling_delete_transformation()
    {
        $transformation = "c_fill,h_100,w_150";

        // given
        $this->api->shouldReceive('delete_transformation')->once()->with($transformation, array());

        // when
        $this->cloudinaire->deleteTransformation($transformation);
    }

    /** @test */
    public function it_should_update_a_transformation_when_calling_update_transformation()
    {
        $transformation = "c_fill,h_100,w_150";
        $updates = array("allowed_for_strict" => 1);

        // given
        $this->api->shouldReceive('update_transformation')->once()->with($transformation, $updates, array());

        // when
        $this->cloudinaire->updateTransformation($transformation, $updates);
    }

    /** @test */
    public function it_should_create_a_transformation_when_calling_create_transformation()
    {
        $name = "name";
        $definition = "c_fill,h_100,w_150";

        // given
        $this->api->shouldReceive('create_transformation')->once()->with($name, $definition, array());

        // when
        $this->cloudinaire->createTransformation($name, $definition);
    }

    /** @test */
    public function it_should_restore_resources_when_calling_restore()
    {
        $pids = ['pid1', 'pid2'];

        // given
        $this->api->shouldReceive('restore')->once()->with($pids, array());

        // when
        $this->cloudinaire->restore($pids);
    }

    /** @test */
    public function it_should_show_upload_mappings_list_when_calling_upload_mappings()
    {
        // given
        $this->api->shouldReceive('upload_mappings')->once()->with(array());

        // when
        $this->cloudinaire->uploadMappings();
    }

    /** @test */
    public function it_should_upload_mapping_when_calling_upload_mapping()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('upload_mapping')->once()->with($pid, array());

        // when
        $this->cloudinaire->uploadMapping($pid);
    }

    /** @test */
    public function it_should_create_upload_mapping_when_calling_create_upload_mapping()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('create_upload_mapping')->once()->with($pid, array());

        // when
        $this->cloudinaire->createUploadMapping($pid);
    }

    /** @test */
    public function it_should_delete_upload_mapping_when_calling_delete_upload_mapping()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('delete_upload_mapping')->once()->with($pid, array());

        // when
        $this->cloudinaire->deleteUploadMapping($pid);
    }

    /** @test */
    public function it_should_update_upload_mapping_when_calling_update_upload_mapping()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('update_upload_mapping')->once()->with($pid, array());

        // when
        $this->cloudinaire->updateUploadMapping($pid);
    }

    /** @test */
    public function it_should_show_upload_presets_list_when_calling_upload_presets()
    {
        // given
        $this->api->shouldReceive('upload_presets')->once()->with(array());

        // when
        $this->cloudinaire->uploadPresets();
    }


    /** @test */
    public function it_should_upload_preset_when_calling_upload_preset()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('upload_preset')->once()->with($pid, array());

        // when
        $this->cloudinaire->uploadPreset($pid);
    }

    /** @test */
    public function it_should_create_upload_preset_when_calling_create_upload_preset()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('create_upload_preset')->once()->with($pid, array());

        // when
        $this->cloudinaire->createUploadPreset($pid);
    }

    /** @test */
    public function it_should_delete_upload_preset_when_calling_delete_upload_preset()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('delete_upload_preset')->once()->with($pid, array());

        // when
        $this->cloudinaire->deleteUploadPreset($pid);
    }

    /** @test */
    public function it_should_update_upload_preset_when_calling_update_upload_preset()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('update_upload_preset')->once()->with($pid, array());

        // when
        $this->cloudinaire->updateUploadPreset($pid);
    }

    /** @test */
    public function it_should_show_root_folders_list_when_calling_root_folders()
    {
        // given
        $this->api->shouldReceive('root_folders')->once()->with(array());

        // when
        $this->cloudinaire->rootFolders();
    }

    /** @test */
    public function it_should_subfolders_when_calling_subfolders()
    {
        $pid = 'pid1';

        // given
        $this->api->shouldReceive('subfolders')->once()->with($pid, array());

        // when
        $this->cloudinaire->subfolders($pid);
    }

    /** @test */
    public function it_should_show_usage_list_when_calling_usage()
    {
        // given
        $this->api->shouldReceive('usage')->once()->with(array());

        // when
        $this->cloudinaire->usage();
    }

    /** @test */
    public function it_should_show_ping_list_when_calling_ping()
    {
        // given
        $this->api->shouldReceive('ping')->once()->with(array());

        // when
        $this->cloudinaire->ping();
    }
}
