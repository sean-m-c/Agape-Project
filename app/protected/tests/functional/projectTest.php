<?php

class projectTest extends WebTestCase
{
	public $fixtures=array(
		'projects'=>'project',
	);

	public function testShow()
	{
		$this->open('?r=project/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=project/create');
	}

	public function testUpdate()
	{
		$this->open('?r=project/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=project/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=project/index');
	}

	public function testAdmin()
	{
		$this->open('?r=project/admin');
	}
}
