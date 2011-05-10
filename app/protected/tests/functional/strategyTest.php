<?php

class strategyTest extends WebTestCase
{
	public $fixtures=array(
		'strategys'=>'strategy',
	);

	public function testShow()
	{
		$this->open('?r=strategy/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=strategy/create');
	}

	public function testUpdate()
	{
		$this->open('?r=strategy/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=strategy/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=strategy/index');
	}

	public function testAdmin()
	{
		$this->open('?r=strategy/admin');
	}
}
