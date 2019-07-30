<?php
namespace Pheanstalk;

use Pheanstalk\Command\StatsCommand;
use Pheanstalk\Exception\ConnectionException;
use Pheanstalk\Socket\NativeSocket;
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
        $this->assertIsArray($response);
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
        $this->assertIsArray($response);
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
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $workflow = $pheanstalk->createTask('testconnectionreset', 'testGroup', '/bin/echo "test"');
        $this->assertEquals('testconnectionreset', $workflow->getName());
        $pheanstalk->delete($workflow);
        $pheanstalk->setConnection($connection);
        $connection->expects($this->once())
            ->method('dispatchCommand')
            ->will($this->throwException(new Exception\SocketException('socket error simulated')));
        $workflow = $pheanstalk->workflowExists('testconnectionreset');
    }

    public function testConfiguration()
    {
        $pheanstalk = new Pheanstalk(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT,
            self::CONNECT_TIMEOUT
        );
        $this->assertSame($pheanstalk->getConnection()->getPort(), self::SERVER_PORT);
        $this->assertSame($pheanstalk->getConnection()->getHost(), self::SERVER_HOST);
        $this->assertSame($pheanstalk->getConnection()->getConnectTimeout(), self::CONNECT_TIMEOUT);
        $this->assertTrue($pheanstalk->getConnection()->isServiceListening());
        $socket = $this->getMockBuilder(NativeSocket::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pheanstalk->getConnection()->setSocket($socket);
        $this->assertSame($pheanstalk->getConnection()->getSocket(), $socket);
    }

    public function testConnectionWillFail()
    {
        $connection = new Connection(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT + 1,
            self::CONNECT_TIMEOUT
        );
        $this->assertFalse($connection->isServiceListening());
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

    /**
     * @expectedException \Pheanstalk\Exception\ServerUnknownCommandException
     */
    public function testServerServerUnknownCommandException()
    {
        $pheanstalk = new Pheanstalk(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT,
            self::CONNECT_TIMEOUT
        );
        $connection = $this->_getConnection();
        $socket = $this->getMockBuilder(NativeSocket::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->setSocket($socket);
        $socket->expects($this->once())
            ->method('getLine')
            ->will($this->returnValue('<response error="Unknown command or action" error-code="UNKNOWN_COMMAND" node="localhost" status="KO"/>'));
        $pheanstalk->getConnection()->setSocket($socket);
        $command = new StatsCommand();
        $connection->dispatchCommand($command);
    }

    /**
     * @expectedException \Pheanstalk\Exception\ServerException
     */
    public function testBuildQuery()
    {
        $connection = $this->_getConnection();
        $command = $this->getMockBuilder(StatsCommand::class)
            ->disableOriginalConstructor()
            ->getMock();
        $command->expects($this->any())
            ->method('getAction')
            ->will($this->returnValue('action'));
        $command->expects($this->any())
            ->method('getGroup')
            ->will($this->returnValue('group'));
        $command->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(['one' => 'two']));
        $command->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(['one' => 'two']));
        $response = $connection->dispatchCommand($command);
    }

    // ----------------------------------------
    // private
    private function _getConnection()
    {
        return new Connection(
            self::SERVER_HOST,
            self::SERVER_USER,
            self::SERVER_PASSWORD,
            self::SERVER_PORT
        );
    }
}
