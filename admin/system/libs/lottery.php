<?php
class LotteryBaseAction{
	/** 抽奖
	 * @param unknown_type $proArr 各中奖的段
	 * @param unknown_type $total 总共数量 默认为100
	 * @return int 抽中的数
	 */
	function get_rand($list_multiple,$total) {
        $proArr = $this->prize_arr($list_multiple);
        $result = 0;
        $randNum = mt_rand(1, $total);
        //$randNum = 34;
        foreach ($proArr as $k => $v) {
            //if ($v['v']!=''){//奖项存在或者奖项之外
                if ($randNum>$v['start']&&$randNum<=$v['end']){
                    $result=$k;
                    break;
                }
            //}
        }
        return $result;
	}

    function prize_arr($list_multiple){
        $multiple_arr = array();
		foreach($list_multiple as $k=>$v){
           $multiple_arr[] =intval($v['probability']);
        }
	    $a = 1;
        $prize_arr = array();
        foreach($list_multiple as $k=>$v){
            $prize_arr[$v['multiple']]['id'] = $v['id'];
            $prize_arr[$v['multiple']]['name'] = $v['name'];
            $prize_arr[$v['multiple']]['multiple'] = $v['multiple'];
            $prize_arr[$v['multiple']]['probability'] = $v['probability'];
            $prize_arr[$v['multiple']]['is_effect'] = $v['is_effect'];
            $percent_info = $this->percent_info($multiple_arr,$a-1);
            $prize_arr[$v['multiple']]['start'] =$percent_info['start'];
            $prize_arr[$v['multiple']]['end'] = $percent_info['end'];
            $a++;
        }
        return $prize_arr;
    }

    /**
     * 计算 摇奖区间
     * @param $prize_flow_arr
     * @param $k
     * @return mixed
     */
	function percent_info($multiple_arr,$k){
        $section = array();
	    if($k){
			for($i=0;$i<$k;$i++){
                $section['start'] += $multiple_arr[$i];
   			}
            $section['end'] = $section['start']+$multiple_arr[$k];
		}else{
	        if($multiple_arr[$k]>0){
                $section['start'] = 1;
                $section['end'] =$multiple_arr[$k];
            }else{
                $section['start'] = 0;
                $section['end'] = 0;
            }
		}
   		return $section;
	}
}

?>