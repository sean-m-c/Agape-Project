<?php
/**
 * EAdvancedRelations class file.
 *
 * @link http://www.yiiframework.com/
 * @version 0.1
 */

 /**
  * The EAdvancedArBehavior extension takes away some of the complexities when saving object, for example, with MANY_MANY and BELONGS_TO relations.
  * 
  * 
  * Usage
  *
  * The EAdvancedArBehavior extension adds up some functionality to the default possibilites of yii«s ActiveRecord implementation.
  * To use this extension, just copy this file to your extensions/ directory, add 'import' => 'application.extensions.EAdvancedArBehavior', [...] to your config/main.php and add this behavior to each model you would like to inherit the new possibilities:
  *
  * public function behaviors(){
       * return array( 'EAdvancedArBehavior' => array(
             * 'class' => 'application.extensions.EAdvancedArBehavior'));
  * }
  *
  * 
  *
  * Better support of MANY_TO_MANY relations: 
  *
  * see CAdvancedArBehavior (http://www.yiiframework.com/extension/cadvancedarbehavior/)
  *
  *
  * Better support of BELONGS_TO relations:
  *
  * With this extension you can connect two related object as follows:
  * 
  * $user = new User();
  * $user->company = Company::model()->find($conditions, $params);
  * $post->save();
  * 
  * Without this extension, you would do this as follows:
  *
  * $user = new User();
  * $company = Company::model()->find($conditions, $params) ;
  * $user->company_id = $company->id ;
  * $post->save();
 */

class EAdvancedArbehavior extends CActiveRecordBehavior
{
	public function afterSave($on) 
	{
		$this->writeManyManyTables();
		return TRUE;
	}
	
	public function beforeValidate() { 
		$this->fixBELONGS_TO() ;
	}

	public function fixBELONGS_TO() {
		foreach($this->owner->relations() as $key => $relation)
		{
			if($relation['0'] == CActiveRecord::BELONGS_TO) // ['0'] equals relationType
			{
				Yii::trace('set BELONGS_TO foreign-key field for '.get_class($this->owner),'system.db.ar.CActiveRecord');
				/* $relation[1] -> related table
			     * $relation[2] -> foreignkey field
				 */
				if ( isset($this->owner->{$key}) ) {
					$this->owner->$relation[2] = $this->owner->{$key}->id ;
				}
			}
		}
	}

	/**
	 * At first, this function cycles through each MANY_MANY Relation. Then
	 * it checks if the attribute of the Object instance is an integer, an
	 * array or another ActiveRecord instance. It then builds up the SQL-Query
	 * to add up the needed Data to the MANY_MANY-Table given in the relation
	 * settings.
	 */
	public function writeManyManyTables() 
	{
		Yii::trace('writing MANY_MANY data for '.get_class($this->owner),'system.db.ar.CActiveRecord');

		foreach($this->owner->relations() as $key => $relation)
		{
			if($relation['0'] == CActiveRecord::MANY_MANY) // ['0'] equals relationType
			{
				if(isset($this->owner->$key))
				{
					if(is_object($this->owner->$key) || is_numeric($this->owner->$key))
					{
						$this->executeManyManyEntry($this->makeManyManyDeleteCommand(
							$relation[2],
							$this->owner->{$this->owner->tableSchema->primaryKey}));
						$this->executeManyManyEntry($this->owner->makeManyManyInsertCommand(
							$relation[2],
							(is_object($this->owner->$key))
							? $this->owner->$key->{$this->owner->$key->tableSchema->primaryKey}
							: $this->owner->{$key}));
					}
					else if (is_array($this->owner->$key) && $this->owner->$key != array())
					{
						$this->executeManyManyEntry($this->makeManyManyDeleteCommand(
							$relation[2],
							$this->owner->{$this->owner->tableSchema->primaryKey}));
						foreach($this->owner->$key as $foreignobject)
						{
							$this->executeManyManyEntry ($this->makeManyManyInsertCommand(
								$relation[2],
								(is_object($foreignobject))
								? $foreignobject->{$foreignobject->tableSchema->primaryKey}
								: $foreignobject));
						}
					}
				}
			}
		}
	}

	// We can't throw an Exception when this query fails, because it is possible
	// that there is not row available in the MANY_MANY table, thus execute()
	// returns 0 and the error gets thrown falsely.
	public function executeManyManyEntry($query) {
		Yii::app()->db->createCommand($query)->execute();
	}

	// It is important to use insert IGNORE so SQL doesn't throw an foreign key
	// integrity violation
	public function makeManyManyInsertCommand($model, $rel) {
		return sprintf("insert ignore into %s values ('%s', '%s')", $model,	$this->owner->{$this->owner->tableSchema->primaryKey}, $rel);
	}

	public function makeManyManyDeleteCommand($model, $rel) {
		return sprintf("delete ignore from %s where %s = '%s'", $this->getManyManyTable($model), $this->getRelationNameForDeletion($model), $rel);
	}

	public function getManyManyTable($model) {
		if (($ps=strpos($model, '('))!==FALSE)
		{
			return substr($model, 0, $ps);
		}
		else
			return $model;
	}

	public function getRelationNameForDeletion($model) {
		preg_match('/\((.*),/',$model, $matches) ;
		return substr($matches[0], 1, strlen($matches[0]) - 2);
	}
}
