<?php

class involvedTest extends WebTestCase
{
	public $fixtures=array(
		'involveds'=>'involved',
	);

	public function testShow()
	{
		$this->open('?r=involved/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=involved/create');
	}

	public function testUpdate()
	{
		$this->open('?r=involved/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=involved/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=involved/index');
	}

	public function testAdmin()
	{
		$this->open('?r=involved/admin');
	}
}
