<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Craftyx\SlackApi\Facades\SlackChat;
use Craftyx\SlackApi\Facades\SlackUser;
use Craftyx\SlackApi\Facades\SlackGroup;
use Craftyx\SlackApi\Facades\SlackTeam;
use Craftyx\SlackApi\Facades\SlackChannel;

class Slack extends Model
{

    public static function removeFromChannel($user, $stage)
    {
        $group_name = env('SLACK_STAGE_PREFIX', 'test-stage').$stage;
        $group_id = self::getGroupIDFromName($group_name);
        return SlackGroup::kick($group_id, $user);
    }

    public static function addToChannel($user, $stage)
    {
        $group_name = env('SLACK_STAGE_PREFIX', 'test-stage').$stage;
        $group_id = self::getGroupIDFromName($group_name);
        return SlackGroup::invite($group_id, $user);
    }
    
    public static function removeFromGroup($user, $group_name)
    {
        $group_id = self::getGroupIDFromName($group_name);
        return SlackGroup::kick($group_id, $user);
    }

    public static function addToGroup($user, $group_name)
    {
        $group_id = self::getGroupIDFromName($group_name);
        return SlackGroup::invite($group_id, $user);
    }

    private static function getGroupIDFromName($stage_name){
        
        $stage_name = strtolower($stage_name);
        $groups = SlackGroup::lists(true);
                    
        foreach($groups->groups as $group){
            if($group->name == $stage_name){
                return $group->id;
                break;
            }
        }
    }

    public static function removeAddToGroup($user_id, $remove_from, $add_to)
    {
        $data = [];
        $remove = self::removeFromGroup($user_id, $remove_from);
        $data['removed_group'] = $remove;
         
        $added_group = self::addToGroup($user_id, $add_to);
        $data['added_group'] = $added_group;
        
        if($added_group->ok){
            return true;
        }
        return $data;
    }
}