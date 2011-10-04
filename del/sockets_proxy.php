<?php

class FacelinkProxyServer
{
    /**
     * List of client sockets
     * @var array
     */
    protected $clients        = array();

    /**
     * The socket clients will connect to
     * @var resource
     */
    protected $listenSocket   = null;

    /**
     * The client socket that connects to the facelink server
     * @var resource
     */
    protected $facelinkSocket = null;

    /**
     * The port we listen on for new connections
     * @var int
     */
    protected $listenPort     = 8000;

    /**
     * The IP address we bind to
     * @var string
     */
    protected $listenHost     = '127.0.0.1';

    /**
     * Are we are shutting down (stop accepting new clients)
     * @var boolean
     */
    protected $shutdown       = false;

    /**
     * Create a new instance of FacelinkProxyServer
     * @param array $facelinkConfig
     * @param string $bindAddress
     * @param int $bindPort
     * @return FacelinkProxyServer
     */
    public function __construct (array $facelinkConfig, $bindAddress = '127.0.0.1', $bindPort = 8000)
    {
        $this->listenPort = $bindPort;
        $this->listenHost = $bindAddress;

        $this->createFacelinkConnection($facelinkConfig);
        $this->createListenSocket();
    }

    /**
     * Connects to the facelink service
     * @param array $facelinkConfig
     * @return void
     */
    protected function createFacelinkConnection(array $facelinkConfig)
    {
        $this->facelinkSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_block($this->facelinkSocket);

        $timeout = 10;
        $time = time();
        while (!@socket_connect($this->facelinkSocket, 
                                $facelinkConfig['host'], 
                                $facelinkConfig['port']))
        {
          if(is_resource($this->facelinkSocket))
            break;

          /**
           * Some sockets first return a error 115, then a error 114 to tell you connecting is in progress
           */
          $errorCode = socket_last_error($this->facelinkSocket);
          if ($errorCode == 115 || $errorCode == 114)
          {
            if ((time() - $time) >= $timeout)
            {
              socket_close($this->facelinkSocket);
              $this->shutdown();
              break;
            }
            usleep(500000);
            continue;
          }
        }
        if(is_resource($this->facelinkSocket))
            $this->facelinkLogin($facelinkConfig['user'], $facelinkConfig['password']);
    }

    /**
     * Logs in to the facelink service
     * @param $user
     * @param $password
     * @return void
     */
    protected function facelinkLogin($user, $password)
    {
        $this->readFacelinkSocket();
        $this->writeFacelinkSocket("001 User Identification = ".$user);
        $this->readFacelinkSocket();
        $this->writeFacelinkSocket("002 User Password = ".$password);
        $result = $this->readFacelinkSocket();

        if(substr($result, 0, 3) == Facelink::CODE_USER_ALREADY_LOGGED_IN)
            $this->shutdown();

    }

    /**
     * Closes the proxy for new clients
     * @return void
     */
    protected function shutdown()
    {
        @socket_close($this->listenSocket);
        $this->shutdown = true;
    }

    /**
     * Closes the connection to the facelink service
     * @return void
     */
    protected function quit()
    {
        @socket_close($this->facelinkSocket);
    }

    /**
     * Starts the listening socket for clients to connect to
     * @return void
     */
    protected function createListenSocket()
    {
        $this->listenSocket = @socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_nonblock($this->listenSocket);
        socket_set_option($this->listenSocket, SOL_SOCKET, SO_REUSEADDR, 1);
        $bindResult = @socket_bind($this->listenSocket, $this->listenHost, $this->listenPort);
        if(!$bindResult)
            $this->shutdown();
        @socket_listen($this->listenSocket);
    }

    /**
     * Handles a request from the client to the proxy
     * @param int $connectionId The connection that sent the command 
     * @return void
     */
    protected function proxyCommand($connectionId)
    {
        $clientConnection = $this->clients[$connectionId];
        $clientMessage = $this->readSocket($connectionId);

        /**
         * 003 is the logout command, swallow this.
         */
        if(substr($clientMessage, 0, 3) === "003")
        {
            $this->closeClientSocket($connectionId);
            $this->removeSocket($connectionId);
        }

        /**
         * Fake the clients login
         */
        elseif(in_array(substr($clientMessage, 0, 3), array('001', '002')))
            $this->writeClientSocket($connectionId, '999 OK', false);

        /**
         * Proxy remote shutdown
         */
        elseif(substr($clientMessage, 0, 3) == 'DIE')
            $this->shutdown();
        elseif($clientMessage)
        {           
            $this->writeFacelinkSocket($clientMessage);
            $this->pipeResponseToClient($connectionId);
        }
    }

    /**
     * Sends the response from the facelink service to the client, don't buffer anything.
     * @param int $connectionId The connection that sent the original command
     * @return void
     */
    protected function pipeResponseToClient($connectionId)
    {
        while(false !== @socket_recv($this->facelinkSocket, $data, 1024, 0))
        {

            if(!is_null($data))
                $this->writeClientSocket($connectionId, $data);
            if(ord(substr($data, -1)) == 26 || ord(substr($data, -1)) == 0 || is_null($data))
            {
                break;
            }
        }
    }

    /**
     * Close the client's connection
     * @param int $connectionId The connection to close
     * @return unknown_type
     */
    protected function closeClientSocket($connectionId)
    {
        $clientConnection = $this->clients[$connectionId];
        $this->writeClientSocket($connectionId, '999 BYE');
    }

    /**
     * Removes a client socket from the pool
     * @param int $connectionId The connection that is to be removed
     * @return void
     */
    protected function removeSocket($connectionId)
    {
        $clientConnection = $this->clients[$connectionId];
        @socket_shutdown($clientConnection, 2);
        @socket_close($clientConnection);
        unset($this->clients[$connectionId]);
    }

    /**
     * Sends a message to a client
     * @param int $connectionId The connection to sent the command to.
     * @param string $message The message to send
     * @param boolean $fromFacelink If we are sending a response from the facelink service
     * @return void
     */
    protected function writeClientSocket($connectionId, $message, $fromFacelink = true)
    {
        if(!is_resource($this->clients[$connectionId]))
        {
            $this->closeClientSocket();
        }
        /**
         * Facelink already attaches the chr(26) to the end of the command, the proxy
         * needs to emulate this behaviour.
         */
        if(!$fromFacelink)
        {
            $message .= "\n".chr(26);
        }
        $connection = $this->clients[$connectionId];
        @socket_write($connection, $message, strlen($message)); 
    }

    /**
     * Write a message from a client to the facelink service
     * @param string $message The message to send
     * @return void
     */
    protected function writeFacelinkSocket($message)
    {
        if(empty($message))
            return;
        $message .= chr(26);
        socket_write($this->facelinkSocket, $message, strlen($message));
    }

    /**
     * Reads information from a client socket
     * @param int $connectionId The connection to read from
     * @return string Information from the client
     */
    protected function readSocket($connectionId)
    {
        $connection = $this->clients[$connectionId];
        $response = @socket_recv($connection, $data, 1024, 0);

        /**
         * False == No Data
         * 0 == Widowed socket / Remote client closed connection
         */
        if($response === false || $response === 0)
        {
            $this->removeSocket($connectionId);
            return '';
        }
        $response = $data;
        if((ord(substr($response, -1)) == 26 || ord(substr($response, -1)) == 0 || is_null($response)) === false)
        {
            while($socketStatus = @socket_recv($connection, $data, 1024, 0))
            {
                /**
                 * false =  no data
                 */
                if($socketStatus === false)
                    break;
                /**
                 * 0 = widowed socket -> remote client closed connection;
                 */
                if($socketStatus === 0)
                {
                    $this->removeSocket($connectionId);
                    break;
                }
                if(!is_null($data))
                    $response .= $data;
                if(ord(substr($data, -1)) == 26 || ord(substr($data, -1)) == 0 || is_null($data))
                    break;
            }
        }
        $response = substr($response, 0, -1);
        return $response;
    }

    /**
     * Reads information from the facelink service
     * @return string Information from the facelink service
     */
    protected function readFacelinkSocket()
    {
        $response = "";
        while(false !== @socket_recv($this->facelinkSocket, $data, 1024, 0))
        {
            if(!is_null($data))
                $response .= $data;
            if(ord(substr($data, -1)) == 26 || ord(substr($data, -1)) == 0 || is_null($data))
            {
                break;
            }
        }
        return $response;
    }

    /**
     * Main loop, starts the proxy
     * @return void
     */
    public function run ()
    {
        /*
         * Client connections' pool
         */
        $this->clients = array();

        while (true) 
        {
            if(!$this->shutdown)
            {
                $conn = @socket_accept($this->listenSocket);
                if (is_resource($conn)) 
                {
                    $conn_id = (integer) $conn;
                    $this->clients[$conn_id] = $conn;
                    $this->writeClientSocket($conn_id, "999 HELLO", false);
                }
            }

            /**
             * If we are shutting down and all the clients have finished their business, close up.
             */
            if($this->shutdown && !count($this->clients))
            {
                $this->quit();
                break;
            }
            /**
             * Create a copy of pool for socket_select()
             */
            $active = $this->clients;

            /**
             * Find active sockets
             */
            socket_select($active, $w = null, $e = null, null);

            /**
             * Handle every active client
             */
            foreach ($active as $conn) 
            {
                $conn_id = (integer) $conn;
                $this->proxyCommand($conn_id);
            }

            /**
             * Sleep (a little), if we do the server starts to lock up
             */
            usleep(5000);
        }
        $this->shutdown();
    }
}

?>
