<?php
/**
 *
 */
class weixin_distributionModel extends NewModel
{
    public function addWithUnionId($unionid, $pid)
    {
        if (!$unionid) {
            return false;
        }
        $user = self::build('user')->field('id')->selectOne(['wx_unionid' => $unionid]);
        if (!$user) {
            return false;
        }
        return $this->add($user['id'], $pid);
    }
    public function add($user_id, $pid, $first_rate = 0, $second_rate = 0)
    {
        if ($pid) {
            $p = $this->field('topid')->selectOne(['user_id' => $pid]);
            $topid = intval($p['topid']);
        } else {
            $topid = $user_id;
        }
        if (!$topid) {
            return $topid;
        }
        $data = [
            'user_id'     => intval($user_id),
            'pid'         => intval($pid),
            'topid'       => intval($topid),
            'first_rate'  => intval($first_rate),
            'second_rate' => intval($second_rate),
            'create_time' => NOW_TIME,
        ];
        return $this->insert($data);
    }
    public function getParent($user_id)
    {
        $self = $this->field('pid')->selectOne(['user_id' => $user_id]);
        if ($self['pid']) {
            return $this->selectOne(['user_id' => $self['pid']]);
        }
        return null;
    }
    public function getChild($pid, $field = false)
    {
        if (!$pid) {
            return [];
        }
        if (is_array($pid)) {
            $where = ['pid' => ['in', $pid]];
        } else {
            $where = ['pid' => intval($pid)];
        }
        if ($field === false) {
            $child = $this->field('user_id')->select($where);
            $array = [];
            foreach ($child as $value) {
                $array[] = intval($value['user_id']);
            }
            return $array;
        } else {
            return $this->field($field)->select($where);
        }
    }
    public function getUserListByPid($pid, $topid = 0)
    {
        $table = 'user u,weixin_distribution d';
        $field = 'u.id,u.nick_name,u.head_image,p.pid,p.first_rate,p.second_rate,p.create_time';
        $where = ['pid' => intval($pid)];
        if ($topid) {
            $where['topid'] = intval($topid);
        }
        return self::parseValue($this->table($table)->field($field)->select($where));
    }
    protected static function parseValue($data)
    {
        if (isset($data[0])) {
            foreach ($data as $key => $value) {
                $data[$key] = self::parseValue($value);
            }
        } else {
            if (isset($data['head_image'])) {
                $data['head_image'] = get_spec_image($data['head_image']);
            }
        }
        return $data;
    }
}
