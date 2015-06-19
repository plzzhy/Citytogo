<?php
namespace Home\Controller;
use Think\Storage;
/**
 * @author hyzheng
 * TempletController
 * 模板管理
 */

class TempletController extends CommonController {
	/**
    *生成静态页
    *@return 
    */
    public function build_html($id) {
        $id = I('get.id');
        if((!isset($id) || empty($id))) {
            $this->errorReturn('请选择要生成的信息!');
        }
        if( isset($id)&&!is_array($id) ){
            $id = explode("," , $id);      
        }
        foreach ($id as $tuangou_id) {
            $tuangou = D('Tuangou', 'Service')->getById($tuangou_id);
            $city_id = $tuangou['city'];
            $city_slug = M('city')->where("id=$city_id")->getField('slug');
            $this->assign('tuangou', $tuangou);
            $this->buildHtml($tuangou_id, HTML_PATH . '/'.$city_slug.'/', '/Tpl/build_html', 'utf8');
            $this->successReturn('生成成功',3);
        }
        
    }
// http://localhost/easy-admin/admin.php/Templet/htm/hangzhou/378
    /**
      *查看静态页
      *return 
      */
    public function view($id) {
        $tuangou = D('Tuangou', 'Service')->getById($id);
        $city_id = $tuangou['city'];
   
        $city_slug = M('city')->where("id=$city_id")->getField('slug');
        $path = '/easy-admin/htm/'.$city_slug.'/'.$id.'.html';
        // dump(HTML_PATH . '/'.$city_slug.'/'.$id);exit();
        if(file_exists(HTML_PATH . '/'.$city_slug.'/'.$id.'.html') ) {
            redirect($path);  
        }
        $this->error('该页面还未生成,请先生成!');
    }
}