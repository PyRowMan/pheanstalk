<?php
namespace Pheanstalk;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Connection.
 * Relies on a running beanstalkd server.
 *
 * @author  Paul Annesley
 * @package Pheanstalk
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class ConnectionTest extends TestCase
{
    const SERVER_HOST = 'localhost';
    const SERVER_PORT = '5000';
    const SERVER_PASSWORD = 'admin';
    const SERVER_USER = 'admin';
    const CONNECT_TIMEOUT = 2;

    /**
     * @expectedException \Pheanstalk\Exception\ConnectionException
     */
    public function testConnectionFailsToIncorrectPort()
    {
        $connection = new Connection(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT + 1
        );
        $command = new Command\StatsCommand();
        $connection->dispatchCommand($command);
    }

    public function testDispatchCommandSuccessful()
    {
        $connection = $this->_getConnection();
        $command = new Command\StatsCommand();
        $response = $connection->dispatchCommand($command);
        $this->assertInstanceOf('\Pheanstalk\Response', $response);
    }

    public function testPersistentConnection()
    {
        $timeout = null;
        $persistent = true;
        $connection = new Connection(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT,
            $timeout,
            $persistent
        );
        $command = new Command\StatsCommand();
        $response = $connection->dispatchCommand($command);
        $this->assertInstanceOf('\Pheanstalk\Response', $response);
    }

    /**
     * @expectedException \Pheanstalk\Exception\SocketException
     */
    public function testConnectionResetIfSocketExceptionIsThrown()
    {
        $pheanstalk = new Pheanstalk(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT,
            self::CONNECT_TIMEOUT
        );
        $connection = $this->getMockBuilder('\Pheanstalk\Connection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue(self::SERVER_HOST));
        $connection->expects($this->any())
            ->method('getPort')
            ->will($this->returnValue(self::SERVER_PORT));
        $connection->expects($this->any())
            ->method('getConnectTimeout')
            ->will($this->returnValue(self::CONNECT_TIMEOUT));
        $workflow = $pheanstalk->createTask('testconnectionreset', 'testGroup', 'echo "test"');
        $this->assertEquals('testconnectionreset', $workflow->getName());
        $pheanstalk->delete($workflow);
        $pheanstalk->setConnection($connection);
        $connection->expects($this->once())
            ->method('dispatchCommand')
            ->will($this->throwException(new Exception\SocketException('socket error simulated')));
        $workflow = $pheanstalk->workflowExists('testconnectionreset');
    }

    public function testDisconnect()
    {
        $connection = $this->_getConnection();
        // initial connection
        $connection->dispatchCommand(new Command\StatsCommand());
        $this->assertTrue($connection->hasSocket());
        // disconnect
        $connection->disconnect();
        $this->assertFalse($connection->hasSocket());
        // auto-reconnect
        $connection->dispatchCommand(new Command\StatsCommand());
        $this->assertTrue($connection->hasSocket());
    }
    // ----------------------------------------
    // private
    private function _getConnection()
    {
        return new Connection(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT);
    }
}