<?php
/**
 *
 */
class courseModel extends NewModel
{
    public function getList($where, $field = 'id,title,content,img,is_hot,is_recommend,type', $limit = '', $order = 'id desc')
    {
        $where['is_effect'] = 1;
        $where['is_delete'] = 0;
        return self::parseValue($this->field($field)->limit($limit)->order($order)->select($where));
    }
    public function count($where = array())
    {
        $where['is_effect'] = 1;
        $where['is_delete'] = 0;
        $count              = $this->field(array(array('count(1) count')))->selectOne($where);
        return $count ? $count['count'] : $count;
    }
    public function sumViews($course_id)
    {
        $where = array('pid' => intval($course_id), 'is_effect' => 1, 'is_delete' => 0);
        $sum   = Model::build('course_season')->field(array(array('sum(`view_times`) sum')))->selectOne($where);
        return $sum ? $sum['sum'] : $sum;
    }
    public function newest($course_id)
    {
        $where = array('pid' => intval($course_id), 'is_effect' => 1, 'is_delete' => 0);
        $max   = Model::build('course_season')->field(array(array('max(`create_time`) max')))->selectOne($where);
        return $max ? $max['max'] : $max;
    }
    public function getOneById($id, $field = 'id,title,content,img,is_hot,is_recommend,type')
    {
        $where = array('id' => intval($id), 'is_effect' => 1, 'is_delete' => 0);
        return self::parseValue($this->field($field)->selectOne($where));
    }
    public static function getUrlById($id)
    {
        return get_domain() . '/weixin/index.php?itype=wx&ctl=course&act=detail&pid=' . $id;
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
            if (isset($data['creat_time'])) {
                $data['creat_time'] = to_date($data['creat_time'],'y-m-d');
            }
        }
        return $data;
    }
}
