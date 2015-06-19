<?php
namespace Home\Controller;

/**
 * @author hyzheng
 * CityController
 * 城市管理
 */
class CityController extends CommonController {
	/**
     * 城市管理首页
     * @return
     */
    public function index() {
        $cityService = D('City', 'Service');
        $cities = $cityService->getCities();
        $this->assign('cities', $cities);
        $this->assign('rows_count', count($cities));
        $this->display();
        
    }

    /**
     *增加子栏目编辑
     * @return json
     */
    public function add() {
    	$this->assign('fid', $_GET['id']);
    	$this->assign('f_name', $_GET['name']);
    	$this->display();
    }

    /**
      *新建子栏目
      *@return json
      */
     public function create() {
    	$fid = $_GET['fid'];
    	$data = $_POST;
    	$result = D('City', 'Service')->create($fid, $data);
        if (!$result['status']) {
            $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('新增子栏目成功', U('City/index'));
    }

    /**
      *编辑节点信息
      *@param int $id
      *@return json
      */
    public function edit() {
        if (!isset($_GET['id'])) {
            $this->error('您需要编辑的信息不存在！');
        }

        $city = D('City', 'Service')->getById($_GET['id']);
        if (is_null($city)) {
            $this->error('您需要编辑的团购不存在！');
        }
        //获取父栏目的名字
        $p = D('City', 'Service')->getP($_GET['id']);
        if($p != null) {
            $p_name = $p['name'];
        } else {
            $p_name = '根';
        }
       
        $this->assign('p_name', $p_name);
        $this->assign('city', $city);
       
        $this->display();
    }

    /**
      *修改节点信息
      *param array $data 节点信息
      *@return json
      */
    public function update() {
        $data = $_POST;
        $result = D('City', 'Service')->update($data);
        if(!$result['status']) {
            $this->errorReturn($result['data']['error']);
        }

        $this->successReturn('栏目更新成功');
    }

    /**
      *删除该栏目
      *@return json
      */
    public function delete() {
    	$result = D('City', 'Service')->delete($_GET['id']);
    	if (!$result) {
    		$this->errorReturn('删除出错');
    	}

    	return $this->successReturn('删除该栏目成功', U('City/index'));
    }
}
