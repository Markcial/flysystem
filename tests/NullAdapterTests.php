<?php

use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;

class NullAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        return new Filesystem(new NullAdapter);
    }

    protected function getAdapter()
    {
        return new NullAdapter;
    }

    public function testWrite()
    {
        $fs = $this->getFilesystem();
        $result = $fs->write('path', 'contents');
        $this->assertTrue($result);
        $this->assertTrue($fs->has('path'));
    }

    /**
     * @expectedException  \League\Flysystem\FileNotFoundException
     */
    public function testRead()
    {
        $fs = $this->getFilesystem();
        $fs->read('something');
    }

    public function testHas()
    {
        $fs = $this->getFilesystem();
        $this->assertFalse($fs->has('something'));
    }

    public function testDelete()
    {
        $adapter = $this->getAdapter();
        $this->assertFalse($adapter->delete('something'));
    }

    public function expectedFailsProvider()
    {
        return array(
            array('read'),
            array('update'),
            array('read'),
            array('rename'),
            array('delete'),
            array('listContents', array()),
            array('getMetadata'),
            array('getSize'),
            array('getMimetype'),
            array('getTimestamp'),
            array('getVisibility'),
            array('deleteDir'),
        );
    }

    /**
     * @dataProvider expectedFailsProvider
     */
    public function testExpectedFails($method, $result = false)
    {
        $adapter = new NullAdapter;
        $this->assertEquals($result, $adapter->{$method}('one', 'two', new Config));
    }

    public function expectedArrayResultProvider()
    {
        return array(
            array('write'),
            array('setVisibility'),
        );
    }

    /**
     * @dataProvider expectedArrayResultProvider
     */
    public function testArrayResult($method)
    {
        $adapter = new NullAdapter;
        $this->assertInternalType('array', $adapter->{$method}('one', tmpfile(), new Config(array('visibility' => 'public'))));
    }

    public function testArrayResultForCreateDir()
    {
        $adapter = new NullAdapter;
        $this->assertInternalType('array', $adapter->createDir('one', new Config(array('visibility' => 'public'))));
    }
}
