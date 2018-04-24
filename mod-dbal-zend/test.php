<?php
// [@[#[!SF.DEV-ONLY!]#]@]
// Controller: Zend Dbal Test Sample
// Route: ?/page/dbal-zend.test (?page=dbal-zend.test)
// Author: unix-world.org
// v.3.7.5 r.2018.03.09 / smart.framework.v.3.7

//----------------------------------------------------- PREVENT EXECUTION BEFORE RUNTIME READY
if(!defined('SMART_FRAMEWORK_RUNTIME_READY')) { // this must be defined in the first line of the application
	die('Invalid Runtime Status in PHP Script: '.@basename(__FILE__).' ...');
} //end if
//-----------------------------------------------------

define('SMART_APP_MODULE_AREA', 'SHARED'); // INDEX, ADMIN, SHARED

/**
 * Index Controller
 *
 * @ignore
 *
 */
class SmartAppIndexController extends SmartAbstractAppController {

	public function Run() {

		//-- dissalow run this sample if not test mode enabled
		if(SMART_FRAMEWORK_TEST_MODE !== true) {
			$this->PageViewSetErrorStatus(503, 'ERROR: Test mode is disabled ...');
			return;
		} //end if
		//--
		if(!defined('SMART_FRAMEWORK_TESTUNIT_ALLOW_ZEND_DBAL_TESTS') OR (SMART_FRAMEWORK_TESTUNIT_ALLOW_ZEND_DBAL_TESTS !== true)) {
			$this->PageViewSetErrorStatus(503, 'ERROR: Zend/DBAL Test mode is disabled ...');
			return;
		} //end if
		//--

		//--
		$conf_sqlite = [
			'driver'   => 'pdo_sqlite',
			'database' => 'tmp/zend-test.sqlite'
		];
		//--
		$conf_mysql = [
			'host'     => '127.0.0.1',
			'driver'   => 'pdo_mysql',
			'database' => 'smart_framework',
			'username' => 'root',
			'password' => 'root'
		];
		//--
		$conf_pgsql = [
			'host'     => '127.0.0.1',
			'driver'   => 'pdo_pgsql',
			'database' => 'smart_framework',
			'username' => 'pgsql',
			'password' => 'pgsql'
		];
		//--

		//--
		$db = new \SmartModExtLib\DbalZend\DbalPdo((array)$conf_sqlite);
		//--
		$adapter = $db->getConnection();
		//--

		//--
		$db->write_data('DROP TABLE IF EXISTS sf_zend_dbal_test', 'QUERY_MODE_EXECUTE');
		//--
		$table = new \Zend\Db\Sql\Ddl\CreateTable('sf_zend_dbal_test', true);
		$table->addColumn(new \Zend\Db\Sql\Ddl\Column\Integer('id'));
		$table->addConstraint(new \Zend\Db\Sql\Ddl\Constraint\PrimaryKey('id'));
		$table->addColumn(new \Zend\Db\Sql\Ddl\Column\Varchar('name', 100));
		$table->addColumn(new \Zend\Db\Sql\Ddl\Column\Text('descr'));
		$table->addColumn(new \Zend\Db\Sql\Ddl\Column\Integer('cnt'));
		$sql = new \Zend\Db\Sql\Sql($adapter);
		$adapter->query(
			$sql->getSqlStringForSqlObject($table),
			$adapter::QUERY_MODE_EXECUTE
		);
		//--

		//--
		$db->write_data('DELETE FROM sf_zend_dbal_test');
		//--
		$affected = $db->write_data(
			'INSERT INTO sf_zend_dbal_test (id, name, descr, cnt) VALUES (?, ?, ?, ?)',
			[1, 'Name 1', 'Descr 1', 0]
		);
		if($affected != 1) {
			throw new Exception('Failed to Add Record #1 ('.$affected.')');
			return;
		} //end if
		//--
		$affected = $db->write_data(
			'INSERT INTO sf_zend_dbal_test (id, name, descr, cnt) VALUES (?, ?, ?, ?)',
			[2, 'Name 2', 'Descr 2', 0]
		);
		if($affected != 1) {
			throw new Exception('Failed to Add Record #2');
			return;
		} //end if
		//--
		$affected = $db->write_data(
			'UPDATE sf_zend_dbal_test SET cnt = cnt + 1 WHERE id > ?',
			[0]
		);
		if($affected != 2) {
			throw new Exception('Failed to Update Records');
			return;
		} //end if
		//--

		//--
		$count = $db->count_data('SELECT COUNT(1) FROM sf_zend_dbal_test');
		if($count != 2) {
			throw new Exception('Invalid Records Count');
			return;
		} //end if
		//--

		//--
		$results = $db->read_adata(
			'SELECT * FROM sf_zend_dbal_test WHERE id > ?',
			[0]
		);
		if((Smart::array_size($results) != 2) OR (Smart::array_size($results[0]) != 4) OR (Smart::array_size($results[1]) != 4)) {
			throw new Exception('Invalid Records aRead');
			return;
		} //end if
		//--

		//--
		$results = $db->read_asdata(
			'SELECT * FROM sf_zend_dbal_test WHERE id > ? LIMIT 1 OFFSET 0',
			[1]
		);
		if(Smart::array_size($results) != 4) {
			throw new Exception('Invalid Records asRead');
			return;
		} //end if
		//--

		//--
		$sql = new \Zend\Db\Sql\Sql($adapter);
		$select = $sql->select();
		$select->from('sf_zend_dbal_test');
		$select->where(array('id' => 1));
		$sqlstr = (string) $sql->getSqlStringForSqlObject($select);
		$results = (array) $adapter->query($sqlstr, [])->toArray();
		if((Smart::array_size($results) != 1) OR (Smart::array_size($results[0]) != 4)) {
			throw new Exception('Invalid Records Zend/SQL');
			return;
		} //end if
		//--

		//--
		$this->PageViewSetVars([
			'title' => 'Test: Zend DBAL',
			'main'  => '<h1>Zend DBAL('.Smart::escape_html($db->getDriver()).'): All Tests Done ... OK</h1>'
		]);
		//--

	} //END FUNCTION

} //END CLASS


/**
 * Admin Controller
 *
 * @ignore
 *
 */
class SmartAppAdminController extends SmartAppIndexController {

	// this will clone the SmartAppIndexController to run exactly the same action in admin.php

} //END CLASS


//end of php code
?>