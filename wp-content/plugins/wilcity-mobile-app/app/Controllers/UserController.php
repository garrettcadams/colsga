<?php
namespace WILCITY_APP\Controllers;

use WilokeListingTools\Controllers\ProfileController;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

class UserController
{
    use VerifyToken;
    use JsonSkeleton;
    use ParsePost;
    
    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'get-profile', [
                'methods'  => 'GET',
                'callback' => [$this, 'getProfiles']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'get-short-profile', [
                'methods'  => 'GET',
                'callback' => [$this, 'getShortProfile']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'search-users', [
                'methods'  => 'GET',
                'callback' => [$this, 'searchUsers']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'list-users', [
                'methods'  => 'GET',
                'callback' => [$this, 'getShortUsersInfo']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'users/(?P<id>\d+)', [
                'methods'  => 'GET',
                'callback' => [$this, 'getUserShortInfo']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'get-my-profile-fields', [
                'methods'  => 'GET',
                'callback' => [$this, 'getProfileFields']
            ]);
        });
        
        add_action('rest_api_init', function () {
            register_rest_route(WILOKE_PREFIX.'/v2', 'put-my-profile', [
                'methods'  => 'POST',
                'callback' => [$this, 'putMyProfile']
            ]);
        });
        
        add_filter('determine_current_user', [$this, 'filterUserLoggedInStatusInRestAPI']);
    }
    
    public function filterUserLoggedInStatusInRestAPI($status)
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $status;
        }
        
        $this->getUserID();
        
        return $oToken->userID;
    }
    
    private function getQuickUserInformation($oUser)
    {
        return [
            'userID'      => $oUser->ID,
            'displayName' => $oUser->display_name,
            'avatar'      => User::getAvatar($oUser->ID),
            'firebaseID'  => User::getFirebaseUserID()
        ];
    }
    
    public function getShortUsersInfo()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $aMessage = [
            'status' => 'error',
            'msg'    => 'The user does not exists'
        ];
        
        if (!isset($_GET['s']) || empty($_GET['s'])) {
            return $aMessage;
        }
        
        $aParsedUsers = explode(',', $_GET['s']);
        $aParsedUsers = array_map(function ($userName) {
            return trim($userName);
        }, $aParsedUsers);
        
        if (is_numeric($aParsedUsers[0])) {
            $by = 'ID';
        } else {
            $by = 'login';
        }
        
        $aUserInfo = [];
        foreach ($aParsedUsers as $username) {
            $oUser = get_user_by($by, $username);
            if (empty($oUser) || is_wp_error($oUser)) {
                continue;
            }
            
            $aUserInfo[] = [
                'userID'      => $oUser->ID,
                'displayName' => $oUser->display_name,
                'avatar'      => User::getAvatar($oUser->ID)
            ];
        }
        
        return [
            'status'  => 'success',
            'aResult' => $aUserInfo
        ];
    }
    
    public function getUserShortInfo($aData)
    {
        $aMessage = [
            'status' => 'error',
            'msg'    => 'The user does not exists'
        ];
        if (!isset($aData['id']) || empty($aData['id'])) {
            return $aMessage;
        }
        
        if (is_numeric($aData['id'])) {
            $by = 'ID';
        } else {
            $by = 'login';
        }
        $oUser = get_user_by($by, $aData['id']);
        if (empty($oUser) || is_wp_error($oUser)) {
            return $aMessage;
        }
        
        return [
            'status' => 'success',
            'oInfo'  => $this->getQuickUserInformation($oUser)
        ];
    }
    
    public function searchUsers()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        
        $aMessage = [
            'status' => 'error',
            'msg'    => 'We found no user info'
        ];
        
        if (!isset($_GET['s']) || empty($_GET['s'])) {
            return $aMessage;
        }
        
        $q = '*'.esc_attr(trim($_GET['s'])).'*';
        
        $args       = [
            'search'         => $q,
            'search_columns' => ['user_login', 'display_name', 'first_name', 'last_name'],
            'exclude'        => [$oToken->userID]
        ];
        $oUserQuery = new \WP_User_Query($args);
        $aUsers     = $oUserQuery->get_results();
        if (empty($aUsers)) {
            return $aMessage;
        }
        
        $aInfo = [];
        foreach ($aUsers as $oUser) {
            if (username_exists($oUser->login)) {
                continue;
            }
            
            $aInfo[] = $this->getQuickUserInformation($oUser);
        }
        
        return [
            'status'   => 'success',
            'aResults' => $aInfo
        ];
    }
    
    public function putMyProfile()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        
        $aFields = $this->parsePost();
        $msg     = 'profileHasBeenUpdated';
        if (isset($aFields['oPassword']) && !empty($aFields['oPassword'])) {
            $aRawPassword = json_decode(stripslashes($aFields['oPassword']), true);
            
            if (!empty($aRawPassword['confirm_new_password']) && !empty($aRawPassword['current_password']) && !empty($aRawPassword['new_password'])) {
                $aPasswordUpdate = [
                    'newPassword'        => $aRawPassword['new_password'],
                    'confirmNewPassword' => $aRawPassword['confirm_new_password'],
                    'currentPassword'    => $aRawPassword['current_password']
                ];
                $aStatus         = ProfileController::updatePassword($aPasswordUpdate, $oToken->userID);
                if ($aStatus['status'] == 'error') {
                    return [
                        'status' => 'error',
                        'msg'    => 'errorUpdatePassword'
                    ];
                } else {
                    $msg = 'passwordHasBeenUpdated';
                }
            }
        }
        
        if (isset($aFields['oBasicInfo']) && !empty($aFields['oBasicInfo'])) {
            $aBasicInfo = json_decode(stripslashes($aFields['oBasicInfo']), true);
            foreach ($aBasicInfo as $key => $aValue) {
                if ($key == 'avatar') {
                    if (is_array($aBasicInfo['avatar'])) {
                        $aBasicInfo['avatar']['value'][0]['src']      = $aBasicInfo['avatar']['base64'];
                        $aBasicInfo['avatar']['value'][0]['fileName'] = $aBasicInfo['avatar']['name'];
                        $aBasicInfo['avatar']['value'][0]['fileType'] = 'image/jpg';
                        
                        unset($aBasicInfo['avatar']['base64']);
                        unset($aBasicInfo['avatar']['name']);
                        unset($aBasicInfo['avatar']['type']);
                        unset($aBasicInfo['avatar']['uri']);
                    } else {
                        unset($aBasicInfo['avatar']);
                    }
                } else if ($key == 'cover_image') {
                    if (is_array($aBasicInfo['cover_image'])) {
                        $aBasicInfo['cover_image']['value'][0]['src']      = $aBasicInfo['cover_image']['base64'];
                        $aBasicInfo['cover_image']['value'][0]['fileName'] = $aBasicInfo['cover_image']['name'];
                        $aBasicInfo['cover_image']['value'][0]['fileType'] = 'image/jpg';
                        unset($aBasicInfo['cover_image']['base64']);
                        unset($aBasicInfo['cover_image']['name']);
                        unset($aBasicInfo['cover_image']['type']);
                        unset($aBasicInfo['cover_image']['uri']);
                    } else {
                        unset($aBasicInfo['cover_image']);
                    }
                } else {
                    unset($aBasicInfo[$key]);
                    if (!empty($aValue)) {
                        $aBasicInfo[$key]['value'] = $aValue;
                    }
                }
            }
            
            $aStatus = ProfileController::updateBasicInfo($aBasicInfo, $oToken->userID);
            if ($aStatus !== true) {
                return [
                    'status' => 'error',
                    'msg'    => 'errorUpdateProfile'
                ];
            }
        }
        
        if (isset($aFields['oFollowAndContact']) && !empty($aFields['oFollowAndContact'])) {
            $aRawFollowAndContact = json_decode(stripslashes($aFields['oFollowAndContact']), true);
            $aFollowAndContact    = [];
            foreach ($aRawFollowAndContact as $key => $aVal) {
                if ($key == 'social_networks') {
                    if (!empty($aVal) && !empty($key)) {
                        $aFollowAndContact[$key] = [
                            'value' => []
                        ];
                        foreach ($aVal as $aSocial) {
                            $aFollowAndContact[$key]['value'][] = [
                                'name' => $aSocial['id'],
                                'url'  => $aSocial['url']
                            ];
                        }
                    }
                } else {
                    $aFollowAndContact[$key]['value'] = $aVal;
                }
            }
            if (!empty($aFollowAndContact)) {
                ProfileController::updateFollowAndContact($aFollowAndContact, $oToken->userID);
            }
        }
        
        $aNewProfiles = $this->getUserProfile($oToken->userID);
        
        return [
            'status'   => 'success',
            'msg'      => $msg,
            'oResults' => $aNewProfiles
        ];
    }
    
    public function getProfileFields()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        
        $aFields = [
            [
                'heading' => 'basicInfo',
                'key'     => 'oBasicInfo',
                'aFields' => [
                    [
                        'label'          => 'firstName',
                        'key'            => 'first_name',
                        'type'           => 'text',
                        'validationType' => 'firstName',
                    ],
                    [
                        'label'          => 'lastName',
                        'key'            => 'last_name',
                        'type'           => 'text',
                        'validationType' => 'lastName',
                    ],
                    [
                        'label'          => 'displayName',
                        'key'            => 'display_name',
                        'type'           => 'text',
                        'required'       => true,
                        'validationType' => 'displayName',
                    ],
                    [
                        'label' => 'avatar',
                        'key'   => 'avatar',
                        'type'  => 'file'
                    ],
                    [
                        'label' => 'coverImg',
                        'key'   => 'cover_image',
                        'type'  => 'file'
                    ],
                    [
                        'label'          => 'email',
                        'key'            => 'email',
                        'type'           => 'text',
                        'validationType' => 'email',
                        'required'       => true
                    ],
                    [
                        'label' => 'position',
                        'key'   => 'position',
                        'type'  => 'text'
                    ],
                    [
                        'label' => 'introYourSelf',
                        'key'   => 'description',
                        'type'  => 'textarea'
                    ],
                    [
                        'label' => 'sendAnEmailIfIReceiveAMessageFromAdmin',
                        'key'   => 'send_email_if_reply_message',
                        'type'  => 'switch'
                    ]
                ]
            ],
            [
                'heading' => 'followAndContact',
                'key'     => 'oFollowAndContact',
                'aFields' => [
                    [
                        'label' => 'address',
                        'key'   => 'address',
                        'type'  => 'text'
                    ],
                    [
                        'label'          => 'phone',
                        'key'            => 'phone',
                        'type'           => 'text',
                        'validationType' => 'phone',
                    ],
                    [
                        'label'          => 'website',
                        'key'            => 'website',
                        'type'           => 'text',
                        'validationType' => 'url'
                    ],
                    [
                        'label'   => 'socialNetworks',
                        'key'     => 'social_networks',
                        'type'    => 'social_networks',
                        'options' => $this->buildSelectOptions(\WilokeSocialNetworks::getUsedSocialNetworks())
                    ]
                ]
            ],
            [
                'heading' => 'changePassword',
                'key'     => 'oPassword',
                'aFields' => [
                    [
                        'label' => 'currentPassword',
                        'key'   => 'current_password',
                        'type'  => 'password'
                    ],
                    [
                        'label' => 'newPassword',
                        'key'   => 'new_password',
                        'type'  => 'password'
                    ],
                    [
                        'label' => 'confirmNewPassword',
                        'key'   => 'confirm_new_password',
                        'type'  => 'password'
                    ]
                ]
            ]
        ];
        
        return [
            'status'   => 'success',
            'oResults' => $aFields
        ];
    }
    
    public function getShortProfile()
    {
        $oToken = $this->verifyPermanentToken();
        if (!$oToken) {
            return $this->tokenExpiration();
        }
        $oToken->getUserID();
        
        $oUser = get_user_by('ID', $this->userID);
        
        return [
            'status'  => 'success',
            'oResult' => $this->getQuickUserInformation($oUser)
        ];
    }
    
    public function getProfiles()
    {
        $oToken = $this->verifyPermanentToken();
        
        if (!$oToken) {
            $userID = isset($_GET['userID']) ? $_GET['userID'] : '';
        } else {
            $oToken->getUserID();
            $userID = $this->userID;
        }
        
        if (empty($userID)) {
            return [
                'status' => 'error',
                'msg'    => 'foundNoUser'
            ];
        }
        
        $aUserInfo = $this->getUserProfile($userID);
        
        return [
            'status'   => 'success',
            'oResults' => $aUserInfo
        ];
    }
}
