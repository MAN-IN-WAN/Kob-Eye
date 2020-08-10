<?php
/**
 * NB: do not let your IDE fool you. The correct encoding for this file is NOT UTF8.
 */
include_once __DIR__ . '/../lib/xmlrpc.inc';
include_once __DIR__ . '/../lib/xmlrpcs.inc';

include_once __DIR__ . '/parse_args.php';

/**
 * Tests involving parsing of xml and handling of xmlrpc values
 */
class ParsingBugsTests extends PHPUnit_Framework_TestCase
{
    public $args = array();

    protected function setUp()
    {
        $this->args = argParser::getArgs();
        if ($this->args['DEBUG'] == 1)
            ob_start();
    }

    protected function tearDown()
    {
        if ($this->args['DEBUG'] != 1)
            return;
        $out = ob_get_clean();
        $status = $this->getStatus();
        if ($status == PHPUnit_Runner_BaseTestRunner::STATUS_ERROR
            || $status == PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            echo $out;
        }
    }

    protected function newMsg($methodName, $params = array())
    {
        $msg = new xmlrpcmsg($methodName, $params);
        $msg->setDebug($this->args['DEBUG']);
        return $msg;
    }

    public function testMinusOneString()
    {
        $v = new xmlrpcval('-1');
        $u = new xmlrpcval('-1', 'string');
        $t = new xmlrpcval(-1, 'string');
        $this->assertEquals($v->scalarval(), $u->scalarval());
        $this->assertEquals($v->scalarval(), $t->scalarval());
    }

    /**
     * This looks funny, and we might call it a bu