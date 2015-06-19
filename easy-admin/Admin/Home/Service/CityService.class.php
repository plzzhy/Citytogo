<?php
namespace Home\Service;

/**
 * CityService
 */
class CityService extends CommonService {


	protected function getModelName() {
        return 'City';
    }


	 /**
     * 得到带又层级的栏目数据
     * @return array
     */
    public function getCities() {
        $category = new \Org\Util\Category($this->getModelName(),
                                           array('id', 'pid','name'));
        return $category->getList();
    }

    /**
      *通过某个栏目信息
      *@param int $id 节点id
      *@return array
      */
    public function getById($id) {
        $city = $this->getD()->where("id=$id")->find();
        if (empty($city)) {
            return null;
        }

        return $city;
    }

    /**
     *新增子城市
     *@param int $pid 父字节点id, array $data 子节点信息
     *@return array
     */
    public function create($pid, $data) {
        $City = $this->getD();
        
        $data['level'] = $this->getLevel($pid);
        $data['pid'] = intval($pid);
        // $Tuangou->startTrans();
        if (false === ($city = $City->create($data))) {
            return $this->errorResultReturn($City->getError());
        }
        $as = $City->add($city);

        if(false === $as) {
            return $this->errorResultReturn('系统错误!');
        }

        return $this->resultReturn(true);
    }

    /**
      *更新城市
      *@param data 城市的信息
      *@return array
      */
    public function update($data) {
        $City = $this->getD();
        if (false === ($city = $City->create($data))) {
            return $this->errorResultReturn($City->getError());
        }

        $as = $City->save($city);

        if(false === $as) {
            return $this->errorResultReturn('系统错误!');
        }

        return $this->resultReturn(true);
    }

    /**
      *删除栏目
      *@param int $id 要删除的节点id
      *@return json
      */
    public function delete($id) {
       
        $City = $this->getM();

        $result = $City->delete($id);
        if(false === $result) {
            unset($result);
            return false;
        }
        $id = $City->where("pid={$id}")->getField('id', true);
        
        if(null !==$id) {
            foreach ($id as $key => $value) {
                $this->delete($value);
            }
        } 

        return true;
    }


    /**
      *查询节点的等级
      *@param int id 节点的id
      *@return int 该节点的等级
      */
    private function getLevel($id) {
        $level = $this->getD()->where("id=$id")->getField('level');
        if($level === '') {
            return 1;
        }

        return intval($level) + 1;
    }

    /**
      *得到父节点
      *@param int id 子节点的id 
      *@return array 父节点的信息
      */
    public function getP($id) {
      $city = $this->getById($id);
      if($city['pid'] !== null) {
        $p = $this->getById($city['pid']);
        return $p;
      }

      return null;
    }

    /**
      *根据登录信息得到权限
      *@param null
      *@return true 是管理员 or array 权限内的城市
      */
    public function city_priv() {
        if($_SESSION['current_account']['is_super']==1) {
            return 1;
        } else {
            $city = $this->getCityByRoleid($_SESSION['current_account']['role_id']);
        }

        return $city;
    }

    /**
      *根据角色id得到city
      *@param int id 角色id
      *@return array 城市数组
      */
    public function getCityByRoleid($id) {
         $cities_id = M('role_city')->where("role_id=$id")->getField('city_id', true);

         return $cities_id;
    }

}