<?php
namespace Home\Controller;

/**
 *@author hyzheng
 * TuangouController
 * 团购控制器
 */
class TuangouController extends CommonController {
	 /**
     * 团购信息管理列表
     * @return
     */
    public function index() {
        $city_id = I('request.city_id');
        $where = '1';
        $city_where = 'level = 1';

        //根据用户得到城市数组
        $allowed_cities = D('City', 'Service')->city_priv();
        if(is_array($allowed_cities)) {
            for($i=0; $i<count($allowed_cities); $i++) {
                if($i==count($allowed_cities)-1) {
                    $city_string = $city_string."'".$allowed_cities[$i]."'";
                    break;  
                }
                $city_string = $city_string."'".$allowed_cities[$i]."',";
            }
             $where .= " and city in (".$city_string.")";
             $city_where .= " and id in (".$city_string.")";
        }
        

        

        if(!empty($city_id)) {
            $where .= ' and city="'.$city_id.'"' ;
            $param['city_id'] =  $city_id;
            $this->assign('old_city_id', $city_id);
        }
        $count = M('tuangou')->where($where)->count();
        $Page  = new \Think\Page($count,20, implode('&', $param));
        $Page->lastSuffix=false;
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('first','首页');
        $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        if (!empty($param)) {
            $Page->parameter = $param;
        }
        $show  = $Page->show();
        $this->assign('page',$show);
        $tuangous = M('tuangou')->where($where)->order('created_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('tuangous', $tuangous);
        $all_city = M('city')->where($city_where)->getField('id,name');
        $this->assign('count', $count);
        $this->assign('all_city', $all_city);
        $this->display();   
    }

    /**
     * 编辑新团购信息
     * @return
     */
    public function add() {
        $where = 1;
        $city_where = 'level = 1';
        $allowed_cities = D('City', 'Service')->city_priv();
        if(is_array($allowed_cities)) {
            for($i=0; $i<count($allowed_cities); $i++) {
                if($i==count($allowed_cities)-1) {
                    $city_string = $city_string."'".$allowed_cities[$i]."'";
                    break;  
                }
                $city_string = $city_string."'".$allowed_cities[$i]."',";
            }
            $city_where .= " and id in (".$city_string.")";
        }
        $all_city = M('city')->where($city_where)->getField('id,name');
        $this->assign('all_city', $all_city);
        $this->display();
    }
    /**
      *新增团购信息
      *@return 
      */
    public function create() {
        $data = $_POST;
        if (!isset($data)||!IS_POST) {
            $this->errorReturn('无效的操作!');
        }

        //进行数据库存储
        $result = D('Tuangou', 'Service')->create($data);
        if (!$result['status']) {
            $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('插入成功', U('Tuangou/index'));
    }   
    /**
     *团购信息
     *@return
     */
    public function show() {
    	if (!isset($_GET['id'])) {
            $this->error('您需要查看的模型不存在！');
        }

        $tuangou = D('Tuangou', 'Service')->getById($_GET['id']);
        if (empty($tuangou)) {
            $this->error('您需要查看的模型不存在！');   
        }

        // 得到input的显示顺序
        $orders = array();
        foreach ($model['fields'] as $key => $field) {
            $input = M('Input')->field('show_order')
                               ->where("field_id={$field['id']}")
                               ->find();
            $model['fields'][$key]['show_order'] =
                (is_null($input) || 0 == $input['show_order'])
                    ? 0 : $input['show_order'];
            $orders[$key] = $model['fields'][$key]['show_order'];
        }

        // field按show_order排序
        array_multisort($orders, $model['fields']);

        $this->assign('model', $model);
        $this->display();
    }

    /**
      *标记团购信息
      *@return json
      */
    public function edit() {
        if (!isset($_GET['id'])) {
            $this->error('您需要编辑的信息不存在！');
        }

        $tuangou = D('Tuangou', 'Service')->getById($_GET['id']);
        if (is_null($tuangou)) {
            $this->error('您需要编辑的团购不存在！');
        }
        $city = M('city')->find($tuangou['city']);
        $this->assign('city_name', $city['name']);
        $this->assign('tuangou', $tuangou);
        $this->display();
    }

    /**
      *更新团购信息
      *@return json
      */
    public function update() {
        $data = $_POST;
        $id = $data[tuangou][id];
        $result = D('Tuangou', 'Service')->update($data);

        if(!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新成功', U('Tuangou/edit', array('id' => $id)));  
    }

    /**
      *删除团购信息
      *@return json
      */
    public function delete() {
        $id = I('get.id');
        if((!isset($id) || empty($id))) {
            $this->errorReturn('请选择要删除的信息!');
        }
        if( isset($id)&&!is_array($id) ){
            $id = explode("," , $id);      
        }
        foreach ($id as $tuangou_id) {
            D('Tuangou')->relation(true)->delete($tuangou_id);
        }
        $this->successReturn('删除成功');  
    }

    /**
      *查看报名信息
      *@return 
      */
    public function enroll() {
        $id = I('get.id');
        $enroll = M('enroll')->where("tuangou_id=$id")->select();
        $count = count($enroll);
        $this->assign('count', $count);
        $this->assign('enroll', $enroll);
        $this->display();
    }

    /**
      *删除报名信息
      *@return 
      */
    public function delete_enroll() {
        $id = I('get.id');
        if((!isset($id) || empty($id))) {
            $this->errorReturn('请选择要删除的信息!');
        }
        if( isset($id)&&!is_array($id) ){
            $id = explode("," , $id);      
        }
        foreach ($id as $enroll_id) {
            D('enroll')->delete($enroll_id);
        }
        $this->successReturn('删除成功');  
    }

    /**
      *处理ckeditor图片上传
      *@return mix
      */
    public function upload() {
        $extensions = array("jpg","bmp","gif","png");  
        $uploadFilename = $_FILES['upload']['name'];
        $extension = pathInfo($uploadFilename,PATHINFO_EXTENSION);  
        if(in_array($extension,$extensions)) {
            $uploadPath = './Public/Uploads/';
            $uuid = str_replace('.','',uniqid("",TRUE)).".".$extension;
            $desname = $uploadPath.$uuid;  
            $previewname = '/easy-admin/public/Uploads/'.$uuid;
            $tag = move_uploaded_file($_FILES['upload']['tmp_name'],$desname);
            $callback = $_REQUEST["CKEditorFuncNum"];
            echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'".$previewname."','');</script>";  
        } else {  
            echo "<font color=\"red\"size=\"2\">*文件格式不正确（必须为.jpg/.gif/.bmp/.png文件）</font>";
        }
    }

    /**
      *处理uploadify大图上传
      *@return 
      */
    public function uploadBigPhoto() {
        dump('aa');exit();
        $extensions = array("jpg", "bmp", "gif", "png");
        $uploadFilename = $_FILES['file_upload']['name'];
        $extensions = pathInfo($uploadFilename, PATHINFO_EXTENSION);
        if(in_array($extension, $extensions)) {
            $uploadPath = './Public/Uploads';
            $uuid = str_replace('.','',uniqid("",TRUE)).".".$extension;
            $desname = $uploadPath.$uuid;
            $tag = move_uploaded_file($_FILES['upload']['tmp_name'],$desname);
           
        }
    }

    /**
     * 检查标题可用性
     * @return
     */
    public function checkTuangouTitle() {
        $result = D('Tuangou', 'Service')->checkTuangouTitle($_GET['tuanogu_title'],
                                                        $_GET['id']);
        if ($result['status']) {
            return $this->successReturn('团购名称可用！');
        }

        return $this->errorReturn($result['data']['error']);
    }
}   