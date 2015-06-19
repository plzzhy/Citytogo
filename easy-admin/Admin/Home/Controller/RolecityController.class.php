<?php
namespace Home\Controller;

/**
 * @author hyzheng
 * RolecityController
 * 角色城市权限管理
 */
class RolecityController extends CommonController {

    /**
      *角色城市权限编辑列表
      *@author hyzheng
      *@param 
      *@return 
      */
    public function index() {
        $roles = D('Role', 'Service')->getRoles();

        $this->assign('roles', $roles);
        $this->assign('rows_count', count($roles));
        $this->display();
    }

    /**
      *编辑
      *@author hyzheng
      *@param 
      *@return 
      */
    public function edit() {
        $id = $_GET['id'];
        $role = M('role')->find($id);
        $all_city = M('city')->getField('id, name');
        $cities_id = M('role_city')->where("role_id=$id")->getField('city_id', true);
        $this->assign('role', $role);
        $this->assign('cities_id', $cities_id);
        $this->assign('all_city', $all_city);
        $this->display();
    }

    /**
      *更新
      *@author hyzheng
      *@param 
      *@return 
      */
    public function update() {
        $role_id = $_POST['id'];
        $city_id = $_POST['city'];
        //进行先删除后重新添加的方式更新
        M('role_city')->where("role_id=$role_id")->delete();
        foreach ($city_id as $k=>$v) {
          $insertList[] = array('role_id'=>$role_id, 'city_id'=>$v);
        }

        M('role_city')->addAll($insertList);
        $this->success('更新成功');
    }


}
