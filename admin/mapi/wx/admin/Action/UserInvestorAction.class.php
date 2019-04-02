<?php
class UserInvestorAction extends CommonAction{
	public function index(){
        if(trim($_REQUEST['nick_name'])!='')
        {
            $map[DB_PREFIX.'user.nick_name'] = array('like','%'.trim($_REQUEST['nick_name']).'%');
        }

        $map[DB_PREFIX.'user.is_authentication'] = 1;
        $map[DB_PREFIX.'user.is_effect'] = 1;
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }

        //$name=$this->getActionName();
        $model = D ('User');
        if (! empty ( $model )) {
            $this->_list ( $model, $map );
        }
        $this->display ();
	}
	public function show_content(){
		$id=intval($_REQUEST['id']);
		$status=intval($_REQUEST['status']);
		
		$user=M("User")->getById($id);
		if($status==1){
			$user['do_info']='审核通过';
		}elseif($status==3){
			$user['do_info']='审核';
			$show_bnt=3;
		}else{
			$user['do_info']='审核不通过';
		}

		$user['business_card']=get_spec_image($user['business_card']);
		$user['work_card']=get_spec_image($user['work_card']);
		$user['work_contract']=get_spec_image($user['work_contract']);
		
		
 		$this->assign('user',$user);
		$this->assign('status',$status);
		$this->assign('show_bnt',$show_bnt);
		$this->display();
 	}
 	public function investor_go_allow(){
 		$id=intval($_REQUEST['id']);
 		$status=intval($_REQUEST['is_authentication']);
 		if($_REQUEST['investor_send_info']){
 			$investor_send_info=strim($_REQUEST['investor_send_info']);
 		}
        $user =  M("User")->getById($id);
 		if($user){
            $user_data['id'] = $user['id'];
 			$user_data['is_authentication']=$status;

 			if($investor_send_info){
 				$user_data['investor_send_info']=$investor_send_info;	
 			}else{
 				$user_data['investor_send_info']='';
 			}
 			
 			$list = M("User")->save($user_data);
            if ($list !== false){
                save_log($user_data['id']."审核操作成功",1);
            }else{
                save_log($user_data['id']."审核操作失败",0);
            }
  			$this->success("操作成功");
 		}else{
 			$this->error("没有该会员信息");
 		}
 	}
 	
 	
}
?>