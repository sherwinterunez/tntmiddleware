import RPi.GPIO as GPIO
import math
import os
import requests
from datetime import datetime
from time import sleep

# This is for revision 1 of the Raspberry Pi, Model B
# This pin is also referred to as GPIO23
INPUT_WIRE = 12

GPIO.setmode(GPIO.BOARD)
GPIO.setup(INPUT_WIRE, GPIO.IN)

ctr = 0

previousVal = 0;

value = GPIO.input(INPUT_WIRE)

#now = datetime.now()
#pulseLength = now - startTime
#startTime = now
#command.append((previousVal, pulseLength.microseconds))

startTime = datetime.now()

print startTime, ctr

ones = 0

while True:

	value = GPIO.input(INPUT_WIRE)

        if value == 1:
                ones = ones + 1
                if ones > 1000:
                        print "connection error!"
			sleep(1)

	if value == previousVal:
		#print value
		continue

	now = datetime.now()

	pulseLength = now - startTime

	print value, previousVal, pulseLength.microseconds

	startTime = now

	if value == 0:
		ones = 0
		print startTime, ctr, pulseLength.microseconds
		r = requests.get('http://127.0.0.1/rfidprocess2.php')
		#r = requests.get('http://127.0.0.1/infrared.php')
		#r = requests.get('http://127.0.0.1:8080/infrared')
		#r.json()
		print r.text

	ctr = ctr+ 1

	previousVal = value

	sleep(0.7);

#
