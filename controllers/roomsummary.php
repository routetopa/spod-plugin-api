<?php

class SPODAPI_CTRL_RoomSummary extends OW_ActionController
{
    const ERR_INVALID_ROOM_ID = 'Invalid room id';
    const ERR_MISSING_PARAMETER = 'Parameter missing: id';

    const PARAMETER_ROOM_ID = 'id';

    private function output_success($result) {
        echo json_encode([
            'status' => 'success',
            'result' => $result,
        ], JSON_UNESCAPED_SLASHES);
        die();
    }

    private function output_error($error) {
        echo json_encode([
            'status' => 'error',
            'error' => $error,
        ]);
        die();
    }

    public function index( array $params = null ) {
        $room_id =  @$_GET[self::PARAMETER_ROOM_ID];
        if (!$room_id) {
            return $this->output_error(self::ERR_MISSING_PARAMETER);
        }

        $room_id = filter_var($room_id, FILTER_VALIDATE_INT);
        if (!$room_id) {
            return $this->output_error(self::ERR_INVALID_ROOM_ID);
        }

        $public_room = SPODPUBLIC_BOL_Service::getInstance()->getPublicRoomById($room_id);
        if (!$public_room) {
            return $this->output_error(self::ERR_INVALID_ROOM_ID);
        }

        $comments = BOL_CommentService::getInstance()->findCommentList('spodpublic_topic_entity', $room_id);

        $userIds = array();
        foreach ($comments as $comment) {
            $userIds[$comment->userId] = $comment->userId;
        }

        $users = BOL_UserService::getInstance()->findUserListByIdList($userIds);
        $userDisplayNames = BOL_UserService::getInstance()->getDisplayNamesForList($userIds);
        $userUrls = BOL_UserService::getInstance()->getUserUrlsForList($userIds);
        $avatarUrls = BOL_AvatarService::getInstance()->getAvatarsUrlList($userIds);

        $result = array();
        foreach ($comments as $comment) {
            $result[] = array(
                'userId' => $comment->userId,
                //'username' => $users[ $comment->userId ]->username,
                'userUrl' => $userUrls[ $comment->userId ],
                'userDisplayName' => $userDisplayNames[ $comment->userId ],
                'userAvatarUrl' => $avatarUrls[ $comment->userId ],
                'timestamp' => $comment->createStamp,
                'message' => $comment->message,
            );
        }

        echo '<pre>'; print_r($result);die();

        return $this->output_success($result);
    }
}