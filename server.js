

const PORT = 8080;
const ADDRESS = '0.0.0.0';

var PHPFPM = require('/usr/local/etc/node_modules/node-phpfpm');

var serialport = require('serialport');

var SerialPort = serialport.SerialPort;

var parsers = serialport.parsers;

//var SerialPort = require('serialport').SerialPort;

//var parsers = serialport.parsers;

/*var port = new SerialPort('/dev/cu.usbserial',{
  baudrate: 115200,
  parser: parsers.readline('\r\n'),
});*/

//var PHPFPM = require('./nodejs/node-phpfpm');

var phpfpm = new PHPFPM(
{
    host: '127.0.0.1',
    port: 9000,
    //documentRoot: __dirname + '/',
    documentRoot: '/WEBDEV/sms101.dev/',
});

/*console.log(phpfpm);

phpfpm.run('sms3.php', function(err, output, phpErrors)
{
    if (err == 99) console.error('PHPFPM server error');
    console.log(output);
    if (phpErrors) console.error(phpErrors);
});*/


var http = require('http');

var server = http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/html'});
  //res.end('Hello World\n');
  //console.log(req);
  //console.log(res);

  console.log('req.method => '+req.method);
  console.log('req.url => '+req.url);

  if(req.url==='/start') {

    phpfpm.run('test101.php?q=start', function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');
        
        console.log(output);
        
        if (phpErrors) console.error(phpErrors);
    });

  } else

  if(req.url==='/end') {

    phpfpm.run('test101.php?q=end', function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');
        
        console.log(output);
        
        if (phpErrors) console.error(phpErrors);
    });

  } else

  if(req.url==='/init') {

    phpfpm.run('sms3.php?q=init', function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');
        
        console.log(output);
        
        if (phpErrors) console.error(phpErrors);
    });

  } else

  if(req.url==='/balance') {

    phpfpm.run('sms3.php?q=balance', function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');
        
        console.log(output);
        
        if (phpErrors) console.error(phpErrors);
    });

  } else

  if(req.url==='/check') {

    phpfpm.run('sms3.php?q=check', function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');
        
        console.log(output);
        
        if (phpErrors) console.error(phpErrors);
    });

  }


  res.end('end.\n');

});

/*port.on('open', function () {
  port.write("AT\r\n", function(err, bytesWritten) {
    if (err) {
      return console.log('Error: ', err.message);
    }
    console.log(bytesWritten, 'bytes written');
  });
});

port.on('data', function (data) {
  console.log('Data: ' + data);
});*/

server.listen(PORT, ADDRESS, function () {
    console.log('Server running at http://%s:%d/', ADDRESS, PORT);
    console.log('Press CTRL+C to exit');
});

/*var ctr=0;

setInterval(function(){
  console.log('setInterval: '+ctr);
  ctr++;

  phpfpm.run('sms3.php?q=check', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');
      
      console.log(output);
      
      if (phpErrors) console.error(phpErrors);
  });

},60000);*/

function doCheck() {

  phpfpm.run('sms3.php?q=check', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');
      
      console.log(output);

      setTimeout(doCheck, 10000);
      
      if (phpErrors) console.error(phpErrors);
  });

}

doCheck();

