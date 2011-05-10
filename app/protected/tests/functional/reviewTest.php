<?php

class reviewTest extends WebTestCase
{
	public $fixtures=array(
		'reviews'=>'review',
	);

	public function testShow()
	{
		$this->open('?r=review/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=review/create');
	}

	public function testUpdate()
	{
		$this->open('?r=review/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=review/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=review/index');
	}

	public function testAdmin()
	{
		$this->open('?r=review/admin');
	}
}
