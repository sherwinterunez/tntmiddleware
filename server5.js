

//const TIMEOUT = 10000;
const TIMEOUT = 100;

const PORT = 8080;
const ADDRESS = '0.0.0.0';

var PHPFPM = require('./node_modules/node-phpfpm');

//var io     = require('./node_modules/socket.io');
var spawn = require('child_process').spawn;
const { exec } = require('child_process');
//var http = require('http');

//var PHPFPM = require('/usr/local/etc/node_modules/node-phpfpm');

//var serialport = require('serialport');

//var SerialPort = serialport.SerialPort;

//var parsers = serialport.parsers;

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
    //documentRoot: '/WEBDEV/sms101.dev/',
    //documentRoot: '/srv/www/sms102.dev/',
    //documentRoot: '/srv/www/tntmobile.dev/',
    //documentRoot: '/WEBDEV/tntattendance.dev/',
    documentRoot: '/srv/www/tntmiddleware.dev/',
});

/*console.log(phpfpm);

phpfpm.run('sms3.php', function(err, output, phpErrors)
{
    if (err == 99) console.error('PHPFPM server error');
    console.log(output);
    if (phpErrors) console.error(phpErrors);
});*/

var runPortCheck = false;

var portCheckRunning = false;

var lastSim = false;

var processCount = 0;

var terminateFlag = false;

var poweroffFlag = false;

var rebootFlag = false;

var pauseFlag = false;

var sims = false;

var rfid = false;

var ctr = 1;

var pacsCtr = 0;

var http = require('http');

var debug = false;

var server = http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/html'});
  //res.end('Hello World\n');
  //console.log(req);
  //console.log(res);

  //console.log('req.method => '+req.method);

  //console.log('req.url => '+req.url);

  /*if(req.url==='/test') {
    //doTest();
    setTimeout(function(){
      doTest(ctr++);
    }, 10);

    res.end('/test.\n');

    return true;
  }*/

  if(req.url==='/restartkiosk') {
    //poweroffFlag = true;
    //runPortCheck = true;
    spawn("killall", ["electron-quick-start"]);
    res.end('restarting kiosk.\n');

    return true;
  } else if(req.url==='/poweroff') {
    poweroffFlag = true;
    runPortCheck = true;
    res.end('powering off.\n');

    return true;
  } else if(req.url==='/reboot') {
    rebootFlag = true;
    runPortCheck = true;
    res.end('rebooting.\n');

    return true;
  } else if(req.url==='/terminate') {
    terminateFlag = true;
    runPortCheck = true;
    res.end('terminating.\n');

    return true;
  } else if(req.url==='/process') {
    res.end('processCount: '+processCount+'\n');
    return true;
  } else if(req.url==='/processcount') {
    res.end(''+processCount+'');
    return true;
  } else if(req.url==='/portcheck') {
    runPortCheck = true;
    pauseFlag = false;
    res.end('checking port.\n');

    return true;
  } else if(req.url==='/pause') {
    pauseFlag = true;
    res.end('paused.\n');

    return true;
  } else if(req.url==='/resume') {
    pauseFlag = false;
    res.end('resumed.\n');

    return true;
  } else if(req.url==='/status') {

    if(portCheckRunning) {
      res.end('scanning');
    } else
    if(pauseFlag) {
      res.end('paused');
    } else {
      res.end('running');
    }

    return true;
  } else if(req.url==='/infrared') {
    res.end('hello sherwin!');
    return true;
  } else {
    var rid = req.url.match(/\/rfidreader\/(.*)\//gi);
    if(rid) {
      var sp = rid[0].split('/');
      console.log('sp',sp);
      console.log('sp[2]',sp[2]);
      //spawn("export", ["DISPLAY=:0.0"]);
      //spawn("xdotool", ["type",sp[2]]);
      //spawn("xdotool", ["key","Return"]);
      exec("export DISPLAY=:0.0 && xdotool type "+sp[2]+" && xdotool key Return", (err, stdout, stderr) => {
        if (err) {
          console.error(`exec error: ${err}`);
          return;
        }
      });
    }
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

    doInit();
});

//var filename = '/var/log/messages';

//var io = io.listen(server);

//io.on('connection', function(client){
  //console.log('Client connected');
//  var tail = spawn("tail", ["-f", filename]);
//  client.send( { filename : filename } );

//  tail.stdout.on("data", function (data) {
    //console.log(data.toString('utf-8'))
//    client.send( { tail : data.toString('utf-8') } )
//  });

//});

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

/*function doCheck() {

  phpfpm.run('sms3.php?q=check', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      console.log(output);

      setTimeout(doCheck, 10);

      if (phpErrors) console.error(phpErrors);
  });

}*/

function doInit() {

  console.log('doInit started.');

  //portCheck();

  //syncToServer();

  //adminSMS();

  //pacsDownload();

  proccessPacs();

  //processQuantum();

}

function proccessPacs() {

  phpfpm.run('processpacs.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      var obj = false;

      try {

        var obj = JSON.parse(output);

        console.log(obj);

      } catch(e) {
        //console.log(e);
      }

      pacsCtr = 0;

      if(obj&&obj.length>0) {
        for(var i in obj) {

         //console.log('sim: '+sims[i]);
          pacsCtr++;
          pacsDownload(obj[i].id,obj[i].conn);
        }
      } else {
        setTimeout(function(){
          proccessPacs();
        }, 5000);
      }

      if (phpErrors) console.error(phpErrors);
  });

}

function pacsDownload(id,conn) {

  var pacsData = false;

  id = id ? id : 0;
  conn = conn ? conn : 0;

  var pacsChild = spawn("/usr/bin/php", [__dirname+"/processpacsimagedownload.php",conn,id]);

  //var pacsChild = spawn("php", [__dirname+"/processpacs.php"]);

  //var pacsChild = spawn("pwd");

  pacsChild.on('exit', function (code, signal) {
    //console.log('child process exited with ' + `code ${code} and signal ${signal}`);

    //setTimeout(function(){
      //pacsDownload();
    //}, TIMEOUT);

    if(pacsCtr==1) {
      pacsCtr--;
      console.log('pacsCtr',pacsCtr);
      if(pacsCtr<1) {
        setTimeout(function(){
          proccessPacs();
        }, 5000);
      }
      return;
    }

    if(pacsData&&pacsData.length>0&&pacsData[0].id&&pacsData[0].conn) {
      console.log(pacsData);

      for(var i in pacsData) {

       setTimeout(function(){
         pacsDownload(pacsData[i].id,pacsData[i].conn);
       }, TIMEOUT);

      }

    } else {
      pacsCtr--;
      console.log('pacsCtr',pacsCtr);
      if(pacsCtr<1) {
        setTimeout(function(){
          proccessPacs();
        }, 5000);
      }
    }
  });

  pacsChild.stdout.on('data', (data) => {
    //console.log(`child stdout:\n${data}`);
    //console.log(`${data}`);

    try {

      var obj = JSON.parse(data);

      if(obj&&obj.length>0&&obj[0].id&&obj[0].conn) {
        pacsData = obj;

        /*console.log(obj);

        for(var i in obj) {

         setTimeout(function(){
           pacsDownload(obj[i].id,obj[i].conn);
         }, TIMEOUT);

       }*/

     }

    } catch(e) {
      //console.log(e);
    }

  });

  pacsChild.stderr.on('data', (data) => {
    console.error(`child stderr:\n${data}`);
    //console.error(`${data}`);
  });

} // function pacsDownload(id,conn) {

function processQuantum(id,conn) {

  var quantumData = false;

  id = id ? id : 0;
  conn = conn ? conn : 0;

  var quantumChild = spawn("/usr/bin/php", [__dirname+"/processquantum.php",conn,id]);

  //var pacsChild = spawn("php", [__dirname+"/processpacs.php"]);

  //var pacsChild = spawn("pwd");

  quantumChild.on('exit', function (code, signal) {
    //console.log('child process exited with ' + `code ${code} and signal ${signal}`);

    setTimeout(function(){
      processQuantum();
    }, TIMEOUT);

  });

  pacsChild.stdout.on('data', (data) => {
    console.log(`child stdout:\n`);
    console.log(`${data}`);
  });

  pacsChild.stderr.on('data', (data) => {
    console.error(`child stderr:\n`);
    console.error(`${data}`);
  });

} // function processQuantum(id,conn) {

function adminSMS() {

  phpfpm.run('adminsms.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      if(output) console.log(output);

      setTimeout(function(){
      //  processCommands(dev,sim,ip);
        //processOutbox(dev,sim);
        adminSMS();
      }, 5000);

      if (phpErrors) console.error(phpErrors);
  });

}

function rfidProcess() {

  phpfpm.run('rfidprocess.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      if(output) console.log(output);

      setTimeout(function(){
      //  processCommands(dev,sim,ip);
        //processOutbox(dev,sim);
        rfidProcess();
      }, TIMEOUT);

      if (phpErrors) console.error(phpErrors);
  });

}

function serverStarted() {

  phpfpm.run('serverstarted.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      if(output) console.log(output);

      if (phpErrors) console.error(phpErrors);
  });
}

function syncToServer() {

  //console.log("synctoserver.php running...");

  //phpfpm.run('retrieve2.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
  phpfpm.run('synctoserver.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      //console.log("synctoserver.php done.");

      if(output) console.log(output);

      //setTimeout(doInit, (60*1000*2));

      //processCount--;

      setTimeout(function(){
      //  processCommands(dev,sim,ip);
        //processOutbox(dev,sim);
        processNotification();
      }, TIMEOUT);

      if (phpErrors) console.error(phpErrors);
  });

}

function processNotification() {
  //console.log("processnotification.php running...");

  //phpfpm.run('retrieve2.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
  phpfpm.run('processnotification.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      //console.log("processnotification.php done.");

      if(output) console.log(output);

      //setTimeout(doInit, (60*1000*2));

      //processCount--;

      setTimeout(function(){
      //  processCommands(dev,sim,ip);
        //processOutbox(dev,sim);
        syncToServer();
      }, TIMEOUT);

      if (phpErrors) console.error(phpErrors);
  });
}

function portCheck() {

  //syncToServer();
  //processNotification();

  if(portCheckRunning) return false;

  if(processCount>0) {
    setTimeout(function(){
      portCheck();
    }, 500);
    return false;
  }

  if(terminateFlag) {
    process.exit(1);
  }

  if(rebootFlag) {
    spawn("reboot");
  }

  if(poweroffFlag) {
    spawn("halt", ["-p"]);
  }

  portCheckRunning = true;

  console.log("portcheck.php running...");

  phpfpm.run('portcheck.php', function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      var obj = false;

      try {

        var obj = JSON.parse(output);

        console.log(obj);

      } catch(e) {
        console.log(e);
      }

      //console.log(output);

      //setTimeout(doInit, (60*1000*2));

      //setTimeout(function(){
      //  retrieveSMS(dev);
      //}, 10000);

      runPortCheck = false;

      portCheckRunning = false;

      if(obj&&obj.devices&&obj.devices.length>0) {

        //var sims = ['/dev/ttyUSB0','/dev/ttyUSB1'];

        //serverStarted();

        sims = obj.devices;

        lastSim = sims[sims.length-1].port;

        for(var i in sims) {

         //console.log('sim: '+sims[i]);

          simInit(sims[i].port,sims[i].sim,sims[i].ip);
        }
      } else if(obj&&obj.rfidreader&&obj.rfidreader.length>0) {

        rfid = obj.rfidreader;

        for(var i in rfid) {
          rfidRead(rfid[i].port,rfid[i].ip);
        }

        rfidProcess();
      } else {
        setTimeout(function(){
          portCheck();
        }, TIMEOUT);
      }

      if (phpErrors) console.error(phpErrors);
  });

}

function rfidRead(dev,ip) {
  //if(debug)
  console.log("rfidreader.php "+dev+" "+ip+" running...");

  //console.log("checksignal.php "+dev+" "+sim+" "+ip+" running...");

  phpfpm.run('rfidreader.php?dev='+dev+'&ip='+ip, function(err, output, phpErrors)
  {
      if (err == 99) console.error('PHPFPM server error');

      //if(debug) console.log("checksignal.php "+dev+" "+sim+" "+ip+" done.");

      //console.log(output);

      //setTimeout(doInit, (60*1000*2));

      //processCount--;

      setTimeout(function(){
        rfidRead(dev,ip);
        //processOutbox(dev,sim);
      }, 1);

      if (phpErrors) console.error(phpErrors);
  });
}

function simInit(dev,sim,ip) {

    processCount++;

    //console.log("siminit.php "+dev+" "+sim+" "+ip+" running...");

    phpfpm.run('siminit.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        //console.log("siminit.php "+dev+" "+sim+" "+ip+" done.");

        //if(output) console.log(output);

        //setTimeout(doInit, (60*1000*2));

        //processCount--;

        if(output.match(/SIM_PAUSED/)) {
          processCount--;
          console.log('SIM PAUSED: '+sim+' '+dev+' '+ip+' '+processCount);
          setTimeout(function(){
            //retrieveSMS(dev,sim,ip);
            simInit(dev,sim,ip);
          }, TIMEOUT);
          return;
        }

        setTimeout(function(){
          //retrieveSMS(dev,sim,ip);
          checkSignal(dev,sim,ip);
        }, TIMEOUT);

        if (phpErrors) console.error(phpErrors);
    });

}

function checkSignal(dev,sim,ip) {

    //processCount++;

    if(debug) console.log("checksignal.php "+dev+" "+sim+" "+ip+" running...");

    //console.log("checksignal.php "+dev+" "+sim+" "+ip+" running...");

    phpfpm.run('checksignal.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        //if(debug) console.log("checksignal.php "+dev+" "+sim+" "+ip+" done.");

        //console.log(output);

        //setTimeout(doInit, (60*1000*2));

        //processCount--;

        setTimeout(function(){
          retrieveSMS(dev,sim,ip);
          //processOutbox(dev,sim);
        }, TIMEOUT);

        if (phpErrors) console.error(phpErrors);
    });

}

function retrieveSMS(dev,sim,ip) {

    //processCount++;

    //console.log("retrieve2.php "+dev+" "+sim+" "+ip+" running...");
    //console.log("retrieve4.php "+dev+" "+sim+" "+ip+" running...");

    //phpfpm.run('retrieve2.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    phpfpm.run('retrieve4.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        //console.log("retrieve4.php "+dev+" "+sim+" "+ip+" done.");

        if(output) console.log(output);

        //setTimeout(doInit, (60*1000*2));

        //processCount--;

        setTimeout(function(){
          processCommands(dev,sim,ip);
          //processOutbox(dev,sim);
        }, TIMEOUT);

        if (phpErrors) console.error(phpErrors);
    });

}

function processCommands(dev,sim,ip) {

    //processCount++;

    //console.log("process4.php "+dev+" "+sim+" "+ip+" running...");

    phpfpm.run('process4.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        //console.log("process4.php "+dev+" "+sim+" "+ip+" done.");

        if(output) console.log(output);

        //setTimeout(doInit, (60*1000*2));

        //processCount--;

        setTimeout(function(){
          processOutbox(dev,sim,ip);
        }, TIMEOUT);

        if (phpErrors) console.error(phpErrors);
    });

}

function processOutbox(dev,sim,ip) {

    //processCount++;

    //console.log("processoutbox2.php "+dev+" "+sim+" "+ip+" running...");

    phpfpm.run('processoutbox2.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        //console.log("processoutbox2.php "+dev+" "+sim+" "+ip+" done.");

        if(output) console.log(output);

        setTimeout(function(){
          checkError(dev,sim,ip)
        }, TIMEOUT);

        if (phpErrors) console.error(phpErrors);
    });

}

function processOutboxXXX(dev,sim,ip) {

    //processCount++;

    console.log("processoutbox2.php "+dev+" "+sim+" "+ip+" running...");

    phpfpm.run('processoutbox2.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        console.log("processoutbox2.php "+dev+" "+sim+" "+ip+" done.");

        console.log(output);

        processCount--;

        if(pauseFlag) {

          if(lastSim===dev) {

            setTimeout(function(){
              //simInit(dev);
              systemPaused();
            }, TIMEOUT);

          }

        } else
        if(runPortCheck) {

          if(lastSim===dev) {

            setTimeout(function(){
              //simInit(dev);
              portCheck();
            }, TIMEOUT);

          }

        } else {
          simInit(dev,sim,ip);
        }

        if (phpErrors) console.error(phpErrors);
    });

}

function checkError(dev,sim,ip) {

    //processCount++;

    //console.log("checkerror.php "+dev+" "+sim+" "+ip+" running...");

    phpfpm.run('checkerror.php?dev='+dev+'&sim='+sim+'&ip='+ip, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        //console.log("checkerror.php "+dev+" "+sim+" "+ip+" done.");

        if(output) console.log(output);

        if(output.match(/STATUS_SIMERROR/)) {
          runPortCheck = true;
        } else
        if(output.match(/STATUS_AT_ERROR/)) {
          console.log("checkerror.php / STATUS_AT_ERROR / "+dev+" "+sim+" "+ip);
          //runPortCheck = true;
        }

        processCount--;

        if(pauseFlag) {

          if(lastSim===dev) {

            setTimeout(function(){
              //simInit(dev);
              systemPaused();
            }, TIMEOUT);

          }

        } else
        if(runPortCheck) {

          if(lastSim===dev) {

            setTimeout(function(){
              //simInit(dev);
              portCheck();
            }, TIMEOUT);

          }

        } else {
          simInit(dev,sim,ip);
        }

        if (phpErrors) console.error(phpErrors);
    });

}

function systemPaused() {

  if(pauseFlag) {

    console.log('System is paused.');

    if(processCount==0&&terminateFlag) {
      process.exit(1);
    }

    if(processCount==0&&rebootFlag) {
      spawn("reboot");
    }

    if(processCount==0&&poweroffFlag) {
      spawn("halt", ["-p"]);
    }

    setTimeout(function(){
      //simInit(dev);
      systemPaused();
    }, TIMEOUT);

  } else {

    if(runPortCheck) {

        setTimeout(function(){
          //simInit(dev);
          portCheck();
        }, TIMEOUT);

    } else
    if(sims.length) {
      lastSim = sims[sims.length-1].port;

      for(var i in sims) {

       //console.log('sim: '+sims[i]);

        simInit(sims[i].port,sims[i].sim,sims[i].ip);
      }
    }

    /*setTimeout(function(){
      //simInit(dev);
      portCheck();
    }, TIMEOUT);  */

  }

}


function doTest(ctr) {

    //ctr++;

    phpfpm.run('test102.php?ctr='+ctr, function(err, output, phpErrors)
    {
        if (err == 99) console.error('PHPFPM server error');

        console.log(output);

        try {
          var ret = JSON.parse(output);

          //console.log(ret);

          console.log('ctr => '+ret.ctr);
          console.log('timer => '+ret.timer);

        } catch(e) {
          console.log(e);
        }

        if (phpErrors) console.error(phpErrors);
    });

}


//doCheck();
