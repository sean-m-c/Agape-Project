<?php

class final_reviewTest extends WebTestCase
{
	public $fixtures=array(
		'final_reviews'=>'final_review',
	);

	public function testShow()
	{
		$this->open('?r=final_review/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=final_review/create');
	}

	public function testUpdate()
	{
		$this->open('?r=final_review/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=final_review/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=final_review/index');
	}

	public function testAdmin()
	{
		$this->open('?r=final_review/admin');
	}
}
