<?php
/**
 * FesCkFinderModule
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */

return array(
    'ck_finder' => array(
        // Authentication service must have getIdentity method
        'authenticationService' => 'Zend\Authentication\AuthenticationService',
        // CKFinder Internal Directory
        'privateDir'     => array(
            'backend' => 'default',
            'tags'   => '.ckfinder/tags',
            'logs'   => '.ckfinder/logs',
            'cache'  => '.ckfinder/cache',
            'thumbs' => '.ckfinder/cache/thumbs',
        ),
        // Images and Thumbnails
        'images' => array(
            'maxWidth'  => PHP_INT_MAX,
            'maxHeight' => PHP_INT_MAX,
            'quality'   => 80,
            'sizes' => array(
                'article' => array('width' => 1024, 'height' => 725, 'quality' => 80),
                /*'small'  => array('width' => 480, 'height' => 320, 'quality' => 80),
                'medium' => array('width' => 600, 'height' => 480, 'quality' => 80),
                'large'  => array('width' => 800, 'height' => 600, 'quality' => 80)*/
            )
        ),
        // Backends
        'backends' => array(
            array(
                'name'         => 'default',
                'adapter'      => 'local',
                'baseUrl'      => '/',
            //  'root'         => '', // Can be used to explicitly set the CKFinder user files directory.
                'chmodFiles'   => 0777,
                'chmodFolders' => 0755,
                'filesystemEncoding' => 'UTF-8',
            ),
        ),
        // Resource Types
        'defaultResourceTypes' => '',
        'resourceTypes'        => array(
            array(
                'name'              => 'Files', // Single quotes not allowed.
                'directory'         => 'files',
                'maxSize'           => 0,
                'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
                'deniedExtensions'  => '',
                'backend'           => 'default'
            ),
            array(
                'name'              => 'Images',
                'directory'         => 'images',
                'maxSize'           => 0,
                'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
                'deniedExtensions'  => '',
                'backend'           => 'default'
            ),
        ),
        // Access Control
        'accessControl'    => array(
            array(
                'role'                => '*',
                'resourceType'        => '*',
                'folder'              => '/',

                'FOLDER_VIEW'         => true,
                'FOLDER_CREATE'       => true,
                'FOLDER_RENAME'       => true,
                'FOLDER_DELETE'       => true,

                'FILE_VIEW'           => true,
                'FILE_CREATE'         => true,
                'FILE_RENAME'         => true,
                'FILE_DELETE'         => true,

                'IMAGE_RESIZE'        => true,
                'IMAGE_RESIZE_CUSTOM' => true,
            ),
        ),
        // Other Settings
        'overwriteOnUpload'        => false,
        'checkDoubleExtension'     => true,
        'disallowUnsafeCharacters' => false,
        'secureImageUploads'       => true,
        'checkSizeAfterScaling'    => true,
        'htmlExtensions'           => array('html', 'htm', 'xml', 'js'),
        'hideFolders'              => array('.*', 'CVS', '__thumbs'),
        'hideFiles'                => array('.*'),
        'forceAscii'               => false,
        'xSendfile'                => false,
        'debug'                    => false,
        // Plugins
        //'pluginsDirectory' => '',
        //'plugins'   => array(),
        // Cache settings
        'cache'     => array(
            'imagePreview' => 24 * 3600,
            'thumbnails'   => 24 * 3600 * 365,
            'proxyCommand' => 0
        ),
        // Temp Directory settings
        // 'tempDirectory' => sys_get_temp_dir(),
        // Session Cause Performance Issues
        'sessionWriteClose' => true,
        // CSRF protection
        'csrfProtection'    => true,
    ),
);
