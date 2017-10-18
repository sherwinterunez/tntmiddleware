<!DOCTYPE html>
<html>
<head>
  <title>tail.js</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script src="http://sms101.remote:8080/socket.io/socket.io.js"></script>

  <style>
    body
      { color: #1a2c37;
        font-family: 'Helvetica', sans-serif; font-size: 86%;
        padding: 2em; }
    #info
      { font-size: 120%;
        font-weight: bold; }
    #tail
      { border: 1px solid #ccc;
        height: 300px;
        padding: 0.5em;
        overflow: hidden;
        position: relative;
        overflow-y: scroll; }
  </style>

</head>
<body>
  <pre id="info"></pre>
  <pre id="tail"></pre>

  <script>

  var Application = function() {
    
   // var socket  = new io.Socket('http://', {port: 8080});
    var socket = io.connect('http://sms101.remote:8080/');

    var lines = 0;

    socket.on('connect', function() {
      console.log('Connected to:', socket.host);
    });
    socket.on('message', function(message) {
      console.log('Received message:', message);
      if (message.filename) {
        $('#info').html( '$ tail -f ' + message.filename );
      };
      if (message.tail) {
        $('#tail').html( $('#tail').html() + message.tail );
        lines++
        //$('#tail').scrollTop(lines*100)
      }
    });
    
    return {
      socket : socket
    };
  };
 
  $(function() { var app = Application(); });

  </script>

</body>
</html>
