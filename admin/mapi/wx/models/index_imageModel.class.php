<?php
/**
 *
 */
class index_imageModel extends NewModel
{
    public function getList($where = array(), $field = 'title name,image img,url', $limit = '', $order = 'sort desc')
    {
    	$where['is_effect']=1;
        return self::parseValue($this->field($field)->where($where)->limit($limit)->order($order)->select());
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
        }
        return $data;
    }
}
