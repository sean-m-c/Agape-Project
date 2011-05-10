<?php

class taskTest extends WebTestCase
{
	public $fixtures=array(
		'tasks'=>'task',
	);

	public function testShow()
	{
		$this->open('?r=task/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=task/create');
	}

	public function testUpdate()
	{
		$this->open('?r=task/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=task/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=task/index');
	}

	public function testAdmin()
	{
		$this->open('?r=task/admin');
	}
}
