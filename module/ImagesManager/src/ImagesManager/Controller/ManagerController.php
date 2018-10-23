<?php
/**
 * Sort of the file explorer controller 
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace ImagesManager\Controller;

use WebinoImageThumb\Service\ImageThumb as WebinoImageThumb;
use Admin\Controller\AbstractController;
use ImagesManager\Form\Upload as UploadForm;
use Zend\View\Model\JsonModel;
use Zend\Validator\ValidatorChain;
use \DirectoryIterator;
use ImagesManager\Lister\Image as ListerImage;
use Zend\Mvc\MvcEvent;
use ImagesManager\Form\CreateFolder;
use Zend\Uri\UriFactory;
use Zend\Http\PhpEnvironment\Response as EnvironmentResponse;
use Zend\Session\Container as SessionContainer;
use ImagesManager\Thumbnail\Thumbnailer;
use ImagesManager\Thumbnail\Cropping\Fengyuanchen as FengyuanchenAdapter;

class ManagerController extends AbstractController
{
    const LISTER_MODE_VIEW          = 'view';
    const LISTER_MODE_SELECT_IMAGE  = 'image_select';
    const LISTER_MODE_SELECT_IMAGES = 'images_select';
    const LISTER_MODE_SELECT_FOLDER = 'folder_select';    

    /**
     * @var Thumbnailer
     */
    protected $thumbnailer;

    /**
     * @var UploadForm
     */
    protected $uploadForm;

    /**
     * @var CreateFolder
     */
    protected $cForm;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $denied;

    public function __construct(
        Thumbnailer $thumbnailer,
        array $config, 
        UploadForm $form, 
        CreateFolder $cForm
    )
    {
        $this->thumbnailer  = $thumbnailer;
        $this->uploadForm   = $form;
        $this->config       = $config;
        $this->cForm        = $cForm;

        // Some of the folders are not allowed for anyone
        if (isset($config['lister']['folders']['denied']) && 
            is_array($config['lister']['folders']['denied'])) {
            $this->denied = $config['lister']['folders']['denied'];
        }       
    }

    public function onDispatch(MvcEvent $e)
    {
        if ($this->getRequest()->getQuery('path') && 
            strpos($this->getRequest()->getQuery('path'), '.') !== false
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        parent::onDispatch($e);
    }

    public function indexAction()
    {
        /**
         * Some of the tags of admin layout can be configured
         */
        $this->layout()->tag = [
            'body' => [
                'class' => ['app-media'],
            ],
            'div.page' => [
                'class' => ['bg-white'],
            ],
        ];
        $request         = $this->getRequest();
        $config          = $this->config;
        $mode            = 'view';
        $modes           = [
            self::LISTER_MODE_VIEW,
            self::LISTER_MODE_SELECT_FOLDER,
            self::LISTER_MODE_SELECT_IMAGE,
            self::LISTER_MODE_SELECT_IMAGES,
        ];

        if ($this->apiCall()->isCalled()) {
            $mode = $this->apiCall()->getParams('mode');
        }

        $denied          = [];
        $path            = [];
        $path['public']  = ltrim(trim($request->getQuery('path', '')), '/\\');
        $path['public']  = $path['public'] ? $path['public'] : '/';
        $path['private'] = $config['images_directory'] . '/';
        $path['private'] .= ltrim(trim($request->getQuery('path', '')), '/\\');
        $path['private'] = rtrim($path['private'], '\\/');

        // Some of the folders are not allowed for anyone
        if (isset($config['lister']['folders']['denied']) && 
            is_array($config['lister']['folders']['denied'])) {
            $denied = $config['lister']['folders']['denied'];
        }

        if (is_dir($path['private']) 
            && !in_array($path['private'], $denied)
            && in_array($mode, $modes)
        ) {
            $lister  = new DirectoryIterator($path['private']);
            $files   = [];
            $folders = $this->getFoldersTree($config['images_directory'], $denied);
            $validators = [];

            // init validators chain
            if (isset($config['lister']['images']['validators']) &&
                is_array($config['lister']['images']['validators']) &&
                $config['lister']['images']['validators'])
            {
                $validators = new ValidatorChain();  

                foreach ($config['lister']['images']['validators'] as $validator) {
                    if (!isset($validator['name'])) {
                        continue;
                    }

                    $options = [];

                    if (isset($validator['options']) && is_array($validator['options'])) {
                        $options = $validator['options'];
                    }

                    $validators->attachByName(
                        $validator['name'],
                        $options
                    );
                }              
            }

            $image = new ListerImage();
            $image->setOptions(
                [
                    'config'              => $config, 
                    'base_url'            => $request->getBaseUrl(),
                    'thumbnail_organizer' => $this->thumbnailer
                    ->getStorageOrganizer()
                ]
            );

            foreach ($lister as $file) {
                if ($file->isDir() || $file->isDot()) {
                    continue;
                }

                if ($validators instanceof ValidatorChain &&
                    !$validators->isValid($file->getPathname())) {
                    continue;
                }

                $files[] = clone $image->setFileInfo(clone $file);
            }

            if ($request->isPost()
                && $mode != self::LISTER_MODE_VIEW
                && ($request->getPost('image')
                || $request->getPost('images')
                || $mode === self::LISTER_MODE_SELECT_FOLDER)
            ) {
                $post   = $request->getPost()->toArray(); 
                $result = [];

                if (self::LISTER_MODE_SELECT_FOLDER == $mode) {
                    foreach ($files as $image) {
                        $result[] = [
                            'path' => $image->getFileInfo()
                            ->getPathname(),
                            'href' => substr(
                                str_replace(
                                    '\\', 
                                    '/', 
                                    $image->getFileInfo()->getPathname()
                                ),
                                7
                            ),
                        ];
                    }
                } elseif (self::LISTER_MODE_SELECT_IMAGE == $mode) {
                    $result[] = [
                        'path' => $post['image'],
                        'href' => str_replace(
                            '\\', 
                            '/', 
                            substr($post['image'], 7)
                        ),
                    ];
                } elseif (self::LISTER_MODE_SELECT_IMAGES == $mode) {
                    foreach ($post['images'] as $im) {
                        $result[] = [
                            'path' => $im,
                            'href' => str_replace('\\', '/', substr($im, 7)),
                        ];
                    }
                }

                $this->apiCall()
                ->addResultData(['images' => $result])
                ->addParam('images', $result);

                if ($thumbnails = $this->apiCall()->getParams('thumbnails')) {
                    // this forward will make thumbnails
                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                            'action' => 'getThumbnails',
                        ]
                    ); 
                }

                return $this->apiCall()->result();
            }

            return [
                'path'      => $path['public'],
                'files'     => $files,
                'folders'   => $folders,
                'mode'      => $mode,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    /**
     * Gives json response accepts XmlHttpRequests only
     */
    public function jsonAction()
    {
        $request = $this->getRequest();
        $config  = $this->config;
        $mode            = 'view';
        $modes           = [
            self::LISTER_MODE_VIEW,
            self::LISTER_MODE_SELECT_FOLDER,
            self::LISTER_MODE_SELECT_IMAGE,
            self::LISTER_MODE_SELECT_IMAGES,
        ];

        if ($request->isXmlHttpRequest()) {
            $denied          = [];
            $path            = [];
            $path['public']  = ltrim(trim($request->getQuery('path', '')), '/\\');
            $path['public']  = $path['public'] ? $path['public'] : '/';
            $path['private'] = $config['images_directory'] . '/';
            $path['private'] .= ltrim(trim($request->getQuery('path', '')), '/\\');
            $path['private'] = rtrim($path['private'], '\\/');

            // Some of the folders are not allowed for anyone
            if (isset($config['lister']['folders']['denied']) && 
                is_array($config['lister']['folders']['denied'])) {
                $denied = $config['lister']['folders']['denied'];
            }

            if (is_dir($path['private']) 
                && !in_array($path['private'], $denied)
                && in_array($mode, $modes)
            ) {
                $lister  = new DirectoryIterator($path['private']);
                $files   = [];
                $folders = $this->getFoldersTree($config['images_directory'], $denied);
                $validators = [];

                // init validators chain
                if (isset($config['lister']['images']['validators']) &&
                    is_array($config['lister']['images']['validators']) &&
                    $config['lister']['images']['validators'])
                {
                    $validators = new ValidatorChain();  

                    foreach ($config['lister']['images']['validators'] as $validator) {
                        if (!isset($validator['name'])) {
                            continue;
                        }

                        $options = [];

                        if (isset($validator['options']) && is_array($validator['options'])) {
                            $options = $validator['options'];
                        }

                        $validators->attachByName(
                            $validator['name'],
                            $options
                        );
                    }              
                }

                $image = new ListerImage();
                $image->setOptions(
                    [
                        'config'              => $config, 
                        'base_url'            => $request->getBaseUrl(),
                        'thumbnail_organizer' => $this->thumbnailer
                        ->getStorageOrganizer()
                    ]
                );

                foreach ($lister as $file) {
                    if ($file->isDir() || $file->isDot()) {
                        continue;
                    }

                    if ($validators instanceof ValidatorChain &&
                        !$validators->isValid($file->getPathname())) {
                        continue;
                    }

                    $image->setFileInfo($file);

                    $files[] = [
                        'href'      => substr($file->getPathname(), 7),
                        'thumbnail' => $image->getThumbnailHref('list'),
                    ];
                } 

                return new JsonModel(
                    [
                        'images'  => $files,
                        'folders' => $folders,
                        'path'    => $path['public'],
                    ]
                );  
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    /**
     * Makes thumbnails for given images
     *
     * @throws Exception\RuntimeException
     */
    public function getThumbnailsAction()
    {
        $session    = new SessionContainer('thumbnailing_session');

        if ($this->getRequest()->getQuery('cancel')) {
            $caller = $this->apiCall()->getCallerUri()->toString();

            $this->apiCall()->cancel();

            // Clear session
            $session->getManager()
            ->getStorage()
            ->clear('thumbnailing_session');

            return $this->redirect()->toUrl($caller);
        }

        if ($this->apiCall()->isCalled()) {
            $thumbnails = [
                // All thumbnails config
                'autoload' => $this->config['thumbnails'],
                // Required thumbnails
                'required' => $this->apiCall()->getParams('thumbnails'),
            ];

            if (empty($thumbnails['required'])) {
                return $this->apiCall()->result();
            }

            if (empty($thumbnails['autoload'])
                || $thumbnails['required'] != array_intersect(
                    $thumbnails['required'], 
                    array_keys($thumbnails['autoload'])
                )
            ) {
                throw new Exception\RuntimeException(
                    'Required thumbnails config undefined.'
                );
            }

            // If session runned for the first time than try to auto crop
            if ($session->isRunned === null) {
                // prevent every time auto crop
                $session->isRunned   = true;
                $session->thumbnails = [];
                /**
                 * @var array Images that need to be cropped
                 */
                $images = $session->images = $this->apiCall()->getParams(
                    'images'
                );
                $session->requiredThumbnailsCount = count(
                    $thumbnails['required']
                );

                foreach ($thumbnails['required'] as $tkey => $thumbnailName) {            
                    $this->thumbnailer->setOptions(
                        $thumbnails['autoload'][$thumbnailName]
                    );
                    $temp = $images;

                    foreach ($images as $index => $image) {
                        $flag = $this->thumbnailer->createThumbnail(
                            $image['path']
                        );

                        if (false === $flag) {
                            continue;
                        }

                        $session->thumbnails[$thumbnailName][$index] = [
                            'path' => $flag,
                            'href' => substr($flag, 7),
                        ];
                        unset($temp[$index]); 
                    }

                    if (empty($temp)) {
                        unset($thumbnails['required'][$tkey]);
                    }
                }

                /**
                 * No need to continue all images cropped automatically
                 */
                if (empty($thumbnails['required'])) {
                    $thumbnails = $session->thumbnails;
                    // Clear session
                    $session->getManager()
                    ->getStorage()
                    ->clear('thumbnailing_session');

                    $this->apiCall()->addResultData(
                        ['thumbnails' => $thumbnails]
                    );

                    // Return to caller
                    return $this->apiCall()->result();
                } else {
                    // Save state of required thumbnails
                    $session->required    = $thumbnails['required'];
                    $session->imagesCount = count($images, 1) - count($images);
                    $session->imagesCount = $session->imagesCount / 2;
                    $session->doneAuto    = 0;

                    foreach ($session->thumbnails as $array) {
                        $session->doneAuto += count($array);
                    }

                    $session->current = [
                        'thumbnail' => current($thumbnails['required']),
                    ];

                    $this->setImageIndex($session);
                }
            }

            $request = $this->getRequest();
            $images  = $session->images;
            $image   = $images[$session->current['image']];

            $this->thumbnailer->setOptions(
                $this->config['thumbnails'][$session->current['thumbnail']]
            );

            if ($request->isPost() && $request->isXmlHttpRequest()) {
                $cropAdapter = new FengyuanchenAdapter(
                    $request->getPost()->toArray()
                );


                $path = $this->thumbnailer
                ->createThumbnail(
                    $image['path'],
                    $cropAdapter
                );
                $session->thumbnails
                [$session->current['thumbnail']]
                [$session->current['image']] = [
                    'path' => $path,
                    'href' => substr($path, 7)
                ];
                $session->done++;

                if ($session->imagesCount > ($session->current['image'] + 1)) {
                    $this->setImageIndex($session);
                } elseif ($session->imagesCount == ($session->current['image'] + 1)) {

                    $currPos = array_search($session->current['thumbnail'], $session->required);
                    
                    if (count($session->required) - 1 > $currPos) {
                        $session->current['thumbnail'] = $session->required[$currPos+1];
                    } else {
                        $this->apiCall()->addResultData(
                            [
                                'thumbnails' => $session->thumbnails
                            ]
                        );

                        $session->getManager()->getStorage()->clear(
                            'thumbnailing_session'
                        );

                        return new JsonModel(
                            [
                                'return' => $this->apiCall()->result(
                                    null, 
                                    false
                                ),
                            ]
                        );
                    }
                }

                return new JsonModel(
                    [
                        'ratio' => $this->thumbnailer->getWidth()/$this->thumbnailer->getHeight(),
                        'href' => $images[$session->current['image']]['href'],
                    ]
                );
            } 

            $stats           = [];
            $stats['total']  = $session->imagesCount * $session->requiredThumbnailsCount;
            $stats['total']  = $stats['total'] - $session->doneAuto; 
            $stats['done']   = $session->done ? $session->done : 0;

            return [
                'image'      => $image,
                'required'   => $session->required,
                'thumbnails' => $session->thumbnails,
                'thumbnailer'=> $this->thumbnailer,
                'done'       => $stats['done'],
                'total'      => $stats['total'],
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function createFolderAction()
    {
        $request         = $this->getRequest();
        $config          = $this->config; 
        $path['public']  = trim($request->getQuery('path', '/'));
        $path['private'] = sprintf(
            '%s%s%s',
            rtrim($config['images_directory'], '\\/'),
            '/',
            ltrim($path['public'], '\\/')
        );

        if (is_dir($path['private']) && $request->getQuery('redirect_to')
            && !in_array($path['private'], $this->denied)) {
            $form     = $this->cForm;
            $redirect = base64_decode(trim($request->getQuery('redirect_to')));

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data    = $form->getData();
                    $folder  = rtrim($path['private'], '\\/').'/'.$data['name'];

                    if (!is_dir($folder)) {
                        mkdir(
                            $folder, 
                            0777,
                            false
                        );

                        $redirectUri    = UriFactory::factory($redirect);
                        $query          = $redirectUri->getQueryAsArray();
                        $query['path']  = str_replace(
                            rtrim($config['images_directory'], '\\/') . '/',
                            '',
                            $folder
                        );
                        $redirectUri->setQuery($query);

                        return $this->redirect()->toUrl($redirectUri->toString());
                    } else {
                        $form->get('name')->setMessages(
                            [
                                'change_folder_name' => $this->translate(
                                    'Folder with this name already exists'
                                ),
                            ]
                        );
                    }
                }
            }

            return [
                'form' => $form,
                'back' => $redirect,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteFolderAction()
    {
        $request         = $this->getRequest();
        $config          = $this->config; 
        $path['public']  = trim($request->getQuery('path', '/'));
        $path['private'] = sprintf(
            '%s%s%s',
            rtrim($config['images_directory'], '\\/'),
            '/',
            ltrim($path['public'], '\\/')
        );

        if (is_dir($path['private']) && $request->getQuery('redirect_to')
            && !in_array($path['private'], $this->denied)
            && $path['private'] != $config['images_directory']
        ) {

            $redirect = base64_decode(trim($request->getQuery('redirect_to')));
            $folder   = new DirectoryIterator($path['private']);
            $count    = count(
                array_diff(
                    scandir(
                        $folder->getPathname()
                    ), 
                    ['..', '.']
                )
            );
            $messages = [];

            if ($request->getQuery('confirm')) {
                if ($count) {
                    $thumbnails = str_replace(
                        rtrim($config['images_directory'], '\\/'),
                        rtrim($config['thumbnails']['list']['directory'], '\\/'),
                        $path['private']
                    );

                    $this->deleteFolder($path['private']);

                    if (is_dir($thumbnails)) {
                        $this->deleteFolder($thumbnails);
                        rmdir($thumbnails);
                    }
                }

                rmdir($path['private']);

                $redirectUri    = UriFactory::factory($redirect);
                $query          = $redirectUri->getQueryAsArray();
                $query['path']  = substr(
                    $path['public'], 
                    0, 
                    strrpos($path['public'], basename($path['public']))
                );

                $redirectUri->setQuery($query);

                return $this->redirect()->toUrl($redirectUri->toString());
            }

            if ($count) {
                $messages[] = sprintf(
                    $this->translate('Folder has %s image(s)'), 
                    $count
                );
            }

            $confirmUrl         = clone $request->getUri();
            $query              = $confirmUrl->getQueryAsArray();
            $query['confirm']   = true;

            $confirmUrl->setQuery($query);

            return [
                'back'      => $redirect,
                'messages'  => $messages,
                'folder'    => $path['public'],
                'confirm'   => $confirmUrl->toString(),
            ];            
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function uploadAction() 
    {
        $request     = $this->getRequest();
        $form        = $this->uploadForm;
        $config      = $this->config;
        $thumbnailer = $this->thumbnailer;
        $path    = sprintf(
            '%s%s%s',
            rtrim($config['images_directory'], '/\\'),
            '/',
            ltrim(trim($request->getQuery('path')), '/\\')
        );

        if (!$request->getQuery('path') || !is_dir($path) ||
            in_array($path, $this->denied)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $form->getInputFilter()->get('images')->getFilterChain()->attachByName(
            'File\RenameUpload',
            [
                'overwrite'             => true,
                'use_upload_name'       => true,
                'use_upload_extension'  => true,
                'target'                => $path,
            ]
        );

        if ($request->isPost()) {
            $form->setData(
                array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                )
            );

            $files = [];

            if ($form->isValid()) {
                $data   = $form->getData();
                $conf   = $config['thumbnails']['list'];
                $this->thumbnailer->setOptions($conf);

                foreach ($data['images'] as $image) {
                    $name             = [];
                    // Create thumbnail
                    $name['private'] = $this->thumbnailer->createThumbnail(
                        $image['tmp_name']
                    );
                    $name['public']   = str_replace(
                        'public', 
                        $request->getBaseUrl(), 
                        $name['private']
                    );
                    $files[]          = [
                        'name'          => $image['name'],
                        'size'          => $image['size'],
                        'type'          => $image['type'],
                        'url'           => $name['public'],
                        'thumbnailUrl'  => $name['public'],
                        'deleteUrl'     => $this->url()->fromRoute(
                            'app/admin/images-manager',
                            [
                                'locale' => $this->locale()->current(),
                                'action' => 'deleteUpload',
                            ],
                            [
                                'query' => [
                                    'file' => $image['tmp_name'],
                                ],
                            ]
                        ),
                    ];
                }

                return new JsonModel(['files' => $files]);
            } else {
                $images = $request->getFiles()->toArray()['images'];
                $files  = [];
                $errors = [];

                foreach ($form->get('images')->getMessages() as $message) {
                    $errors[] = $this->translate($message);
                }

                $errors = join($errors, "<br>\n");

                foreach ($images as $file) {
                    $files[] = [
                        'name'  => $file['name'],
                        'size'  => $file['size'],
                        'type'  => $file['type'],
                        'error' => $errors,
                    ];
                }

                return new JsonModel(['files' => $files]);
            }
        }

        $path = ltrim(trim($request->getQuery('path')), '/\\');
        $path = $path ? $path : '/';

        return [
            'path' => $path,
            'back' => base64_decode(trim($request->getQuery('redirect_to'))),
        ];
    }

    public function deleteUploadAction()
    {
        $request = $this->getRequest();

        if ($request->getQuery('file') && is_file($request->getQuery('file'))) {
            $config          = $this->config;
            $name            = [];
            $name['private'] = ltrim(trim($request->getQuery('file')), '/\\');
            $name['thumb']   = $this->thumbnailer->getStorageOrganizer()
            ->getThumbnailPath($name['private'], 'list');

            // Delete thumbnail
            if (is_file($name['thumb'])) {
                unlink($name['thumb']);
            }


            // Delete original image
            unlink($name['private']);

            return new JsonModel(
                [
                    'files' => [
                        basename($name['private']) => true,
                    ],
                ]
            );
        }   

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteImageAction() 
    {
        $request    = $this->getRequest();
        $file       = trim($request->getQuery('file'));
        $redirect   = base64_decode($request->getQuery('redirect_to'));
        $config     = $this->config;

        if (is_file($file)) {
            unlink($file);

            $thumbnail = $this->thumbnailer->getStorageOrganizer()
            ->getThumbnailPath($file, 'list');

            if (is_file($thumbnail)) {
                unlink($thumbnail);
            }

            return $this->redirect()->toUrl($redirect);
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    private function getFoldersTree($path, $denied) 
    {
        if (!is_dir($path)) {
            return [];
        }

        $iterator = new DirectoryIterator($path);
        $tree     = [];
        $current  = rtrim($this->getRequest()->getQuery('path', '/'), '\\/');
        $query    = $this->getRequest()->getUri()->getQueryAsArray();

        foreach ($iterator as $file) {
            $pathname = str_replace('\\', '/', $file->getPathname());

            if ($file->isFile() || 
                $file->isDot() || 
                in_array($pathname, $denied)
            ) {
                continue;
            }

            $filepath = ltrim(
                str_replace(
                    $this->config['images_directory'],
                    '',
                    $pathname
                ),
                '\\/'
            );

            $query['path'] = $filepath;

            $tree[] = [
                'folder'    => clone $file,
                'href'      => $this->url()->fromRoute(
                    'app/admin/images-manager',
                    [
                        'locale' => $this->locale()->current()
                    ],
                    [
                        'query' => $query,
                    ]
                ),
                'isActive'  => (strpos($current, $filepath) === 0 ? true: false),
                'isCurrent' => ($filepath == $current),
                'children'  => $this->getFoldersTree($pathname, $denied) 
            ];
        }

        return $tree;
    }

    private function deleteFolder($folder)
    {
        $folder = new DirectoryIterator($folder);

        foreach ($folder as $file) {
            if ($file->isDot()) {
                continue;
            } else if ($file->isDir()) {
                $this->deleteFolder($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
    }

    private function setImageIndex(SessionContainer $session)
    {
        if (!isset($session->thumbnails[$session->current['thumbnail']])) {
            $session->current['image'] = 0;
            return;
        }

        for ($i=0; $i<$session->imagesCount; $i++) {
            if (!isset($session->thumbnails[$session->current['thumbnail']][$i])) {
                $session->current['image'] = $i;
                return;
            }
        }
    }
}
