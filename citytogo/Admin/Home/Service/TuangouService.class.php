<?php
namespace Home\Service;

/**
 * RoleService
 */
class TuangouService extends CommonService {

	    //团购类型
    private $type = array('建材', '家具', '家电', '专场', '联盟');





    protected function getModelName() {
        return 'Tuangou';
    }

    /**
    *返回团购类型
    *@return array
    **/
    public function getType() {
        return $this->type;
    }

    /**
     * 按id得到tuangou数据
     * @param  int     $id
     * @return array
     */
    public function getById($id) {
        $tuangou = $this->getD()->where("id=$id")->relation(true)->find();
        if (empty($tuangou)) {
            return null;
        }

        return $tuangou;
    }

    /**
    *检测团购标题重复性
    *@return json
     */
    public function checkTuangouTitle($title, $id) {
        $Tuangou = $this->getD();
        $tuangou['title'] = trim($title);
        if ($Tuangou->isValidTuangouTitle($tuangou, $id)) {
            return $this->resultReturn(true);
        }

        return $this->errorResultReturn($Tuangou->getError());
    }

    /**
      *新增团购信息
      *@return json
      */
    public function create($data) {
    	$Tuangou = $this->getD();


        $Tuangou->startTrans();
        if (false === ($tuangou = $Tuangou->create($data['tuangou']))) {
            return $this->errorResultReturn($Tuangou->getError());
        }
        $as = $Tuangou->add($tuangou);

        $data['data']['tuangou_id'] = $as;

        $result = D('Data', 'Service')->create($data['data']);
        if (!$result['status']) {
            $Tuangou->rollback();
            return $this->errorResultReturn($result['data']['error']);
        }

        if (false === $as) {
            $Tuangou->rollback();
            return $this->errorResultReturn('系统出错了!');
        }
        $Tuangou->commit();
        return $this->resultReturn(true);
    }

    /**
      *更新团购信息
      *@return json
      */
    public function update($data) {
        $Tuangou = $this->getD();

        $Tuangou->startTrans();
        if(false === ($tuangou = $Tuangou->create($data['tuangou']))) {
            return $this->errorResultReturn($Tuangou->getError());
        }
        
        $ras = D('Data', 'Service')->update($data['data']);
        $as =  $Tuangou->save($tuangou);

        if(false === $as || false === $ras) {
            $Taungou->rollback();
            return $this->errorResultReturn('更新错误!');
        }
        $Tuangou->commit();
        return $this->resultReturn(true);
    }
}