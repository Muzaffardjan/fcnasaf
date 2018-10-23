<?php
namespace Soccer;

use ImagesManager\Thumbnail\Thumbnailer;

return [
    'router' => [
        'routes' => [
            'app' => [
                'child_routes' => [
                    'admin' => [
                        'child_routes' => [
                            'soccer' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'     => '/soccer',
                                    'defaults'  => [
                                        '__NAMESPACE__' => 'Soccer\Controller',
                                    ],
                                ],
                                'child_routes' => [
                                    'club' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/club[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'ClubManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'club-player' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/club/player[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'ClubPlayerManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'player' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/player[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'PlayerManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'player-positions' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/player/positions[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'PlayerPositions',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'tournaments' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/tournament[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'TournamentManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'matches' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/matches[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'MatchesManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'match-details' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/match/details[/:action[/:match]]',
                                            'contraints' => [
                                                'match'  => '[0-9]+',
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'MatchDetailsManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'match-events' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/match/events[/:action[/:match]]',
                                            'contraints' => [
                                                'match'  => '[0-9]+',
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'MatchEvents',
                                            ],
                                        ],
                                    ],
                                    'seasons' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/tournament_[:tournament]/seasons[/:action[/:id]]',
                                            'contraints' => [
                                                'id'         => '[0-9]+',
                                                'tournament' => '[0-9]+',
                                                'action'     => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'SeasonsManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'stages' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/tournament_[:tournament]/season_[:season]/stages[/:action[/:id]]',
                                            'contraints' => [
                                                'id'         => '[0-9]+',
                                                'tournament' => '[0-9]+',
                                                'season'     => '[0-9]+',
                                                'action'     => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'StagesManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'group-play' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/stage_[:stage]/group-play[/:action[/:id]]',
                                            'contraints' => [
                                                'id'     => '[0-9]+',
                                                'stage'  => '[0-9]+',
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'GroupPlayManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'play-off' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/stage_[:stage]/play-off[/:action[/:id]]',
                                            'contraints' => [
                                                'id'     => '[0-9]+',
                                                'stage'  => '[0-9]+',
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'PlayOffManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'series' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/stage_[:stage]/series[/:action[/:id]]',
                                            'contraints' => [
                                                'id'     => '[0-9]+',
                                                'stage'  => '[0-9]+',
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'SeriesManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'tours' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/tournament_[:tournament]/season_[:season][/stage_[:stage]]/tours[/:action[/:id]]',
                                            'contraints' => [
                                                'id'         => '[0-9]+',
                                                'tournament' => '[0-9]+',
                                                'season'     => '[0-9]+',
                                                'stage'      => '[0-9]+',
                                                'action'     => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'ToursManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'staff' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/staff[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'StaffManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'staff-group' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/staff/group[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'StaffGroupManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'stadiums' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route' => '/stadiums[/:action[/:id]]',
                                            'contraints' => [
                                                'id'    => '[0-9]+',
                                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => 'StadiumsManage',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'staff' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/staff',
                            'defaults' => [
                                '__NAMESPACE__' => 'Soccer\Controller',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'member' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route'     => '/members[/:action[/:id]]',
                                    'contraints' => [
                                        'id'    => '[0-9]+',
                                        'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                    ],
                                    'defaults'  => [
                                        'controller' => 'Staff',
                                        'action'     => 'index',
                                    ],
                                ],
                            ],
                            'group' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route'     => '/group[/:action[/:id]]',
                                    'contraints' => [
                                        'id'    => '[0-9]+',
                                        'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                                    ],
                                    'defaults'  => [
                                        'controller' => 'StaffGroup',
                                        'action'     => 'view',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'match' => [
                        'type' => 'Segment',
                        'options' => [
                            'route'     => '/match[/:action[/:id]]',
                            'contraints' => [
                                'id'    => '[0-9]+',
                                'action'=> '[a-zA-Z][a-zA-Z0-9]+',
                            ],
                            'defaults'  => [
                                '__NAMESPACE__' => 'Soccer\Controller',
                                'controller' => 'Match',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\RoutePermissionsGuard' => [
                'app/admin/soccer*' => ['content.manage'],
            ],
        ],
    ],
    'navigation' => [
        'admin' => [
            [
                'label'         => 'Staff manager',
                'uri'           => 'app/admin/soccer',
                'icon'          => 'wb-users',
                'permission'    => 'content.manage',
                'order'         => 5,
                'pages'         => [
                    [
                        'label' => 'Staff group',
                        'icon'  => 'wb-list',
                        'route' => 'app/admin/soccer/staff-group',
                    ],
                    [
                        'label' => 'Staff',
                        'icon'  => 'wb-user',
                        'route' => 'app/admin/soccer/staff',
                    ],
                ],
            ],
            [
                'label' => 'Soccer',
                'uri'   => 'app/admin/soccer',
                'icon'  => 'fa fa-soccer-ball-o',
                'permission' => 'content.manage',
                'order' => 6,
                'pages' => [
                    [
                        'label' => 'Clubs',
                        'icon'  => 'wb-grid-9',
                        'route' => 'app/admin/soccer/club',
                    ],
                    [
                        'label' => 'Stadiums',
                        'icon'  => 'fa-map-marker',
                        'route' => 'app/admin/soccer/stadiums',
                    ],
                    [
                        'label' => 'Players positions',
                        'icon'  => 'fa-street-view',
                        'route' => 'app/admin/soccer/player-positions',
                    ],
                    [
                        'label' => 'Players',
                        'icon'  => 'wb-users',
                        'route' => 'app/admin/soccer/player',
                    ],
                    [
                        'label' => 'Tournaments',
                        'icon'  => 'fa-trophy',
                        'route' => 'app/admin/soccer/tournaments',
                    ],
                    [
                        'label' => 'Matches',
                        'icon'  => 'fa-calendar-o',
                        'route' => 'app/admin/soccer/matches',
                    ],
                ],
            ]
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__.'_entities' => [
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/'.__NAMESPACE__.'/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__.'\Entity' => __NAMESPACE__.'_entities',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Soccer' => __DIR__ . '/../view',
        ]
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../data/languages',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'images-manager' => [
        'thumbnails' => [
            'soccer_staff_photo' => [
                'directory' => 'thumbnails/soccer/staff',
                'width'     => 640,
                'height'    => 480,
                'quality'   => 80,
                'auto_mode' => false,
                'watermark' => false,
            ],
            'soccer_club_logo' => [
                'directory' => 'thumbnails/soccer/clubs',
                'width'     => 256,
                'height'    => 256,
                'auto_mode' => false,
                'watermark' => false,
            ],
            'soccer_club_logo_small' => [
                'directory' => 'thumbnails/soccer/clubs/small',
                'width'     => 64,
                'height'    => 64,
                'auto_mode' => false,
                'watermark' => false,
            ],
            'soccer_player_profile' => [
                'directory' => 'thumbnails/soccer/player/profile',
                'width'     => 768,
                'height'    => 768,
                'auto_mode' => false,
                'watermark' => false,
            ],
            'soccer_player_profile_small' => [
                'directory' => 'thumbnails/soccer/player/profile/small',
                'width'     => 128,
                'height'    => 128,
                'auto_mode' => false,
                'watermark' => false,
            ],
            'soccer_player_card' => [
                'directory' => 'thumbnails/soccer/player/card',
                'width'     => Thumbnailer::DIMENSION_AUTO,
                'height'    => Thumbnailer::DIMENSION_AUTO,
                'auto_mode' => false,
                'watermark' => false,
            ],
        ],
    ],
];
