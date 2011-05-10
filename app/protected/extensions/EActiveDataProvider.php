<?php

/**
 * EActiveDataProvider
 * An extension on CActiveDataProvider in order to be able to apply scopes
 *
 * Usage:
 * <pre>
 *
 * $dataProvider=new CActiveDataProvider('Post', array(
 * 	 'criteria'=>array(
 * 		 //'condition'=>'status=1 AND tags LIKE :tags',
 * 		 //'params'=>array(':tags'=>$_GET['tags']),
 * 		 'with'=>array('author'),
 * 		 'scope'=>array('published'),
 * 	 ),
 * 		 'pagination'=>array(
 * 		 'pageSize'=>20,
 *		),
 * ));
 * $dataProvider->getData() will return a list of Post objects
 *
 *
 * </pre>
 *
 *
 * @package system.web
 * @author Dragos Protung (dragos@protung.ro)
 * @since 1.1
 *
 */
class EActiveDataProvider extends CActiveDataProvider {

	
	private $_scopesAdded = false;
	
	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		$this->addScopes();
		$criteria=$this->getCriteria();
		if(($pagination=$this->getPagination())!==false)
		{
			$pagination->setItemCount($this->getTotalItemCount());
			$pagination->applyLimit($criteria);
		}
		if(($sort=$this->getSort())!==false)
			$sort->applyOrder($criteria);
		return CActiveRecord::model($this->modelClass)->findAll($criteria);
	}
	
	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount()
	{
		$this->addScopes();
		return CActiveRecord::model($this->modelClass)->count($this->getCriteria());
	}
	
	
	private function addScopes() {
		
		if ($this->_scopesAdded === false) {
			
			$criteria=clone $this->getCriteria();
			
			if (isset($criteria->scope)) {
				foreach ($criteria->scope as $scope){
					CActiveRecord::model($this->modelClass)->{$scope}();
				}
				CActiveRecord::model($this->modelClass)->applyScopes($criteria);
				$this->setCriteria($criteria);
			}
			
			$this->_scopesAdded = true;
		}
	}

}

?>