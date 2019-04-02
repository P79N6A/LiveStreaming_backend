<?php
/**
 *
 */
class course_seasonModel extends NewModel
{
    public function getList($where = array(), $field = 'id,season,is_vip', $limit = '', $order = 'season')
    {
        $where['is_effect'] = 1;
        $where['is_delete'] = 0;
        return self::parseValue($this->field($field)->limit($limit)->order($order)->select($where));
    }
    public function getOneById($id, $field = 'id,title,content,img,pid,season,is_vip,video_url,sound_url')
    {
        $where = array('id' => intval($id));
        $where['is_effect'] = 1;
        $where['is_delete'] = 0;
        return self::parseValue($this->field($field)->selectOne($where));
    }
    public function getRecommendList($field, $limit = '20')
    {
        self::$sql = "SELECT * FROM (SELECT
            `cs`.`id`,
            `cs`.`title` `name`,
            `cs`.`img`,
            `cs`.`long_time`,
            `cs`.view_times,
            pid
        FROM
            `fanwe_course_season` `cs`,
            `fanwe_course` `c`
        WHERE
            `cs`.`is_effect` = '1'
        AND `cs`.`is_delete` = '0'
        AND `cs`.`pid` = `c`.`id`
        AND `c`.`type` = '0'
        ORDER BY
            `cs`.view_times DESC
        ) as a GROUP BY pid order by view_times desc LIMIT 20";
        return self::parseValue(Connect::query(self::$sql));
        $where['cs.is_effect'] = 1;
        $where['cs.is_delete'] = 0;
        $where['cs.pid'] = array('c.id');
        $where['c.type'] = 0;
        return self::parseValue($this->table('course_season cs,course c')->field($field)->group('pid')->limit($limit)->order(array(array('view_times desc')))->select($where));
    }
    public static function getUrlById($id)
    {
        return get_domain() . '/weixin/index.php?itype=wx&ctl=course&act=detail&id=' . $id;
    }
    public function incView($id)
    {
        return $this->update(array('view_times' => array('`view_times`+1')), array('id' => $id));
    }
    protected static function parseValue($data)
    {
        if (isset($data[0])) {
            foreach ($data as $key => $value) {
                $data[$key] = self::parseValue($value);
            }
        } else {
            if (isset($data['img'])) {
                $data['img'] = get_spec_image($data['img']);
            }
            if (isset($data['id'])) {
                $data['url'] = self::getUrlById($data['id']);
            }
            if (isset($data['sound_url'])) {
                $data['sound_url'] = get_spec_image($data['sound_url']);
            }
            if (isset($data['long_time'])) {
                $long_time = $data['long_time'];
                $hour = str_pad(intval($long_time / 3600), 2, '0', 0);
                $minutes = str_pad(intval($long_time % 3600 / 60), 2, '0', 0);
                $seconds = str_pad(intval($long_time % 60), 2, '0', 0);
                $data['long_time'] = (intval($hour) ? "$hour:" : '') . "$minutes:$seconds";
            }
            if (isset($data['is_vip'])) {
                $data['is_vip'] = intval($data['is_vip']);
            }
            if (isset($data['creat_time'])) {
                $data['creat_time'] = to_date($data['creat_time'], 'y-m-d');
            }
        }
        return $data;
    }
}
