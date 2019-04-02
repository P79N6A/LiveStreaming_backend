<?php
/**
 *
 */
class userModel extends NewModel
{
    public function getInfo($id, $field = 'head_image,nick_name,signature')
    {
        return self::parseValue($this->field('head_image,nick_name,signature')->selectOne(array('id' => $id)));
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
