<?php
namespace Home\Service;

/**
 * DataService
 */
class DataService extends CommonService {

    protected function getModelName() {
        return 'Data';
    }

    /**
      *新增团购数据信息
      *@return json
      */
    public function create($data) {
        $Data = $this->getD();

        if (false === ($data = $Data->create($data))) {
            return $this->errorResultReturn($Data->getError());
        }
        $as = $Data->add($data);

        if (false === $as) {
            return $this->errorResultReturn('系统出错了!');
        }

        return $this->resultReturn(true);
    }

    /**
      *更新团购数据信息
      *@return boolean
      */
    public function update($data) {
        $Data = $this->getD();

        if(false === ($data = $Data->create($data))) {
            return $this->errorResultReturn($Data->getError());
        }
        $as = $Data->save($data);

        if(false === $as) {
            return $this->errorResultReturn('系统出错了!');
        }
        
        return $this->resultReturn(true);
    }
}