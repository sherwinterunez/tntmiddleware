#!/usr/bin/env python

import time

#import pigpio

pigpio = ctypes.CDLL('libpigpio.so')

pigpio.gpioInitialise()

GPIO_38K=23

wf_38k = []

#                         ON          OFF         MICROS
wf_38k.append(pigpio.pulse(1<<GPIO_38K, 0,          26))
wf_38k.append(pigpio.pulse(0,          1<<GPIO_38K, 26))

pi = pigpio.pi() # connect to local Pi

pigpio.gpioSetMode(GPIO_38K, PI_OUTPUT)

#pi.set_mode(GPIO_38K, pigpio.OUTPUT)

pi.wave_add_generic(wf_38k)

wid = pi.wave_create()

if wid >= 0:
   pi.wave_send_repeat(wid)
   time.sleep(20)
   pi.wave_tx_stop()
   pi.wave_delete(wid)

pi.stop()
