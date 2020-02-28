<?php
namespace WilokeListingTools\AlterTable;

interface AlterTableInterface{
	public function __construct();

	public function createTable();

	public function deleteTable();

}