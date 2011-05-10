<?php

class tasklistTest extends WebTestCase
{
	public $fixtures=array(
		'tasklists'=>'tasklist',
	);

	public function testShow()
	{
		$this->open('?r=tasklist/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=tasklist/create');
	}

	public function testUpdate()
	{
		$this->open('?r=tasklist/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=tasklist/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=tasklist/index');
	}

	public function testAdmin()
	{
		$this->open('?r=tasklist/admin');
	}
}
