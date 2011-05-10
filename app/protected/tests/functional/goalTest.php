<?php

class goalTest extends WebTestCase
{
	public $fixtures=array(
		'goals'=>'goal',
	);

	public function testShow()
	{
		$this->open('?r=goal/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=goal/create');
	}

	public function testUpdate()
	{
		$this->open('?r=goal/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=goal/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=goal/index');
	}

	public function testAdmin()
	{
		$this->open('?r=goal/admin');
	}
}
