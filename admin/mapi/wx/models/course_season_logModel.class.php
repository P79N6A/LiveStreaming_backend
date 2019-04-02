<?php
/**
 *
 */
class course_season_logModel extends NewModel
{
    public function check($course_season_id, $user_id, $current_time)
    {
        if (!($course_season_id && $user_id)) {
            return false;
        }
        $create_date = to_date(NOW_TIME, 'Y-m-d');
        $where       = array(
            'create_date'      => $create_date,
            'course_season_id' => $course_season_id,
            'user_id'          => $user_id,
        );
        $log = $this->selectOne($where);
        if ($log) {
            $begin_time = $log['begin_time'];
            if ($current_time < $begin_time) {
                $begin_time = $current_time;
            }
            $end_time = $log['end_time'];
            if ($current_time > $end_time) {
                $end_time = $current_time;
            }
            $data = array(
                'current_time' => $current_time,
                'begin_time'   => $begin_time,
                'end_time'     => $end_time,
                'view_time'    => array('end_time-begin_time'),
            );
            return $this->update($data, array('id' => $log['id']));
        } else {
            Model::build('course_season')->incView($course_season_id);
            $where['view_time']    = 0;
            $where['current_time'] = $current_time;
            $where['begin_time']   = $current_time;
            $where['end_time']     = $current_time;
            return $this->insert($where);
        }
    }
    public function getCurrentTime($user_id, $course_season_id)
    {
        if ($user_id && $course_season_id) {
            $where = array(
                'user_id'          => intval($user_id),
                'course_season_id' => intval($course_season_id),
            );
            $res = $this->field('current_time')->order('id desc')->selectOne($where);
            if ($res) {
                return $res['current_time'];
            }
        }
        return 0;
    }
}
