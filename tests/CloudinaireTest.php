<?php

namespace Bakerkretzmar\LaravelCloudinary\Tests;

use Cloudinaire;

use Mockery;

class CloudinaireTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    protected function fakeCloudinary()
    {
        $this->cloudinary = Mockery::mock('Cloudinary');
        $this->uploader = Mockery::mock('Cloudinary\Uploader');
        $this->api = Mockery::mock('Cloudinary\Api');

        Cloudinaire::swap(
            Cloudinaire::fake($this->cloudinary, $this->uploader, $this->api)
        );
    }

    /** @test */
    public function set_upload_result_after_uploading_image()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->upload('file_name', ['public_id' => null, 'tags' => []])
            ->andReturns(['public_id' => '123456789']);

        Cloudinaire::upload('file_name');

        $result = Cloudinaire::getResult();
        $this->assertEquals(['public_id' => '123456789'], $result);
    }

    /** @test */
    public function set_upload_result_after_uploading_unsigned_image()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->unsigned_upload('file_name', ['param' => 1], ['public_id' => null, 'tags' => []])
            ->andReturns(['public_id' => '123456789']);

        Cloudinaire::unsignedUpload('file_name', null, ['param' => 1]);

        $result = Cloudinaire::getResult();
        $this->assertEquals(['public_id' => '123456789'], $result);
    }

    /** @test */
    public function set_upload_result_after_uploading_private_image()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->upload('file_name', [
                'public_id' => null,
                'tags' => [],
                'type' => 'private',
            ])
            ->andReturns(['public_id' => '123456789']);

        Cloudinaire::upload('file_name', null, ['type' => 'private']);

        $result = Cloudinaire::getResult();
        $this->assertEquals(['public_id' => '123456789'], $result);
    }

    /** @test */
    public function return_image_url_when_calling_show()
    {
        $this->fakeCloudinary();

        $this->cloudinary->expects()
            ->cloudinary_url('file_name', []);

        Cloudinaire::show('file_name');
    }

    /** @test */
    public function return_https_image_url_when_calling_secure_show()
    {
        $this->fakeCloudinary();

        $this->cloudinary->expects()
            ->cloudinary_url('file_name', ['secure' => true]);

        Cloudinaire::secureShow('file_name');
    }

    /** @test */
    public function return_image_url_when_calling_show_private_url()
    {
        $this->fakeCloudinary();

        $this->cloudinary->expects()
            ->private_download_url('file_name', 'png', []);

        Cloudinaire::showPrivateUrl('file_name', 'png');
    }

    /** @test */
    public function return_image_url_when_calling_private_download_url()
    {
        $this->fakeCloudinary();

        $this->cloudinary->expects()
            ->private_download_url('file_name', 'png', []);

        Cloudinaire::privateDownloadUrl('file_name', 'png');
    }

    /** @test */
    public function call_uploader_rename_when_calling_rename()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->rename('from', 'to', []);

        Cloudinaire::rename('from', 'to');
    }

    /** @test */
    public function call_uploader_destroy_when_calling_destroy_image()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->destroy('public_id', []);

        Cloudinaire::destroyImage('public_id');
    }

    /** @test */
    public function call_api_destroy_when_calling_destroy()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->destroy('public_id', []);

        Cloudinaire::destroy('public_id');
    }

    /** @test */
    public function delete_alias_returns_boolean()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->destroy('public_id', [])
            ->andReturns(['result' => 'ok']);

        $deleted = Cloudinaire::delete('public_id');
        $this->assertTrue($deleted);
    }







    /** @test */
    public function can_add_tag_to_images()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->add_tag('tag', ['public_id_1', 'public_id_2'], [])
            ->andReturns(['public_ids' => ['public_id_1', 'public_id_2']]);

        Cloudinaire::addTag('tag', ['public_id_1', 'public_id_2']);
    }

    /** @test */
    public function can_add_tag_to_images_hitting_api()
    {
        $response = Cloudinaire::addTag('tag', ['samples/bike']);

        $this->assertArrayHasKey('public_ids', $response);
        $this->assertContains('samples/bike', $response['public_ids']);
    }

    /** @test */
    public function can_remove_tag_from_images()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->remove_tag('tag', ['public_id_1', 'public_id_2'], [])
            ->andReturns(['public_ids' => ['public_id_1', 'public_id_2']]);

        Cloudinaire::removeTag('tag', ['public_id_1', 'public_id_2']);
    }

    /** @test */
    public function can_remove_tag_from_images_hitting_api()
    {
        $response = Cloudinaire::removeTag('tag', ['samples/bike']);

        $this->assertArrayHasKey('public_ids', $response);
        $this->assertContains('samples/bike', $response['public_ids']);
    }

    /** @test */
    public function can_remove_all_tags_from_images()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->remove_all_tags(['public_id_1', 'public_id_2'], [])
            ->andReturns(['public_ids' => ['public_id_1', 'public_id_2']]);

        Cloudinaire::removeAllTags(['public_id_1', 'public_id_2']);
    }

    /** @test */
    public function can_remove_all_tags_from_images_hitting_api()
    {
        $response = Cloudinaire::removeAllTags(['samples/bike']);

        $this->assertArrayHasKey('public_ids', $response);
        $this->assertContains('samples/bike', $response['public_ids']);
    }

    /** @test */
    public function can_replace_tag_for_images()
    {
        $this->fakeCloudinary();

        $this->uploader->expects()
            ->replace_tag('tag', ['public_id_1', 'public_id_2'], [])
            ->andReturns(['public_ids' => ['public_id_1', 'public_id_2']]);

        Cloudinaire::replaceTag('tag', ['public_id_1', 'public_id_2']);
    }

    /** @test */
    public function can_replace_tag_for_images_hitting_api()
    {
        $response = Cloudinaire::replaceTag('tag2', ['samples/bike']);

        $this->assertArrayHasKey('public_ids', $response);
        $this->assertContains('samples/bike', $response['public_ids']);
    }





    /** @test */
    // public function it_should_call_api_delete_resources_when_calling_destroy_images()
    // {
    //     $this->fakeCloudinary();

    //     $pids = ['pid1', 'pid2'];
    //     $this->api->shouldReceive('delete_resources')->once()->with($pids, array());

    //     Cloudinaire::destroyImages($pids);
    // }

    /** @test */
    // public function it_should_call_api_delete_resources_when_calling_delete_resources()
    // {
    //     $this->fakeCloudinary();

    //     $pids = ['pid1', 'pid2'];
    //     $this->api->shouldReceive('delete_resources')->once()->with($pids, array());

    //     Cloudinaire::deleteResources($pids);
    // }

    /** @test */
    // public function it_should_call_api_delete_resources_by_prefix_when_calling_delete_resources_by_prefix()
    // {
    //     $this->fakeCloudinary();

    //     $prefix = 'prefix';
    //     $this->api->shouldReceive('delete_resources_by_prefix')->once()->with($prefix, array());

    //     Cloudinaire::deleteResourcesByPrefix($prefix);
    // }

    /** @test */
    // public function it_should_call_api_delete_all_resources_when_calling_delete_all_resources()
    // {
    //     $this->fakeCloudinary();

    //     $this->api->shouldReceive('delete_all_resources')->once()->with(array());

    //     Cloudinaire::deleteAllResources();
    // }

    /** @test */
    // public function it_should_call_api_delete_resources_by_tag_when_calling_delete_resources_by_tag()
    // {
    //     $this->fakeCloudinary();

    //     $tag = 'tag1';
    //     $this->api->shouldReceive('delete_resources_by_tag')->once()->with($tag, array());

    //     Cloudinaire::deleteResourcesByTag($tag);
    // }

    /** @test */
    // public function it_should_call_api_delete_derived_resources_when_calling_delete_derived_resources()
    // {
    //     $this->fakeCloudinary();

    //     $pids = ['pid1', 'pid2'];
    //     $this->api->shouldReceive('delete_derived_resources')->once()->with($pids, array());

    //     Cloudinaire::deleteDerivedResources($pids);
    // }

    /** @test */
    // public function it_should_set_uploaded_result_when_uploading_video()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $filename = 'filename';
    //     $defaults_options = [
    //         'public_id' => null,
    //         'tags'      => array(),
    //         'resource_type' => 'video'
    //     ];

    //     $expected_result = ['public_id' => '123456789'];

    //     $this->uploader->shouldReceive('upload')->once()->with($filename, $defaults_options)->andReturn($expected_result);

    //     // when
    //     Cloudinaire::uploadVideo($filename);

    //     // then
    //     $result = Cloudinaire::getResult();
    //     $this->assertEquals($expected_result, $result);
    // }

    /** @test */
    // public function it_should_call_api_create_archive_when_generating_archive()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->uploader->shouldReceive('create_archive')->once()->with(
    //       ['tag' => 'kitten', 'mode' => 'create', 'target_public_id' => null]
    //     );

    //     // when
    //     Cloudinaire::createArchive(['tag' => 'kitten']);
    // }

    /** @test */
    // public function it_should_call_api_create_archive_with_correct_archive_name()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->uploader->shouldReceive('create_archive')->once()->with(
    //       ['tag' => 'kitten', 'mode' => 'create', 'target_public_id' => 'kitten_archive']
    //     );

    //     // when
    //     Cloudinaire::createArchive(['tag' => 'kitten'], 'kitten_archive');
    // }

    /** @test */
    // public function it_should_call_api_download_archive_url_when_generating_archive()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->cloudinary->shouldReceive('download_archive_url')->once()->with(
    //       ['tag' => 'kitten', 'target_public_id' => null]
    //     );

    //     // when
    //     Cloudinaire::downloadArchiveUrl(['tag' => 'kitten']);
    // }

    /** @test */
    // public function it_should_call_api_download_archive_url_with_correct_archive_name()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->cloudinary->shouldReceive('download_archive_url')->once()->with(
    //       ['tag' => 'kitten', 'target_public_id' => 'kitten_archive']
    //     );

    //     // when
    //     Cloudinaire::downloadArchiveUrl(['tag' => 'kitten'], 'kitten_archive');
    // }

    /** @test */
    // public function it_should_show_response_when_calling_resources()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->api->shouldReceive('resources')->once()->with(array());

    //     // when
    //     Cloudinaire::resources();
    // }

    /** @test */
    // public function it_should_show_response_when_calling_resources_by_ids()
    // {
    //     $this->fakeCloudinary();

    //     $pids = ['pid1', 'pid2'];

    //     $options = ['test', 'test1'];

    //     // given
    //     $this->api->shouldReceive('resources_by_ids')->once()->with($pids, $options);

    //     // when
    //     Cloudinaire::resourcesByIds($pids, $options);
    // }

    /** @test */
    // public function it_should_show_response_when_calling_resources_by_tag()
    // {
    //     $this->fakeCloudinary();

    //     $tag = 'tag';

    //     // given
    //     $this->api->shouldReceive('resources_by_tag')->once()->with($tag, array());

    //     // when
    //     Cloudinaire::resourcesByTag($tag);
    // }

    /** @test */
    // public function it_should_show_response_when_calling_resources_by_moderation()
    // {
    //     $this->fakeCloudinary();

    //     $kind = 'manual';
    //     $status = 'pending';

    //     // given
    //     $this->api->shouldReceive('resources_by_moderation')->once()->with($kind, $status, array());

    //     // when
    //     Cloudinaire::resourcesByModeration($kind, $status);
    // }

    /** @test */
    // public function it_should_show_list_when_calling_tags()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->api->shouldReceive('tags')->once()->with(array());

    //     // when
    //     Cloudinaire::tags();
    // }

    /** @test */
    // public function it_should_show_response_when_calling_resource()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid';

    //     // given
    //     $this->api->shouldReceive('resource')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::resource($pid);
    // }

    /** @test */
    // public function it_should_update_a_resource_when_calling_update()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid';
    //     $options = ['tags' => 'tag1'];

    //     // given
    //     $this->api->shouldReceive('update')->once()->with($pid, $options);

    //     // when
    //     Cloudinaire::update($pid, $options);
    // }

    /** @test */
    // public function it_should_show_transformations_list_when_calling_transformations()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->api->shouldReceive('transformations')->once()->with(array());

    //     // when
    //     Cloudinaire::transformations();
    // }

    /** @test */
    // public function it_should_show_one_transformation_when_calling_transformation()
    // {
    //     $this->fakeCloudinary();

    //     $transformation = "c_fill,h_100,w_150";

    //     // given
    //     $this->api->shouldReceive('transformation')->once()->with($transformation, array());

    //     // when
    //     Cloudinaire::transformation($transformation);
    // }

    /** @test */
    // public function it_should_delete_a_transformation_when_calling_delete_transformation()
    // {
    //     $this->fakeCloudinary();

    //     $transformation = "c_fill,h_100,w_150";

    //     // given
    //     $this->api->shouldReceive('delete_transformation')->once()->with($transformation, array());

    //     // when
    //     Cloudinaire::deleteTransformation($transformation);
    // }

    /** @test */
    // public function it_should_update_a_transformation_when_calling_update_transformation()
    // {
    //     $this->fakeCloudinary();

    //     $transformation = "c_fill,h_100,w_150";
    //     $updates = array("allowed_for_strict" => 1);

    //     // given
    //     $this->api->shouldReceive('update_transformation')->once()->with($transformation, $updates, array());

    //     // when
    //     Cloudinaire::updateTransformation($transformation, $updates);
    // }

    /** @test */
    // public function it_should_create_a_transformation_when_calling_create_transformation()
    // {
    //     $this->fakeCloudinary();

    //     $name = "name";
    //     $definition = "c_fill,h_100,w_150";

    //     // given
    //     $this->api->shouldReceive('create_transformation')->once()->with($name, $definition, array());

    //     // when
    //     Cloudinaire::createTransformation($name, $definition);
    // }

    /** @test */
    // public function it_should_restore_resources_when_calling_restore()
    // {
    //     $this->fakeCloudinary();

    //     $pids = ['pid1', 'pid2'];

    //     // given
    //     $this->api->shouldReceive('restore')->once()->with($pids, array());

    //     // when
    //     Cloudinaire::restore($pids);
    // }

    /** @test */
    // public function it_should_show_upload_mappings_list_when_calling_upload_mappings()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->api->shouldReceive('upload_mappings')->once()->with(array());

    //     // when
    //     Cloudinaire::uploadMappings();
    // }

    /** @test */
    // public function it_should_upload_mapping_when_calling_upload_mapping()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('upload_mapping')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::uploadMapping($pid);
    // }

    /** @test */
    // public function it_should_create_upload_mapping_when_calling_create_upload_mapping()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('create_upload_mapping')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::createUploadMapping($pid);
    // }

    /** @test */
    // public function it_should_delete_upload_mapping_when_calling_delete_upload_mapping()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('delete_upload_mapping')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::deleteUploadMapping($pid);
    // }

    /** @test */
    // public function it_should_update_upload_mapping_when_calling_update_upload_mapping()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('update_upload_mapping')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::updateUploadMapping($pid);
    // }

    /** @test */
    // public function it_should_show_upload_presets_list_when_calling_upload_presets()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->api->shouldReceive('upload_presets')->once()->with(array());

    //     // when
    //     Cloudinaire::uploadPresets();
    // }


    /** @test */
    // public function it_should_upload_preset_when_calling_upload_preset()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('upload_preset')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::uploadPreset($pid);
    // }

    /** @test */
    // public function it_should_create_upload_preset_when_calling_create_upload_preset()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('create_upload_preset')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::createUploadPreset($pid);
    // }

    /** @test */
    // public function it_should_delete_upload_preset_when_calling_delete_upload_preset()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('delete_upload_preset')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::deleteUploadPreset($pid);
    // }

    /** @test */
    // public function it_should_update_upload_preset_when_calling_update_upload_preset()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('update_upload_preset')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::updateUploadPreset($pid);
    // }

    /** @test */
    // public function it_should_show_root_folders_list_when_calling_root_folders()
    // {
    //     $this->fakeCloudinary();

    //     // given
    //     $this->api->shouldReceive('root_folders')->once()->with(array());

    //     // when
    //     Cloudinaire::rootFolders();
    // }

    /** @test */
    // public function it_should_subfolders_when_calling_subfolders()
    // {
    //     $this->fakeCloudinary();

    //     $pid = 'pid1';

    //     // given
    //     $this->api->shouldReceive('subfolders')->once()->with($pid, array());

    //     // when
    //     Cloudinaire::subfolders($pid);
    // }

    /** @test */
    public function can_get_usage_details()
    {
        $response = Cloudinaire::usage();

        $this->assertArrayHasKey('plan', $response);
        $this->assertArrayHasKey('storage', $response);
        $this->assertArrayHasKey('credits', $response);
    }

    /** @test */
    public function can_ping_admin_api()
    {
        $response = Cloudinaire::ping();

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('ok', $response['status']);
    }
}
