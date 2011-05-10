<?php

class project_helperTest extends WebTestCase
{
	public $fixtures=array(
		'project_helpers'=>'project_helper',
	);

	public function testShow()
	{
		$this->open('?r=project_helper/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=project_helper/create');
	}

	public function testUpdate()
	{
		$this->open('?r=project_helper/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=project_helper/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=project_helper/index');
	}

	public function testAdmin()
	{
		$this->open('?r=project_helper/admin');
	}
}
