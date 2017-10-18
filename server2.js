

const PORT = 8080;
const ADDRESS = '0.0.0.0';

var http = require('http');

var server1 = http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/html'});
  //res.end('Hello World\n');
  //console.log(req);
  //console.log(res);

  console.log('req.method => '+req.method);
  console.log('req.url => '+req.url);

  if(req.url==='/start') {

  } else

  if(req.url==='/end') {


  } else

  if(req.url==='/init') {


  } else

  if(req.url==='/balance') {

  } else

  if(req.url==='/check') {

  }


  res.end('end.\n');

});

var server2 = http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/html'});
  //res.end('Hello World\n');
  //console.log(req);
  //console.log(res);

  console.log('req.method => '+req.method);
  console.log('req.url => '+req.url);

  if(req.url==='/start') {


  } else

  if(req.url==='/end') {


  } else

  if(req.url==='/init') {


  } else

  if(req.url==='/balance') {

  } else

  if(req.url==='/check') {

  }


  res.end('end.\n');

});

server1.listen(8080, ADDRESS, function () {
    console.log('Server #1 running at http://%s:%s/', ADDRESS, '8080');
    console.log('Press CTRL+C to exit');
});

server2.listen(8081, ADDRESS, function () {
    console.log('Server #2 running at http://%s:%s/', ADDRESS, '8081');
    console.log('Press CTRL+C to exit');
});
